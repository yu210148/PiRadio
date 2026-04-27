<?php
/*
    PiRadio - Remove Station
    Copyright (C) 2014  Kevin Lucas
    GPLv3
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

function delete_station($db, $StationID){
    $StationID = (int)$StationID;
    $sql = "DELETE FROM stations WHERE stations.StationID = $StationID";
    mysqli_query($db, $sql);
    $sql = "DELETE FROM format WHERE StationID = $StationID";
    mysqli_query($db, $sql);
    return 0;
}

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

if (!empty($_POST["StationID"])){
    delete_station($db, $_POST["StationID"]);
    header("Location: rmStation.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Remove an Internet Radio Station</title>
<link href='https://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
<div class="container">
    <h2>Remove an Internet Radio Station</h2>

    <div class="admin-actions" style="margin-top: 0; margin-bottom: 30px;">
        <a href="radio.php" class="myGreenButton">Back to Main Menu</a>
    </div>

    <div class="station-grid">
        <?php
        $q = get_stations($db);
        while ($row = mysqli_fetch_array($q, MYSQLI_NUM)):
        ?>
            <div class="station-card">
                <form action="rmStation.php" method="POST" onsubmit="return confirm('Are you sure you want to delete <?php echo addslashes($row[0]); ?>?');">
                    <input type="hidden" name="StationID" value="<?php echo $row[3]; ?>">
                    <img src="uploads/<?php echo $row[2]; ?>" alt="Station Logo" width="120" height="120">
                    <h3><?php echo $row[0]; ?></h3>
                    <input class="myButton" type="submit" name="Generate" value="Delete Station">
                </form>
            </div>
        <?php endwhile; ?>
        <?php mysqli_free_result($q); ?>
    </div>
</div>
</body>
</html>
<?php mysqli_close($db); ?>
