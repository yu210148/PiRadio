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
<center><h2>Add an Internet Radio Station</h2>
<form enctype="multipart/form-data" action="addStation.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
<table class='mine' border = '1'>
<tr>
    <th><center>Station Name</center></th>
    <th><center>Media Stream URL<br><small><small>Test with <i>vlc &#060;url&#062;</i> on a computer first.</small></small></center></th>
    <th><center>Station Logo Image</center></th>
    <th><center>Format</center></th>
</tr>
<tr>
    <td><center><input type='text' size='25' maxlength='100' name='stationName' autofocus></center></td>
    <td><center><input type='text' size='35' maxlength='400' name='stationUrl'></center></td>
    <td><center><input name="uploadedfile" type="file" /></center></td>
    <td><input type='radio' name='format' checked>Talk<br><input type='radio' name='format'>Music</td>
</tr>

</table>
<center><INPUT class="myButton" type="submit" name="Generate" value="Add Station"></center>
</form>
<div align='right'>
<FORM action="radio.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Back to Main Menu">
</FORM>
</div>
HERE;
return 0;
} // end function definition for print_form()

function sanitize_filename($filename){
    // a function to get rid of spaces and replace them with underscores in the filename
    $newfilename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $filename);
    $command = "mv \"./uploads/$filename\" ./uploads/$newfilename";
    exec($command);
    return $newfilename;
} // end function definition sanitize_file_name()

function deal_with_logo_file($files){
    // a function to take the file uploaded and move it to the right directory 
    $target_path = "uploads/";
    $target_path = $target_path . basename($files['uploadedfile']['name']); 
    if(move_uploaded_file($files['uploadedfile']['tmp_name'], $target_path)) {
        //echo "The file " . basename( $_FILES['uploadedfile']['name']) . " has been uploaded";
        $filename = $files['uploadedfile']['name'];
    } else {
        echo "Something's not right I didn't get a logo file.  I'll use a generic one.";
        $filename = 'generic_radio.png';
    } // end else
    
    
    $filename = sanitize_filename($filename);
    return $filename;
}

function create_thumbnail($filename){
    // a function to create a 25px x 25px thumbnail of the logo file for display in the setAlarm drop-down
    $command = "convert uploads/$filename -resize 25x25! uploads/25x25_$filename";
    exec($command);
    return 0;
}

function add_station($db, $stationName, $stationUrl, $files){
    // a function to add the entered station info to the database
    $filename = deal_with_logo_file($files);
    create_thumbnail($filename);
    $stationName = mysqli_real_escape_string($db, $stationName);
    $stationUrl = mysqli_real_escape_string($db, $stationUrl);
    $filename = mysqli_real_escape_string($db, $filename);
    $sql = "INSERT INTO stations VALUES ( NULL, '$stationName', '$stationUrl', '$filename' )";
    
    //debug
    //var_dump($sql);
    
    if (mysqli_query($db, $sql)) {
    //    printf("%d Row inserted.\n", mysqli_affected_rows($db));
    } // end if
    return 0;
}

// HERE'S MAIN
$stationName = $_POST["stationName"];
$stationUrl = $_POST["stationUrl"];
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

//debug
//var_dump($_FILES);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (empty($stationUrl)){
    print_form();
} else {
    print_form();
    add_station($db, $stationName, $stationUrl, $_FILES);
    print "<h3>DONE! Station Added.</h3>";
} // end the grand else

mysqli_close($db);
?>
