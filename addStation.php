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

require_once 'settings.php';

function print_form(){
print <<<HERE
<center><h2>Add an Internet Radio Station</h2>
<form enctype="multipart/form-data" action="addStation.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="200000" />
<table class='mine' border = '1'>
<tr>
    <th><center>Station Name</center></th>
    <th><center>Media Stream URL<br><small><small>Test with <i>vlc &#060;url&#062;</i> on a computer first.</small></small></center></th>
    <th><center>Station Logo Image</center></th>
</tr>
<tr>
    <td><center><input type='text' size='25' maxlength='100' name='stationName' autofocus></center></td>
    <td><center><input type='text' size='35' maxlength='400' name='stationUrl'></center></td>
    <td><center><input name="uploadedfile" type="file" /></center></td>
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
    $newfilename = str_replace(" ", "_", $filename);
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
    } else {
        echo "Something's not right the 'Move uploaded file' function has failed.";
    } // end else
    
    $filename = $files['uploadedfile']['name'];
    $filename = sanitize_filename($filename);
    return $filename;
}

function add_station($db, $stationName, $stationUrl, $files){
    // a function to add the entered station info to the database
    $filename = deal_with_logo_file($files);
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