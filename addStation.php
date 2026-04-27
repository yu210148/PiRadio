<?php
/*
    PiRadio - Add Station
    Copyright (C) 2014  Kevin Lucas
    GPLv3
*/

require_once 'settings.php';

function sanitize_filename($filename){
    $newfilename = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $filename);
    $command = "mv \"./uploads/$filename\" ./uploads/$newfilename";
    exec($command);
    return $newfilename;
}

function deal_with_logo_file($files){
    $target_path = "uploads/";
    $target_path = $target_path . basename($files['uploadedfile']['name']); 
    if(move_uploaded_file($files['uploadedfile']['tmp_name'], $target_path)) {
        $filename = $files['uploadedfile']['name'];
    } else {
        $filename = 'generic_radio.png';
    }
    $filename = sanitize_filename($filename);
    return $filename;
}

function create_thumbnail($filename){
    $command = "convert uploads/$filename -resize 25x25! uploads/25x25_$filename";
    exec($command);
    return 0;
}

function get_station_id($db, $stationName){
    $stationID = 0;
    $stationName = mysqli_real_escape_string($db, $stationName);
    $sql = "SELECT stations.StationID FROM stations WHERE stations.Name = '$stationName'";
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        $stationID = $row[0];
    }
    return $stationID;
}

function add_format($db, $stationID, $fFormat){
    $fFormat = mysqli_real_escape_string($db, $fFormat);
    $sql = "INSERT INTO format VALUES (NULL, '$stationID', '$fFormat')";
    mysqli_query($db, $sql);
    return 0;
}

function add_station($db, $stationName, $stationUrl, $files, $fFormat){
    $filename = deal_with_logo_file($files);
    create_thumbnail($filename);
    $stationName = mysqli_real_escape_string($db, $stationName);
    $stationUrl = mysqli_real_escape_string($db, $stationUrl);
    $filename = mysqli_real_escape_string($db, $filename);
    $sql = "INSERT INTO stations VALUES ( NULL, '$stationName', '$stationUrl', '$filename', 1 )";
    
    if (mysqli_query($db, $sql)) {
        $stationID = get_station_id($db, $stationName);
        add_format($db, $stationID, $fFormat);
    }
    return 0;
}

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);
if (mysqli_connect_errno()) {
    exit("Connect failed: " . mysqli_connect_error());
}

// Handle Form Submission
if (!empty($_POST["stationUrl"])) {
    add_station($db, $_POST["stationName"], $_POST["stationUrl"], $_FILES, $_POST["fFormat"]);
    // Redirect to self with success parameter to prevent form resubmission dialog
    header("Location: addStation.php?success=1");
    exit;
}

// Check for success message in URL
$message = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "DONE! Station Added.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add an Internet Radio Station</title>
<link href='https://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container">
    <h2>Add an Internet Radio Station</h2>
    
    <div class="admin-actions" style="margin-top: 0; margin-bottom: 30px;">
        <a href="radio.php" class="myGreenButton">Back to Main Menu</a>
    </div>

    <?php if ($message): ?>
        <div class="NowPlaying" style="background:#d4edda; border-color:#c3e6cb;">
            <p style="color:#155724;"><?php echo $message; ?></p>
        </div>
    <?php endif; ?>

    <div class="quick-url-section">
        <form enctype="multipart/form-data" action="addStation.php" method="POST">
            <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
            
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Station Name</label>
                <input class="input-text" type='text' maxlength='100' name='stationName' autofocus required>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Media Stream URL</label>
                <input class="input-text" type='text' maxlength='400' name='stationUrl' placeholder="http://..." required>
                <div style="margin-top: 5px; font-size: 0.85rem; color: var(--secondary-color);">Test with <code>vlc &lt;url&gt;</code> first.</div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:10px; font-weight:bold;">Station Logo Image</label>
                <div class="file-upload-wrapper">
                    <input name="uploadedfile" id="uploadedfile" type="file" class="file-upload-input" onchange="updateFileName()" />
                    <label for="uploadedfile" class="btn myPurpleButton file-upload-label" id="file-label">
                        Choose Image File
                    </label>
                </div>
            </div>

            <div style="margin-bottom:30px; background:#f8f9fa; padding:15px; border-radius:10px;">
                <label style="font-weight:bold; margin-right:20px;">Format:</label>
                <label style="margin-right:15px;"><input type='radio' name='fFormat' value='Talk' checked> Talk</label>
                <label><input type='radio' name='fFormat' value='Music'> Music</label>
            </div>

            <input class="myButton" type="submit" name="Generate" value="Add Station">
        </form>
    </div>
</div>

<script>
function updateFileName() {
    var input = document.getElementById('uploadedfile');
    var label = document.getElementById('file-label');
    if (input.files.length > 0) {
        label.innerHTML = 'Selected: ' + input.files[0].name;
    }
}
</script>
</body>
</html>
<?php mysqli_close($db); ?>
