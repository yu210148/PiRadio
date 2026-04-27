<?php
/*
    PiRadio - Set Alarm
    Copyright (C) 2014  Kevin Lucas
    GPLv3
*/

require_once 'settings.php';

function write_shell_script($command, $date, $time, $fRecurring){
    if (1 == $fRecurring){
        $filePath = "/tmp/alarm_script-" . $date . "_" . $time . ".sh";
    } else {
        $filePath = "/tmp/alarm_script.sh";
    }
    if (file_exists($filePath)) unlink("$filePath");
    $handle = fopen("$filePath", "w");
    fwrite($handle, "#!/bin/bash\n");
    fwrite($handle, "$command");
    fclose($handle);
    shell_exec("chmod u+x $filePath");
    return 0;
}

function remove_old_shell_script($date, $time){
    $fileName = "/tmp/alarm_script-" . $date . "_" . $time . ".sh";
    if (file_exists($fileName)) unlink($fileName);
    return 0;
}

function cancel_alarm($db, $AlarmID){
    $AlarmID = (int)$AlarmID;
    $fRecurring = 0;
    $sql = "SELECT alarms.fRecurring, alarms.Date, alarms.Time FROM alarms WHERE alarms.AlarmID = $AlarmID";
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        $fRecurring = $row[0];
        $date = $row[1];
        $time = $row[2];
    }
    
    if (0 == $fRecurring){    
        exec("atq", $atqOutputArray);
        $unixTimestamp = strtotime("$date $time");
        $datetimeAtFormat = date('D M j H:i:s Y', $unixTimestamp);
        $atJobNumber = NULL;
        foreach ($atqOutputArray as $line){
            $lineArray = preg_split('/\s+/', $line);
            $datetimeFromAt = $lineArray[1] . " " . $lineArray[2] . " " . $lineArray[3] . " " . $lineArray[4] . " " . $lineArray[5];
            if ($datetimeAtFormat == $datetimeFromAt) $atJobNumber = $lineArray[0];
        }
        if ($atJobNumber) exec("atrm $atJobNumber");
    } else {
        exec('crontab -l', $output);
        $minute = substr($time, 3, 2);
        $hour = substr($time, 0, 2);
        $dayOfMonth = substr($date, -2);
        $month = substr($date, 5, 2);
        
        $newCrontab = array();
        foreach ($output as $cronJob){
            $lineArray = explode(" ", $cronJob);
            if (!($minute == $lineArray[0] && $hour == $lineArray[1] && $dayOfMonth == $lineArray[2] && $month == $lineArray[3])) {
                $newCrontab[] = $cronJob;
            }
        }
        unlink('/tmp/tmp-crontab.txt');
        $handle = fopen('/tmp/tmp-crontab.txt', 'w');
        foreach ($newCrontab as $line) if (!empty($line)) fwrite($handle, $line . "\n");
        fwrite($handle, "\n");
        fclose($handle);
        exec("crontab -r");
        shell_exec("crontab /tmp/tmp-crontab.txt");
        remove_old_shell_script($date, substr($time, 0, 5));
    }
    mysqli_query($db, "DELETE FROM alarms WHERE alarms.AlarmID = $AlarmID");
    return 0;
}

function write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring){
    mysqli_query($db, "INSERT INTO alarms VALUES (NULL, '$stationID', '$date', '$time', '$fRecurring')");
}

function write_crontab_file($date, $time){
    unlink('/tmp/tmp-crontab.txt');
    $handle = fopen('/tmp/tmp-crontab.txt', "w");
    exec('crontab -l', $output);
    foreach ($output as $l) fwrite($handle, $l . "\n");
    
    $minute = substr($time, -2);
    $hour = substr($time, 0, 2);
    $dayOfMonth = substr($date, -2);
    $month = substr($date, 5, 2);
    $filePath = "/tmp/alarm_script-" . $date . "_" . $time . ".sh";
    fwrite ($handle, "$minute $hour $dayOfMonth $month * $filePath\n");
    fclose($handle);
}

function get_alarm_id($db, $date, $time){
    $timeWithSeconds = $time . ":00";
    $sql = "SELECT AlarmID FROM alarms WHERE alarms.Date = '$date' AND alarms.Time = '$timeWithSeconds'";
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)) return $row[0];
    return 0;
}

function set_alarm($db, $stationName, $date, $time, $user, $pass, $fRecurring){
    $stationName = mysqli_real_escape_string($db, $stationName);
    $q = mysqli_query($db, "SELECT stations.StationURL, stations.StationID FROM stations WHERE stations.Name = '$stationName'");
    $row = mysqli_fetch_array($q, MYSQLI_NUM);
    $stationUrl = $row[0];
    $stationID = $row[1];

    if (0 == $fRecurring){
        write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring);
        $AlarmID = get_alarm_id($db, $date, $time);
        $command = "at $time $date <<< '/usr/bin/killall vlc; mysql -u $user -p$pass radio -e \"DELETE FROM NowPlaying\"; mysql -u $user -p$pass radio -e \"INSERT INTO NowPlaying SET NowPlaying.StationID = $stationID\"; mysql -u $user -p$pass radio -e \"DELETE FROM alarms WHERE alarms.AlarmID = $AlarmID\"; /usr/bin/cvlc $stationUrl'";
        write_shell_script($command, $date, $time, $fRecurring);
        shell_exec("/tmp/alarm_script.sh");
        unlink('/tmp/alarm_script.sh');
    } else {
        write_crontab_file($date, $time);
        $command = "/usr/bin/killall vlc; mysql -u $user -p$pass radio -e \"DELETE FROM NowPlaying\"; mysql -u $user -p$pass radio -e \"INSERT INTO NowPlaying SET NowPlaying.StationID = $stationID\"; /usr/bin/cvlc $stationUrl";
        write_shell_script($command, $date, $time, $fRecurring);
        shell_exec("crontab /tmp/tmp-crontab.txt");
        write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring);
    }
}

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

if (!empty($_POST["AlarmID"])) {
    cancel_alarm($db, $_POST["AlarmID"]);
    header("Location: setAlarm.php"); exit;
}

if (!empty($_POST["time"])) {
    set_alarm($db, $_POST["station"], $_POST["date"], $_POST["time"], $user, $pass, isset($_POST["recurring"]) ? 1 : 0);
    header("Location: setAlarm.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set Alarm</title>
<link href='https://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Eagle+Lake' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="js/msdropdown/jquery.dd.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/msdropdown/dd.css" />
<link rel="stylesheet" href="standard.css" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<style>
    .dd .ddTitle .ddTitleText { padding: 10px; font-weight: bold; }
    .dd .ddChild li { padding: 8px; }
    .alarm-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left; margin-bottom: 20px; }
    @media (max-width: 600px) { .alarm-form-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="container">
    <h2>Set Alarm</h2>

    <div class="quick-url-section">
        <form action="setAlarm.php" method="POST">
            <div class="alarm-form-grid">
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Time</label>
                    <input class="input-text" type="time" name="time" value="<?php echo date('H:i'); ?>" required style="margin:0;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Date</label>
                    <input class="input-text" type="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required style="margin:0;">
                </div>
            </div>

            <div style="margin-bottom:20px; text-align:left;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Station</label>
                <select name="station" id="station" required style="width:100%;">
                    <?php
                    $q = mysqli_query($db, "SELECT Name, FileName FROM stations");
                    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)) {
                        echo "<option data-image='uploads/25x25_$row[1]'>$row[0]</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="margin-bottom:30px; background:#f8f9fa; padding:15px; border-radius:10px; text-align:left;">
                <label style="font-weight:bold; cursor:pointer;">
                    <input type="checkbox" name="recurring" value='1' style="transform: scale(1.5); margin-right:10px;"> Recurring Daily
                </label>
            </div>

            <input class="myButton" type="submit" name="Generate" value="Set Alarm">
        </form>
    </div>

    <?php
    $sql = "SELECT stations.Name, alarms.Date, alarms.Time, stations.FileName, alarms.AlarmID, alarms.fRecurring FROM alarms INNER JOIN stations ON alarms.StationID = stations.StationID";
    $q = mysqli_query($db, $sql);
    if (mysqli_num_rows($q) > 0):
    ?>
        <p style="font-family:'Eagle Lake', cursive; font-size:1.2rem; margin:30px 0 15px;">Scheduled Alarms</p>
        <div class="station-grid">
            <?php while ($row = mysqli_fetch_array($q, MYSQLI_NUM)): ?>
                <div class="station-card">
                    <img src="uploads/<?php echo $row[3]; ?>" alt="Station Logo" width="80" height="80">
                    <h3 style="margin-bottom:5px;"><?php echo $row[0]; ?></h3>
                    <p style="margin:0; font-weight:bold; color:var(--accent-color);">
                        <?php echo $row[2]; ?> on <?php echo $row[1]; ?>
                    </p>
                    <p style="margin:5px 0 15px; font-size:0.9rem;">
                        <?php echo ($row[5] == 1) ? '⏰ Recurring Daily' : '📅 Once'; ?>
                    </p>
                    <form action="setAlarm.php" method="post">
                        <input type="hidden" name="AlarmID" value="<?php echo $row[4]; ?>">
                        <input class="myButton" type="submit" name="Generate" value="Cancel Alarm">
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <div class="admin-actions">
        <form action="radio.php" method="POST">
            <input class="myGreenButton" type="submit" name="Generate" value="Back to Main Menu">
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    try { $("#station").msDropDown(); } catch(e) { console.log(e.message); }
});
</script>
</body>
</html>
<?php mysqli_close($db); ?>
