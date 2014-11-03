<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Set Alarm</title>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script src="js/msdropdown/jquery.dd.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/msdropdown/dd.css" />
<link rel=StyleSheet href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
  <script language="javascript">
$(document).ready(function(e) {
try {
$("body select").msDropDown();
} catch(e) {
alert(e.message);
}
});
</script>
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

function print_form($db){
// get the time and date today for use in setting minimum's 
date_default_timezone_set('America/Toronto');
$today = date('Y-m-d');
$now = date('H:i');

// get a list of stations from the databaseName
$sql = "SELECT stations.Name, stations.StationID, stations.FileName FROM stations";
$q = mysqli_query($db, $sql);

print <<<HERE
<center><h2>Set Alarm</h2>
<form action="setAlarm.php" method="POST">
<table class='mine' border = '1'>
<tr>
    <th><center>Time</center></th>
    <th><center>Date</center></th>
    <th><center>Station</center></th>
    <!-- <th><center>Recurring</center></th> -->
</tr>
<tr>
    <td><center><input type="time" name="time" autocomplete="on" value="$now" autofocus required></center></td>
    <td><center><input type="date" name="date" autocomplete="on" min="$today" value="$today" required></center></td>
    <td><select class="station" name="station" id="station" required onchange="showValue(this)">
HERE;
while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
    print "<option data-image=\"uploads/25x25_$row[2]\">$row[0]</option>";
} // end while


print <<<HERE
    </select></td>
    <!-- <td><center><input type="checkbox" name="recurring"> (not yet implemented)</center></td> -->
</tr>
</table>
<center><INPUT class="myButton" type="submit" name="Generate" value="Set Alarm"></center>
</form>
<div align='right'>
<FORM action="radio.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Back to Main Menu">
</FORM>
</div>
HERE;
return 0;
} // end function definition for print_form()

function write_shell_script($command){
    // a function to write a quick shell script 
    // this is needed because php is executing my at
    // command in /bin/sh rather than /bin/bash
    // which doesn't have the <<< construct used in the
    // command
    
    // remove existing file if it exists
    unlink('./uploads/alarm_script.sh');
    
    // open file for writing
    $handle = fopen('./uploads/alarm_script.sh', "w");
    
    // write the hash bang line
    $line = "#!/bin/bash\n";
    fwrite($handle, $line);
    
    // write the command to the file
    $line = "$command";
    fwrite($handle, $line);

    // close the handle
    fclose($handle);
    
    // make the file executable
    $command = "chmod u+x ./uploads/alarm_script.sh";
    $command = escapeshellcmd($command);
    shell_exec($command);
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

function show_set_alarms(){
    // a function to retreive alamrs already set and display them on the screen
    $command = "atq";
    $command = escapeshellcmd($command);
    exec($command, $outputArray);
    
    //debug
    var_dump($output);
    
    if ($output != NULL){
        
    
print <<<HERE
<BR>
The following alarms are set<BR>
$output<BR>
HERE;
} // end if
return 0;
}

function set_alarm($db, $stationName, $date, $time, $user, $pass){
    // a function to set an at job to start the radio playing at a specificed time
    // TODO: Implement this with a recurring option that sets a cron job rather than 
    // an at job

    // get station ID
    $sql = "SELECT stations.StationURL, stations.StationID FROM stations WHERE stations.Name = '$stationName'";
  
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        $stationUrl = $row[0];
        $stationID = $row[1];
    } // end while
    
    $command = "at $time $date <<< '/usr/bin/killall vlc; mysql -u $user -p$pass radio -e \"DELETE FROM NowPlaying\"; mysql -u $user -p$pass radio -e \"INSERT INTO NowPlaying SET NowPlaying.StationID = $stationID\"; /usr/bin/cvlc $stationUrl'";
    write_shell_script($command);
    $command = "./uploads/alarm_script.sh";
    $command = escapeshellcmd($command);
    $output = shell_exec($command);
    return 0;
}

// HERE'S MAIN
$time = $_POST["time"];
$date = $_POST["date"];
$stationName = $_POST["station"];
$recurring = $_POST["recurring"];

//debug
//var_dump($recurring);

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (empty($time)){
    print_form($db);
    show_set_alarms();
} else {
    print_form($db);
    set_alarm($db, $stationName, $date, $time, $user, $pass);
    print "<h3>DONE! Alarm Set.</h3>";
    show_set_alarms();
} // end the grand else

mysqli_close($db);
?>
