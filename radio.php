<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="yes">
<title>Internet Radio Stations</title>
<link href='https://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="./js/radio/radio.js"></script>
<link rel="stylesheet" href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container">
<?php
/*
    PiRadio Plays an assortment of radio stations on a webhost
    Copyright (C) 2014  Kevin Lucas
    GPLv3
*/

require_once 'settings.php';

function get_stations($db){
    $sql="SELECT stations.Name, stations.StationURL, stations.FileName, timeshift.timeshiftID, format.fFormat 
    FROM stations 
    INNER JOIN format on stations.StationID = format.StationID
    LEFT OUTER JOIN timeshift ON stations.StationID = timeshift.StationID 
    WHERE stations.fDisplay = 1
    ORDER BY stations.StationID=39 desc, stations.StationID=37 desc, stations.StationID=36 desc, stations.StationID=2 desc, stations.Name";
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
    }
}

function get_now_playing($db){
    $sql = "SELECT stations.Name, stations.FileName FROM stations INNER JOIN NowPlaying on stations.StationID = NowPlaying.StationID";
    $q = mysqli_query($db, $sql);
    
    $row_count = mysqli_num_rows($q);
    if ($row_count == 0) {
        $areWePlayingTempStream = check_if_now_playing_temp($db);
        if ($areWePlayingTempStream > 0){
            $NowPlayingName = "Quick Stream";
            $NowPlayingFileName = "generic_radio.png";
        }
    } else {
        while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
            $NowPlayingName = $row[0];
            $NowPlayingFileName = $row[1];
        }
    }
    $NowPlayingArray = array();
    $NowPlayingArray[] = $NowPlayingName;
    $NowPlayingArray[] = $NowPlayingFileName;
    return $NowPlayingArray;
}

function get_ip_address(){
    $serverAddress = $_SERVER['SERVER_ADDR'];
    return $serverAddress;
}

function print_form($db){
    $q = get_stations($db);
    $nowPlayingArray = get_now_playing($db);
    $ipaddress = get_ip_address();

    print "<h2>Internet Radio Stations</h2>";

    // Controls Section
    print "<div class='controls-section'>";
    print "<div class='stop-btn-container'>";
    print "<input class='myButton' id='stopButton' type='submit' name='Generate' value='Stop Player' onClick='stop_player()'>";
    print "</div>";
    
    print "<div class='volume-controls'>";
    print "<span class='volume-label'>Volume</span>";
    print "<input class='volumeButton' id='volDown' type='submit' name='Generate' value='-' onClick='lower_volume()'>";
    print "<input class='volumeButton' id='volUp' type='submit' name='Generate' value='+' onClick='raise_volume()'>";
    print "</div>";

    print "<div class='alarm-btn-container'>";
    print "<form action='setAlarm.php' method='POST'>";
    print "<input class='myAlarmButton' type='submit' name='Generate' value='  Set Alarm '>";
    print "</form>";
    print "</div>";
    print "</div>";

    // Now Playing Section
    if (NULL != $nowPlayingArray[0]){
        print "<div class='NowPlaying'>";
        print "<p>Now Playing: <a href='http://$ipaddress:9090/mobile.html' target='_blank'>$nowPlayingArray[0]</a></p>";
        print "<img src='uploads/$nowPlayingArray[1]' alt='Now Playing Logo' width='40' height='40'>";
        print "</div>";
    }

    // Category Toggle
    $row_cnt = mysqli_num_rows($q);
    if (30 <= $row_cnt){
        print "<script language='javascript' src='./js/radio/show_hide_form.js'></script>";
        print "<div align='right' style='margin-bottom:10px'><a href='#' id='talk'>Talk</a><a href='#' id='music'>Music</a></div>";
    }

    // Station Grid
    print "<div class='station-grid'>";
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        print "<div class='station-card $row[4]'>";
        print "<form action='radio.php' method='POST'>";
        print "<input type='hidden' name='stopPlayer' value='Yes'>";
        print "<input type='hidden' name='stationUrl' value='$row[1]'>";
        print "<input type='hidden' name='fTimeshift' value='0'>";
        print "<img src='uploads/$row[2]' alt='Station Logo' width='120' height='120'>";
        print "<h3>$row[0]</h3>";
        print "<input class='myGreenButton' type='submit' name='Generate' value='Play'>";
        print "</form>";
        
        if ($row[3] > 0){
            print "<form action='radio.php' method='POST'>";
            print "<input type='hidden' name='stopPlayer' value='Yes'>";
            print "<input type='hidden' name='stationUrl' value='$row[1]'>";
            print "<input type='hidden' name='fTimeshift' value='1'>";
            print "<input class='myGreenTimeshiftButton' type='submit' name='Generate' value='Play Timeshifted'>";
            print "</form>";
        }
        print "</div>";
    }
    print "</div>";

    // Quick URL Section
    print "<div class='quick-url-section'>";
    print "<form action='radio.php' method='POST'>";
    print "<input type='hidden' name='stopPlayer' value='Yes'>";
    print "<h3>Play Quick URL</h3>";
    print "<input class='input-text' type='text' maxlength='300' name='stationUrl' placeholder='Enter stream URL...'>";
    print "<br><input class='myGreenButton' type='submit' name='Generate' value='Play'>";
    print "</form>";
    print "</div>";

    // Admin Actions
    print "<div class='admin-actions'>";
    print "<form action='addStation.php' method='POST'>";
    print "<input class='myGreenButton' type='submit' name='Generate' value='Add a Station'>";
    print "</form>";
    print "<form action='rmStation.php' method='POST'>";
    print "<input class='rmButton' type='submit' name='Generate' value='Remove a Station'>";
    print "</form>";
    print "</div>";

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
        if ($row[0] > 0) return 'false';
        else return 'true';
    }
    return 'true';
}

function start_player($stationUrl, $db){
    stop_player($db);
    $stationUrl = urldecode($stationUrl);
    $command = "cvlc --clock-jitter=0 --intf http --http-port 9090 --http-password password $stationUrl";
    $command = escapeshellcmd($command);
    exec($command . " > /dev/null &");
    
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

function return_seconds(){
    $unixTimeNow = time();
    $unixTimeFiveThirtyEastern = strtotime(date('Y-m-d') . " 05:30");
    $secondsIntoFileToStart = $unixTimeNow - $unixTimeFiveThirtyEastern;    
    return $secondsIntoFileToStart;
}

function start_player_west_coast($stationUrl, $db, $secondsIntoFile){
    if ($secondsIntoFile < 0) $secondsIntoFile = 0;
    stop_player($db);
    $stationUrl = urldecode($stationUrl);
    $command = "cvlc --intf http --http-port 9090 --http-password password --start-time $secondsIntoFile $stationUrl";
    $command = escapeshellcmd($command);
    exec($command . " > /dev/null &");
    
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
    $command = "git pull";
    exec($command);
    return 0;
}

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

if ($_POST["fUpdate"] == 1) update_piradio();

$stationUrl = urlencode($_POST["stationUrl"]);

if (empty($stationUrl)) {
    print_form($db);
} else {
    if ($_POST["fTimeshift"] == 1){
        $seconds = return_seconds();
        start_player_west_coast($stationUrl, $db, $seconds);
    } else {
        start_player($stationUrl, $db);
    }
    print_form($db);
}

mysqli_close($db);
?>
</div>
</body>
</html>
