<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add an Internet Radio Station</title>
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
<center><h2>Set Alarm (Not Yet Implemented)</h2>
<form action="setAlarm.php" method="POST">
<table class='mine' border = '1'>
<tr>
    <th><center>Time</center></th>
    <th><center>Date</center></th>
    <th><center>Station</center></th>
</tr>
<tr>
    <td><center><input type="time" name="time" autocomplete="on" value="$now" autofocus required></center></td>
    <td><center><input type="date" name="date" autocomplete="on" min="$today" value="$today" required></center></td>
    <td><center><select class="station" name="station" id="station" required onchange="showValue(this)">
HERE;
while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
    print "<option data-image=\"uploads/25x25_$row[2]\">$row[0]</option>";
} // end while


print <<<HERE
    </select></center></td>
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

function set_alarm($db, $stationName, $date, $time){
    // a function to set an at job to start the radio playing at a specificed time
    // TODO: Implement this with a recurring option that sets a cron job rather than 
    // an at job

    // get station ID
    $sql = "SELECT stations.StationURL FROM stations WHERE stations.Name = '$stationName'";
  
	 $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
      $stationURL = $row[0];
    } // end while

    $command = "at $date $time cvlc $stationUrl";
  
  //debug 
  var_dump($command);
    return 0;
}

// HERE'S MAIN
$time = $_POST["time"];
$date = $_POST["date"];
$stationName = $_POST["station"];
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (empty($time)){
    print_form($db);
} else {
    print_form($db);
    set_alarm($db, $stationName, $date, $time);
    //print "<h3>DONE! Alarm Set.</h3>";
} // end the grand else

mysqli_close($db);
?>
