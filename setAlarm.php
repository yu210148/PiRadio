<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add an Internet Radio Station</title>
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

function print_form(){
print <<<HERE
<center><h2>Set Alarm (Not Yet Implemented)</h2>
<form action="setAlarm.php" method="POST">
<table class='mine' border = '1'>
<tr>
    <th><center>Time</center></th>
    <th><center>Date</center></th>
</tr>
<tr>
    <td><center>placeholder</center></td>
    <td><center>placeholder</center></td>
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


// HERE'S MAIN
$time = $_POST["Time"];
$date = $_POST["Date"];
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

//debug
//var_dump($_FILES);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (empty($time)){
    print_form();
} else {
    print_form();
    //set_alarm($db, $time, $date, $stationID);
    //print "<h3>DONE! Alarm Set.</h3>";
} // end the grand else

mysqli_close($db);
?>
