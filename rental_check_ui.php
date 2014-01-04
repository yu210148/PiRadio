<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Upload Rental List To Check</title>
<!--- <script language="javascript" src="datepicker.js"></script> --->
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<!-- <script language="javascript" src="chainedselects.js"></script> -->
<!-- <script language="javascript" src="bestsellersjsconfig.js"></script> -->
<script language="javascript" src="show_hide_form.js"></script>
<link rel=StyleSheet href="standard.css" type="text/css">
</head>
<body>
<?php
require '/etc/new_standard_functions.php';

function print_form(){
    $divFirstTime = "<div class='hideForm'>";
    $endDivFirstTime = "</div>";
    $hideButton = "<div align=\"right\"><a href =\"#\" id=\"show\">Show </a>/ <a href=\"#\" id=\"hide\">Hide </a>Form</div>";
print <<<HERE
$divFirstTime
<center><h2>Upload Rental List to Check</h2>
<FORM enctype="multipart/form-data" action="rental_check_ui.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="200000" />
<table class='mine' border = '1'>
<tr>
    <th><center>Click to upload a .csv file</center></th>
    <th><center>Select the Season / Year we're dealing with</center></th>
</tr>
<tr>
    <td><center><input name="uploadedfile" type="file" /></center></td>
    <td><center><SELECT name='Season'>
        <OPTION>WINTER 2014</OPTION>
        </SELECT>
        </center></td>
</tr>
</table>
<center><INPUT class="myButton" type="submit" name="Generate" value="Do It"></center>
</form>
$endDivFirstTime
$hideButton
HERE;
return 0;
} // end print form function definition

function clear_upload_dir(){
    array_map('unlink', glob("uploads/*"));
    return 0;
}

function sanitize_filename($filename){
    $newfilename = str_replace(" ", "_", $filename);
    $command = "mv \"./uploads/$filename\" ./uploads/$newfilename";
    exec($command);

    //debug
    //var_dump($filename);
    //var_dump($newfilename);

return $newfilename;
} // end function definition sanitize_file_name()

function call_comparison_script($filename, $season){
    $path = "./uploads/";
    $relativePath = $path . $filename;
    $command = "./rental_check.php $relativePath $season > ./uploads/exceptions.csv";
    //exec($command);
    
    //debug
    //var_dump($command);
    return 0;
}

function print_output(){
print <<<HERE
<hr>
<a href='./uploads/exceptions.csv'>Click here to download results</a>
HERE;
    return 0;
}
// HERE'S MAIN
$season = $_POST["Season"];

    if (empty($_FILES)){
        clear_upload_dir();
        print_form();
    } else {
        clear_upload_dir();
        print_form();
        
        //debug
        //var_dump($_FILES);
        
        $target_path = "uploads/";
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            //echo "The file " . basename( $_FILES['uploadedfile']['name']) . " has been uploaded";
        } else {
            echo "Something's not right the 'Move uploaded file' function has failed.";
        } // end else
        
        $filename = $_FILES['uploadedfile']['name'];
        $filename = sanitize_filename($filename);
        
        //debug
        //var_dump($filename);
        //var_dump($season);
        
        call_comparison_script($filename, $season);
        print_output();
    } // end the grand else
?>