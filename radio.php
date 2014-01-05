<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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

function get_now_playing($db){
    // a function that get's the Name & Logo File Name of the currently playing stations
    $sql = "SELECT stations.Name, stations.FileName FROM stations INNER JOIN NowPlaying on stations.StationID = NowPlaying.StationID";
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        $NowPlayingName = $row[0];
        $NowPlayingFileName = $row[1];
    } // end while
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
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <INPUT class="myButton" type="submit" name="Generate" value="Stop Player">
        </FORM>
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
</table>
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

print <<<HERE
</table>


<div class='addStationButton'>
<FORM action="addStation.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Add A Station">
</FORM>
</div>

</div> 
HERE;
return 0;
} // end function print_form()

function raise_volume(){
    $command = "amixer set PCM 2dB+";
    exec($command);
    return 0;
}

function lower_volume(){
    $command = "amixer set PCM 2dB-";
    exec($command);
    return 0;
}

function stop_player($db){
    $command = "killall vlc";
    exec($command);
    $sql = "DELETE FROM NowPlaying";
    mysqli_query($db, $sql);
    return 0;
}

function start_player($stationUrl, $db){
    // stop the player in case it's running
    stop_player($db);
    $command = "cvlc $stationUrl";
    exec($command . " > /dev/null &");
    $sql = "INSERT INTO NowPlaying VALUES ((SELECT stations.StationID FROM stations WHERE stations.StationURL = '$stationUrl'))";
    mysqli_query($db, $sql);
    return 0;
}

// HERE'S MAIN
$db = mysqli_connect($dbServer, "klucas", "8clock9", "radio");

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
    
    $stationUrl = $_POST["stationUrl"];

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
