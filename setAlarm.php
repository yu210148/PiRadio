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
    <th><center>Recurring</center></th>
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
    <td><center><input type="checkbox" name="recurring" value='1'> (not yet implemented)</center></td>
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

function write_shell_script($command, $date, $time){
    // a function to write a quick shell script 
    // this is needed because php is executing my at
    // command in /bin/sh rather than /bin/bash
    // which doesn't have the <<< construct used in the
    // command
    
    //TODO: need to figure something out here
    // writing the shell script in the uploads 
    // directory creates a vulunarability 
    // because it leaks the database user name
    // and password values if a crafted url is used.
    
    //debug
    //var_dump($date);
    //var_dump($time);
    
    // remove existing file if it exists
    $filePath = "./uploads/alarm_script-" . $date . "_" . $time . ".sh";
    unlink("$filePath");
    
    // open file for writing
    $handle = fopen("$filePath", "w");
    
    // write the hash bang line
    $line = "#!/bin/bash\n";
    fwrite($handle, $line);
    
    // write the command to the file
    $line = "$command";
    fwrite($handle, $line);

    // close the handle
    fclose($handle);
    
    // make the file executable
    $command = "chmod u+x $filePath";
    $command = escapeshellcmd($command);
    shell_exec($command);
    return 0;
}

function show_set_alarms($db){
    // a function to retreive alamrs already set and display them on the screen
    $sql = "SELECT stations.Name, alarms.Date, alarms.Time, stations.FileName, alarms.AlarmID, alarms.fRecurring FROM alarms INNER JOIN stations ON alarms.StationID = stations.StationID";
    $q = mysqli_query($db, $sql);
    
    //debug
    //var_dump($dbOutputArray);
    //<td><center><img src="uploads/$row[2]" alt="Station Logo" width="100" height="100"></center></td>
    
    //$command = "atq";
    //$command = escapeshellcmd($command);
    //exec($command, $outputArray);
    
    //debug
    //var_dump($q->num_rows);
    
    if ($q->num_rows != NULL){
        
    
print <<<HERE
<BR>
<p>The following alarms are set</p>
<table class='mine' border='1'>
HERE;
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        print "<tr>";
        print "<td><center><img src=\"uploads/$row[3]\" alt=\"Station Logo\" width=\"100\" height=\"100\"></center></td>";
        print "<td><center><h3>$row[0]</h3></center></td>";
        print "<td><center><h3>$row[1]</h3></center></td>";
        print "<td><center><h3>$row[2]</h3></center></td>";
        if (1 == $row[5]){
            print "<td><center><h3>Daily</h3></center></td>";
        } else {
            print "<td><center><h3>Once</h3></center></td>";
        } // end else
        print "<td><center><form action=\"setAlarm.php\" method=\"post\"><INPUT type=\"hidden\" name=\"AlarmID\" value=\"$row[4]\"><INPUT class=\"myButton\" type=\"submit\" name=\"Generate\" value=\"Cancel Alarm\"></form></center></td>";
        // AlarmID is $row[4].  Use to cancel alarm
        print "</tr>";
    } // end while
    print "</table>";
} // end if
return 0;
}

function cancel_alarm($db, $AlarmID){
    // a function to cancell an existing alarm
    
    // check if the alarm is a recurring one or not
    $fRecurring = 0;
    $sql = "SELECT alarms.fRecurring FROM alarms WHERE alarms.AlarmID = $AlarmID";
    $q = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
        $fRecurring = $row[0];
    } // end while
    if (0 == $fRecurring){    
        $command = "atq";
        exec($command, $atqOutputArray);
        
        // sample atq output
        //25      Fri Nov  7 09:17:00 2014 a www-data
        //24      Fri Nov  7 08:16:00 2014 a www-data
        
        // get the time & date of selected alarm
        $sql = "SELECT alarms.Date, alarms.Time FROM alarms WHERE alarms.AlarmID = $AlarmID";
        $q = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
            // the above query should only ever return 1 row
            $date = $row[0];
            $time = $row[1];
        } // end while

        // okay, so now I've got the output of the aqt command and the time and date from the db
        // need to isolate the time and date of each at job.  If it matches I'll need the at job number
        
        // change the date format to match the atq output
        $unixTimestamp = strtotime("$date $time");
        $datetimeAtFormat = date('D M j H:i:s Y', $unixTimestamp);
        
        // loop through the lines of atq output 
        foreach ($atqOutputArray as $key=>$line){
            // split line on whitespace so we can deal with the individual elements
            $lineArray = preg_split('/\s+/', $line);
            $datetimeFromAt = $lineArray[1] . " " . $lineArray[2] . " " . $lineArray[3] . " " . $lineArray[4] . " " . $lineArray[5];
            
            // debug
            // var_dump($datetimeFromAt);
            // string(18) "Fri Nov 7 09:17:00" string(18) "Fri Nov 7 08:16:00"
            // var_dump($datetimeAtFormat);
            // string(23) "Fri Nov 7 08:16:00 2014" string(23) "Fri Nov 7 08:16:00 2014"
            
            if ($datetimeAtFormat == $datetimeFromAt){
                $atJobNumber = $lineArray[0];
            } // end if
        } // end foreach
        
        // delete the matched at job
        if (NULL != $atJobNumber){
            $command = "atrm $atJobNumber";
            exec($command);
        } // end if

        // delete the row from the database
        if (NULL != $atJobNumber){
            $sql = "DELETE FROM alarms WHERE alarms.AlarmID = $AlarmID";
            mysqli_query($db, $sql);
        } // end if
        /*

    [Fri Nov 07 06:49:49.040356 2014] [:error] [pid 24467] [client 127.0.0.1:48628] PHP Notice:  Undefined variable: atJobNumber in /var/www/PiRadio/setAlarm.php on line 199
    Usage: at [-V] [-q x] [-f file] [-mMlbv] timespec ...
        at [-V] [-q x] [-f file] [-mMlbv] -t time
        at -c job ...
        atq [-V] [-q x]
        at [ -rd ] job ...
        atrm [-V] job ...
        batch


        */
    } else {
        // TODO: Find a way to cancel a cron job from the command line because this alarm needs to be cancelled
        // but it's recurring so it's in www-data's crontab file
        print "<BR>Not Yet working.  You'll have to manually remove the job from www-data's crontab for now.<BR>";
        
        exec('crontab -l', $output);
        //$output = implode("\n", $output);
        
        // TODO: now need to search $output for the line that matches
        // remove it, then update the crontab
        
        // get the time & date of selected alarm
        $sql = "SELECT alarms.Date, alarms.Time FROM alarms WHERE alarms.AlarmID = $AlarmID";
        $q = mysqli_query($db, $sql);
        while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
            // the above query should only ever return 1 row
            $date = $row[0];
            $time = $row[1];
        } // end while
        
        //debug 
        // compare output to $date & $time
        //var_dump($output);
        //var_dump($date);
        //var_dump($time);
        
        /*
        Not Yet working. You'll have to manually remove the job from www-data's crontab for now.
array(2) { [0]=> string(56) "00 18 10 11 * ./uploads/alarm_script-2014-11-10_18:00.sh" [1]=> string(56) "00 19 10 11 * ./uploads/alarm_script-2014-11-10_19:00.sh" } string(10) "2014-11-10" string(8) "18:00:00"
        */

        $minute = substr($time, -5, 2);
        $hour = substr($time, 0, 2);
        $dayOfMonth = substr($date, -2);
        $month = substr($date, -5, 2);
        
        foreach ($output as $cronJob){
            // check if $cronJob is the one we want to cancel based on time
            $lineArray = explode(" ", $cronJob);
            
            //debug
            //foreach ($lineArray as $val){
            //   print "<br>value of lineArray element on line 298 is: $val<br>";
            //    var_dump($minute);
            //    var_dump($hour);
            //    var_dump($dayOfMonth);
            //    var_dump($month);
            //} // end foreach
            
            if ($minute == $lineArray[0] && $hour == $lineArray[1] && $dayOfMonth == $lineArray[2] && $month == $lineArray[3]){ 
                // if it is don't output it to the new crontab we'll have to write
            } else {
                $newCrontab[] = $cronJob;
            } // end else
        } // end foreach
        
        // TODO: If I've done this right the new crontab file we need to load up is in the 
        // $newCrontab array at this point.  Write this out to a file, purge the old
        // crontab via the exec() function and load this one (again via the exec()
        // function.
        
        //debug
        //print "<br>value of newCrontab is:<br>";
        //var_dump($newCrontab);
        
        // write new crontab file
        unlink('./uploads/tmp-crontab.txt');
        $handle = fopen('./uploads/tmp-crontab.txt');
        foreach ($newCrontab as $line){
            fwrite($handle, $line);
        } // end foreach
        fclose($handle);
        
        // purge old crontab
        $command = "crontab -r";
        exec($command);
        
        // set crontab
        $command = "crontab ./uploads/tmp-crontab.txt";
        shell_exec($command);
        
        // remove meta info from db
        //$sql = "DELETE FROM alarms WHERE alarms.AlarmID = '$AlarmID'";
        //mysqli_query($db, $sql);
    } // end else
    return 0;
}

function write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring){
    // a function to write info about the alarm being set to a table in the db

    $sql = "INSERT INTO alarms VALUES ('NULL', '$stationID', '$date', '$time', '$fRecurring')";
    //$sql = mysqli_real_escape_string($db, $sql);
    mysqli_query($db, $sql);
    
    //debug
    //var_dump($sql);
    return 0;
}

function write_crontab_file($date, $time){
    // a function to write out a file in the crontab format
    // with the scheduling info based on the values of $date
    // and $time
    
    // remove existing file if it exists
    unlink('./uploads/tmp-crontab.txt');
    
    // open file for writing
    $handle = fopen('./uploads/tmp-crontab.txt', "w");
    
    // get existing crontab contents & write them
    exec('crontab -l', $output);
    $output = implode("\n", $output);
    if (empty($output)){
        // do nothing
    } else {
        // append a new line character
        $output = $output . "\n";
    } // end else
    
    fwrite($handle, $output);
    
    
    $minute = substr($time, -2);
    $hour = substr($time, 0, 2);
    
    //debug
    //var_dump($minute);
    //var_dump($hour);
    
    $dayOfMonth = substr($date, -2);
    $month = substr($date, -5, 2);
    $dayOfWeek = "*";
    $filePath = "./uploads/alarm_script-" . $date . "_" . $time . ".sh";
    $command = $filePath;
    
    $line = $minute . " " . $hour . " " . $dayOfMonth . " " . $month . " " . $dayOfWeek . " " . $command . "\n";
    fwrite ($handle, $line);
    
    // close the handle
    fclose($handle);
    
    /*
        Linux Crontab Format

    MIN HOUR DOM MON DOW CMD
    Table: Crontab Fields and Allowed Ranges (Linux Crontab Syntax)
    Field   Description     Allowed Value
    MIN     Minute field    0 to 59
    HOUR    Hour field      0 to 23
    DOM     Day of Month    1-31
    MON     Month field     1-12
    DOW     Day Of Week     0-6
    CMD     Command Any command to be executed.
    */
    return 0;
}

function set_alarm($db, $stationName, $date, $time, $user, $pass, $fRecurring){
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
    if (0 == $fRecurring){
        $command = "at $time $date <<< '/usr/bin/killall vlc; mysql -u $user -p$pass radio -e \"DELETE FROM NowPlaying\"; mysql -u $user -p$pass radio -e \"INSERT INTO NowPlaying SET NowPlaying.StationID = $stationID\"; mysql -u $user -p$pass radio -e \"DELETE FROM alarms WHERE alarms.date = '$date' AND alarms.time = '$time'\"; /usr/bin/cvlc $stationUrl'";
        write_shell_script($command, $date, $time);
        $command = "./uploads/alarm_script.sh";
        $command = escapeshellcmd($command);
        $output = shell_exec($command);
        write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring);
    } else if (1 == $fRecurring){
        // set cron job for recurring alarm
        write_crontab_file($date, $time);
        $command = "/usr/bin/killall vlc; mysql -u $user -p$pass radio -e \"DELETE FROM NowPlaying\"; mysql -u $user -p$pass radio -e \"INSERT INTO NowPlaying SET NowPlaying.StationID = $stationID\"; /usr/bin/cvlc $stationUrl'";
        write_shell_script($command, $date, $time);
        $command = "crontab ./uploads/tmp-crontab.txt";
        shell_exec($command);
        write_alarm_meta_info_to_db($db, $stationID, $date, $time, $fRecurring);
    }
    return 0;
}


// HERE'S MAIN
$time = $_POST["time"];
$date = $_POST["date"];
$stationName = $_POST["station"];
$fRecurring = $_POST["recurring"];
$AlarmID = $_POST["AlarmID"];

$db = mysqli_connect($dbServer, $user, $pass, $databaseName);

//debug
// test data
//$AlarmID = 21;
//cancel_alarm($db, $AlarmID);
//$time = "12:37:00";
//$date = "2014-11-09";
//write_crontab_file($date, $time);
//var_dump($time);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// see if we're cancelling a set alarm
if ($AlarmID != NULL){
    cancel_alarm($db, $AlarmID);
} // end if

if (empty($time)){
    print_form($db);
    show_set_alarms($db);
} else {
    print_form($db);
    set_alarm($db, $stationName, $date, $time, $user, $pass, $fRecurring);

    //print "<h3>DONE! Alarm Set.</h3>";
    show_set_alarms($db);
} // end the grand else

mysqli_close($db);
?>
