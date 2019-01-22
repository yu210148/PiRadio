<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html">
<meta name="mobile-web-app-capable" content="yes">
<title>Internet Radio Stations</title>
<link href='https://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="./js/radio/radio.js"></script>
<script language="javascript" src="./js/radio/show_hide_form.js"></script>
<link rel=StyleSheet href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<?php
/*
    PiRadio Plays an assortment of radio stations on a webhost (I've got a
    Raspberry Pi on my bookshelf with a pair of speakers plugged into it.)
    
    Copyright (C) 2014  Kevin Lucas

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'settings.php';

function get_stations($db){
    $sql="SELECT stations.Name, stations.StationURL, stations.FileName, timeshift.timeshiftID, format.fFormat 
    FROM stations 
    INNER JOIN format on stations.StationID = format.StationID
    LEFT OUTER JOIN timeshift ON stations.StationID = timeshift.StationID 
    ORDER BY stations.StationID=3 desc, stations.StationID=1 desc, stations.StationID=4 desc, stations.Name";
    $q = mysqli_query($db, $sql);
    return $q;
}

function check_if_now_playing_temp($db){
    $sql = "SELECT NowPlaying.StationID FROM NowPlaying";
    $q = mysqli_query($db, $sql);
    $row_count = mysqli_num_rows($q);
    if (0 == $row_count){
        return 0;
    } else {
        return 1;
    } // end else
}// end function definition

function get_now_playing($db){
    // a function that get's the Name & Logo File Name of the currently playing stations
    $sql = "SELECT stations.Name, stations.FileName FROM stations INNER JOIN NowPlaying on stations.StationID = NowPlaying.StationID";
    $q = mysqli_query($db, $sql);
    
    $row_count = mysqli_num_rows($q);
    if ($row_count == 0) {
        // we're either not playing or playing a temp stream
        $areWePlayingTempStream = check_if_now_playing_temp($db);
        if ($areWePlayingTempStream > 0){
            // we're playing a temp stream
            $NowPlayingName = "Quick Stream";
            $NowPlayingFileName = "generic_radio.png";
        } else {
            // we're not playing anything
        } // end else
    } else {
        while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
            $NowPlayingName = $row[0];
            $NowPlayingFileName = $row[1];
        } // end while
    } // end else
    $NowPlayingArray = array();
    $NowPlayingArray[] = $NowPlayingName;
    $NowPlayingArray[] = $NowPlayingFileName;
    return $NowPlayingArray;
} // end function definition for get_now_playing()

function get_ip_address(){
    // a function that get's the player's IP address on the LAN
    $serverAddress = $_SERVER['SERVER_ADDR'];
    return $serverAddress;
}

function print_form($db){
        $divFirstTime = "<div class='hideForm'>";
        $endDivFirstTime = "</div>";

        $hideButton = "<div align=\"right\"><a href =\"#\" id=\"talk\">Talk</a><a href =\"#\" id=\"music\">Music</a></div>";

// database stuff here
$q = get_stations($db);

$nowPlayingArray = get_now_playing($db);

//debug
//var_dump($nowPlayingArray);

print <<<HERE

<div class='grandparent'>
<h2>Internet Radio Stations</h2>

<table border=0 width=100%>
<tr>
    <td><center>
        <div class='stopButton'>
        <!--- <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes"> --->
        <INPUT class="myButton" id="stopButton" type="submit" name="Generate" value="Stop Player" onClick="stop_player()">
        <!--- </FORM> --->
        </div>
    </center></td>
    <td><center>
        <table border=0>
        <tr>
            <th colspan=2>
                Volume
            </th>
        </tr>
        <tr>
            <td><center>
                <INPUT class="volumeButton" id="volDown" type="submit" name="Generate" value="-" onClick="lower_volume()">
            </center></td>
            <td><center>
                <INPUT class="volumeButton" id="volUp" type="submit" name="Generate" value="+" onClick="raise_volume()">
            </center></td>
        </tr>
        </table>
    </center></td>
</tr>
<tr><td><center>
<div class='setAlarmButton'>
<FORM action="setAlarm.php" method="POST">
<INPUT class="myAlarmButton" type="submit" name="Generate" value="  Set Alarm ">
</FORM>
</div>
</center></td><td></td></tr>
</table>

<!--
<div class='setAlarmButton'>
<FORM action="setAlarm.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Set Alarm">
</FORM>
</div>
-->

HERE;

if (NULL == $nowPlayingArray[0]){
    // do nothing
} else {
    // print a centered table showing what's currently playing
    // TODO: add in a onClick pop-out remote control pointed at 127.0.0.1:9090 (VLC Controls)
    // http://127.0.0.1:9090/mobile.html may be helpful :)
    
    $ipaddress = get_ip_address();
    
    print <<<HERE
<center>
<div class='NowPlaying'>
<table border=0>
    <tr>
        <td><center><p>Now Playing:</p></center></td>
        <td><center><p><a href="http://$ipaddress:9090/mobile.html" target="_blank">$nowPlayingArray[0]</p></center></td>
        <td><center><img src="uploads/$nowPlayingArray[1]" alt="Now Playing Logo" width="25" height="25"></center></td>
    </tr>
</table>
</div>
</a>    
</center>
HERE;
}

// if number of rows > 30 show talk/music button
$row_cnt = mysqli_num_rows($q);

//debug
var_dump($row_cnt);

if (25 <= $row_cnt){
  print "$hideButton";
} // end if

print <<<HERE

<table class='mine' border = '1'>

HERE;

while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
    // debug
    //var_dump($row);
    print <<<HERE
    <tr class='$row[4]'>
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <input type="hidden" name="stationUrl" value="$row[1]">
        <input type="hidden" name="fTimeshift" value="0">
        <td><center><img src="uploads/$row[2]" alt="Station Logo" width="100" height="100"></center></td>
        <td><center><h3><center>$row[0]</center></h3></td>
        <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center>
HERE;
    // if there's a value for the timeshiftID then show a play timeshifted button
    if ($row[3] > 0){
print <<<HERE
</FORM><FORM action="radio.php" method="POST">
<input type="hidden" name="stopPlayer" value="Yes">
<input type="hidden" name="stationUrl" value="$row[1]">
<input type="hidden" name="fTimeshift" value="1">
<br><center><INPUT class="myGreenTimeshiftButton" type="submit" name="Generate" value="Play Timeshifted">
HERE;
    } // end if
    
    print <<<HERE
        </center></td>
        </FORM>
    </tr>
HERE;
} // end while

mysqli_free_result($q);

// add field for temp station
print <<<HERE
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <td colspan=2><center><h3>Play Quick URL</h3>
    <input class="text" type="text" length=100 maxlength=300 name="stationUrl"></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
HERE;

print <<<HERE
</table>



<div class='addStationButton'>
<FORM action="addStation.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Add a Station">
</FORM>
<FORM action="rmStation.php" method="POST">
<INPUT class="rmButton" type="submit" name="Generate" value="Remove a Station">
</FORM>
</div>
<div class='updateButton'>
<FORM action="radio.php" method="POST">
<INPUT type="hidden" value="1" name="fUpdate">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Update PiRadio">
</FORM>
</div>
</div>

HERE;
return 0;
} // end function print_form()

function stop_player($db){
    $command = "killall vlc";
    $command = escapeshellcmd($command);
    exec($command);
    $sql = "DELETE FROM NowPlaying";
    mysqli_query($db, $sql);
    return 0;
}

function check_if_temp_stream($db, $stationUrl){
    $stationUrl = urldecode($stationUrl);
    $sql = "SELECT stations.StationID FROM stations WHERE stations.StationURL = '$stationUrl'";
    
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        if ($row[0] > 0){
            // stream exists in db
            return 'false';
        } else {
            // it is a temp stream
            return 'true';
        } // end else
    } // end while
} // end function definition

function start_player($stationUrl, $db){
    // stop the player in case it's running
    stop_player($db);
    $stationUrl = urldecode($stationUrl);
    $command = "cvlc --intf http --http-port 9090 --http-password password $stationUrl";
    $command = escapeshellcmd($command);
    exec($command . " > /dev/null &");
    
    // check if it's a temp stream and if so write station id 0 to now playing
    $isTempStream = check_if_temp_stream($db, $stationUrl);

    if ('false' == $isTempStream){ 
        $sql = "INSERT INTO NowPlaying VALUES ((SELECT stations.StationID FROM stations WHERE stations.StationURL = '$stationUrl'))";
        mysqli_query($db, $sql);
    } else {
        $sql = "INSERT INTO NowPlaying Values (0)";
        mysqli_query($db, $sql);
    }
    return 0;
}

function get_timezone_offset($remote_tz, $origin_tz = null) {
    // the following function was stolen from http://php.net/manual/en/function.timezone-offset-get.php
    // credit to [d][a][n][at][authenticdesign][.][net]
    
    /**    Returns the offset from the origin timezone to the remote timezone, in seconds.
    *    @param $remote_tz;
    *    @param $origin_tz; If null the servers current timezone is used as the origin.
    *    @return int;
    */
    if($origin_tz === null) {
        if(!is_string($origin_tz = date_default_timezone_get())) {
            return false; // A UTC timestamp was returned -- bail out!
        }
    }
    $origin_dtz = new DateTimeZone($origin_tz);
    $remote_dtz = new DateTimeZone($remote_tz);
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
    return $offset;
}

function return_seconds(){
    // a helper function to take the system time 'now', subtract 3 hours, then
    // return the number of seconds between 5:30am eastern and 'now' for passing
    // in to the start_player_west_coast() function
    // NOTE: the use case for this is time shifting the Toronto morning
    // radio show for the west coast of North America so although this should
    // work for any time zone where the file that you want to play was recorded 
    // server side already (I've got a cron job that records the show to a webserver
    // on my lan) the variable names suggest the 3 hour difference between Eastern
    // and Pacific time.

    //$systemTimeZone = date_default_timezone_get();
    //$timezoneOffsetSeconds = get_timezone_offset('America/New_York', $systemTimeZone);
    
    // the the unix time 'now' using the system's time zone
    $unixTimeNow = time();
    //$unixTimeThreeHoursAgo = strtotime($timezoneOffsetSeconds . ' seconds', $unixTimeNow); 
    $unixTimeFiveThirtyEastern = strtotime(date('Y-m-d') . " 05:30");
    
    // we're getting up a bit later than we used to so I'm going to start this an hour earlier so we get more of the show --KL
    //$unixTimeFiveThirtyEastern = strtotime(date('Y-m-d') . " 06:30");
    
    //$unixTimeFiveThirtyEastern = $unixTimeFiveThirtyEastern - $timezoneOffsetSeconds;
    //$fiveThirty = date('Y-m-d H:i:s', $unixTimeFiveThirtyEastern);
    
    // seconds between now -3 hours and 5:30am
    //$secondsIntoFileToStart = $unixTimeFiveThirtyEastern - $unixTimeThreeHoursAgo;
    //return $secondsIntoFileToStart;
    
    // I seem to be making this more complicated than it needs to be.
    // Take the unix time now and subtract the unix time at 5:30am from it
    // to get the number of seconds in to the file to start
    $secondsIntoFileToStart = $unixTimeNow - $unixTimeFiveThirtyEastern;    
    return $secondsIntoFileToStart;
}

function start_player_west_coast($stationUrl, $db, $secondsIntoFile){
    // a function to start playing a station on a 3 hour delay 
    // note this only works when a recording of the station exists somewhere
    // it doesn't do the recording itself. --KL 2015-03-22
    
    // sanity check: if the secondsIntoFile value is negative we cannot proceed
    if ($secondsIntoFile < 0){
        // start playing at the beginning
        print "<br>I can't start playing $secondsIntoFile into the program.  I'll start at the beginning.<br>";
        $secondsIntoFile = 0;
    } // end if
    
    // stop the player in case it's running
    stop_player($db);
    $stationUrl = urldecode($stationUrl);
    // starting vlc with a web interface to control it from
    $command = "cvlc --intf http --http-port 9090 --http-password password --start-time $secondsIntoFile $stationUrl";
    $command = escapeshellcmd($command);
    exec($command . " > /dev/null &");
    
    // check if it's a temp stream and if so write station id 0 to now playing
    $isTempStream = check_if_temp_stream($db, $stationUrl);

    if ('false' == $isTempStream){ 
        $sql = "INSERT INTO NowPlaying VALUES ((SELECT stations.StationID FROM stations WHERE stations.StationURL = '$stationUrl'))";
        mysqli_query($db, $sql);
    } else {
        $sql = "INSERT INTO NowPlaying Values (0)";
        mysqli_query($db, $sql);
    }
    return 0;
}

function update_piradio(){
    // a function to call git pull and update pi-radio
    // to whatever the current state is on github
    // NOTE: this requires the permissions to be set
    // on the files and directories such that
    // the webserver user can do this.
    $command = "git pull";
    exec($command);
    return 0;
}

// HERE'S MAIN
//TODO; figure out how to get stop player button to update the screen when clicked to remove the now playing station
// now that it's an ajax call and doesn't refresh the page

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

// are we updating the software
if ($_POST["fUpdate"] == 1){
    // update
    update_piradio();
} // end if

// if (NULL == $_POST["stopPlayer"]){
//     $stopPlayer = "No";
// } else {
//     $stopPlayer = $_POST["stopPlayer"];
// } // end else

$stationUrl = urlencode($_POST["stationUrl"]);

//debug
//var_dump($_REQUEST["stationUrl"]);

if (empty($stationUrl)) {
//     if ('Yes' == $stopPlayer){
//         stop_player($db);
//     } // end if
    print_form($db);
} else {
//     if ('Yes' == $stopPlayer){
//         stop_player($db);
//     } // end if
    // check if timeshifted
    
    //bugfix
    //var_dump($_POST["fTimeshift"]);
    
    if ($_POST["fTimeshift"] == 1){
        $seconds = return_seconds();
        start_player_west_coast($stationUrl, $db, $seconds);
    } else {
        start_player($stationUrl, $db);
    }
    print_form($db);
} // end else

mysqli_close($db);
?>
