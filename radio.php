<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html">
<meta name="mobile-web-app-capable" content="yes">
<title>Internet Radio Stations</title>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
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
    $sql="SELECT
        stations.Name,
        stations.StationURL,
        stations.FileName
        FROM
        stations
        ORDER BY
        stations.StationID";
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

function print_form($db){
//        $divFirstTime = "<div class='hideForm'>";
//        $endDivFirstTime = "</div>";

//        $hideButton = "<div align=\"right\"><a href =\"#\" id=\"show\">Show </a>/ <a href=\"#\" id=\"hide\">Hide </a>Form</div>";

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
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <INPUT class="myButton" type="submit" name="Generate" value="Stop Player">
        </FORM>
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
                <FORM action="radio.php" method="POST">
                <input type="hidden" name="volume" value="down">
                <input type="hidden" name="stopPlayer" value="No">
                <INPUT class="volumeButton" type="submit" name="Generate" value="-">
                </FORM>
            </center></td>
            <td><center>
                <FORM action="radio.php" method="POST">
                <input type="hidden" name="volume" value="up">
                <input type="hidden" name="stopPlayer" value="No">
                <INPUT class="volumeButton" type="submit" name="Generate" value="+">
                </FORM>
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
    print <<<HERE
<center>
<table border=0>
    <tr>
        <td><center><p>Now Playing:</p></center></td>
        <td><center><p>$nowPlayingArray[0]</p></center></td>
        <td><center><img src="uploads/$nowPlayingArray[1]" alt="Now Playing Logo" width="25" height="25"></center></td>
    </tr>
    </table>
</center>
HERE;
}

print <<<HERE

<table class='mine' border = '1'>

HERE;

while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
    // debug
    //var_dump($row);
    print <<<HERE
    <tr>
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <input type="hidden" name="stationUrl" value="$row[1]">
        <td><center><img src="uploads/$row[2]" alt="Station Logo" width="100" height="100"></center></td>
        <td><center><h3><center>$row[0]</center></h3></td>
        <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
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

function raise_volume(){
    $command = "amixer set PCM 2dB+";
    $command = escapeshellcmd($command);
    exec($command);
    return 0;
}

function lower_volume(){
    $command = "amixer set PCM 2dB-";
    $command = escapeshellcmd($command);
    exec($command);
    return 0;
}

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
    $command = "cvlc $stationUrl";
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
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

// are we updating the software
if ($_POST["fUpdate"] == 1){
    // update
    update_piradio();
} // end if

// are we adjusting the volume?
$volumeAdjust = $_POST["volume"];
if ("down" == $volumeAdjust){
    lower_volume();
    print_form($db);
} else if ("up" == $volumeAdjust){
    raise_volume();
    print_form($db);
} else {
    // we're not adjusting the volume so move on
    if (NULL == $_POST["stopPlayer"]){
        $stopPlayer = "No";
    } else {
        $stopPlayer = $_POST["stopPlayer"];
    } // end else
    
    $stationUrl = urlencode($_POST["stationUrl"]);
    
    //debug
    //var_dump($_REQUEST["stationUrl"]);
    
    if (empty($stationUrl)) {
        if ('Yes' == $stopPlayer){
            stop_player($db);
        } // end if
        print_form($db);
    } else {
        if ('Yes' == $stopPlayer){
            stop_player($db);
        } // end if
        start_player($stationUrl, $db);
        print_form($db);
    } // end else
}

mysqli_close($db);
?>
