<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Remove an Internet Radio Station</title>
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
        stations.FileName,
        stations.StationID
        FROM
        stations
        ORDER BY
        stations.StationID";
    $q = mysqli_query($db, $sql);
    return $q;
}

function print_form($db){
    $q = get_stations($db);
print <<<HERE
    <center><h2>Remove an Internet Radio Station</h2>
    <table class='mine' border = '1'>
HERE;
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        print <<<HERE
        <tr>
            <FORM action="rmStation.php" method="POST">
            <input type="hidden" name="StationID" value="$row[3]">
            <td><center><img src="uploads/$row[2]" alt="Station Logo" width="100" height="100"></center></td>
            <td><center><h3><center>$row[0]</center></h3></td>
            <td><center><INPUT class="myButton" type="submit" name="Generate" value="Delete Station"></center></td>
            </FORM>
        </tr>
HERE;
    } // end while
    mysqli_free_result($q);
print <<<HERE
    </table>
    <div align='right'>
    <FORM action="radio.php" method="POST">
    <INPUT class="myGreenButton" type="submit" name="Generate" value="Back to Main Menu">
    </FORM>
    </div>
HERE;
    return 0;
} // end print_form() function definition

function delete_station($db, $StationID){
    $sql = "DELETE FROM stations WHERE stations.StationID = $StationID";
    mysqli_query($db, $sql);
    return 0;
} // end delete_station() functino definition

// HERE'S MAIN
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);
$StationID = $_POST["StationID"];

if (NULL == $StationID){
    print_form($db);
} else {
    delete_station($db, $StationID);
    print_form($db);
} // end else
mysqli_close($db);