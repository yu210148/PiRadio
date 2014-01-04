<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Internet Radio Stations</title>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<link rel=StyleSheet href="standard.css" type="text/css">
</head>
<body>
<?php
require_once 'settings.php';

function get_stations($db){
    $sql="SELECT
        stations.Name,
        stations.StationURL,
        stations.FileName
        FROM
        stations
        ORDER BY
        stations.StationID";
    $q = mysqli_query($db, $sql);
    return $q;
}

function print_form($db){
//        $divFirstTime = "<div class='hideForm'>";
//        $endDivFirstTime = "</div>";

//        $hideButton = "<div align=\"right\"><a href =\"#\" id=\"show\">Show </a>/ <a href=\"#\" id=\"hide\">Hide </a>Form</div>";

// database stuff here
$q = get_stations($db);

print <<<HERE
<center><h2>Internet Radio Stations</h2>

<table border=0 width=100%>
<tr>
    <td><center>
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <INPUT class="myButton" type="submit" name="Generate" value="Stop Player">
        </FORM>
    </center></td>
    <td><center>
        <table border=0>
        <tr>
            <th colspan=2>
                Volume
            </th>
        </tr>
        <tr>
            <td><center>
                <FORM action="radio.php" method="POST">
                <input type="hidden" name="volume" value="down">
                <input type="hidden" name="stopPlayer" value="No">
                <INPUT class="volumeButton" type="submit" name="Generate" value="-">
                </FORM>
            </center></td>
            <td><center>
                <FORM action="radio.php" method="POST">
                <input type="hidden" name="volume" value="up">
                <input type="hidden" name="stopPlayer" value="No">
                <INPUT class="volumeButton" type="submit" name="Generate" value="+">
                </FORM>
            </center></td>
        </tr>
        </table>
    </center></td>
</tr>
</table>

<table class='mine' border = '1'>

HERE;

while ($row = mysqli_fetch_array($q, MYSQLI_NUM)){
    // debug
    //var_dump($row);
    print <<<HERE
    <tr>
        <FORM action="radio.php" method="POST">
        <input type="hidden" name="stopPlayer" value="Yes">
        <input type="hidden" name="stationUrl" value="$row[1]">
        <td><center><img src="uploads/$row[2]" alt="Station Logo" width="100" height="100"><center></td>
        <td><center><h3><center>$row[0]</center></h3></td>
        <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
        </FORM>
    </tr>
HERE;
} // end while

mysqli_free_result($q);

/*
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="kcrwsimulcast.pls">
    <td><center><img src="kcrw.png" alt="KCRW Logo" width="100" height="100"><center></td>
    <td><center><h3><center>KCRW Los Angles</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://current.stream.publicradio.org/kcmp.mp3">
    <td><center><img src="the_current.png" alt="The Current Logo" width="100" height="100"><center></td>
    <td><center><h3><center>The Current<br>Minnesota Public Radio</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://playerservices.streamtheworld.com/pls/CBC_R1_TOR_L.pls">
    <td><center><img src="cbc.png" alt="CBC Logo" width="100" height="100"><center></td>
    <td><center><h3><center>CBC Radio One Toronto</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://193.42.152.215:8000/listen.pls">
    <td><center><img src="wrn.png" alt="WRN Logo" width="100" height="100"><center></td>
    <td><center><h3><center>WRN English North America</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://193.42.152.215:8026/listen.pls">
    <td><center><img src="wrn.png" alt="WRN Logo" width="100" height="100"><center></td>
    <td><center><h3><center>WRN English Europe</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://193.42.152.215:8012/listen.pls">
    <td><center><img src="wrn.png" alt="WRN Logo" width="100" height="100"><center></td>
    <td><center><h3><center>WRN English Africa & Asia Pacific</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
<tr>
    <FORM action="radio.php" method="POST">
    <input type="hidden" name="stopPlayer" value="Yes">
    <input type="hidden" name="stationUrl" value="http://www.abc.net.AU/res/streaming/audio/windows/radio_australia_eng_asia.asx">
    <td><center><img src="abc_ra.png" alt="ABC Radio Australia Logo" width="100" height="100"><center></td>
    <td><center><h3><center>ABC Radio Australia</center></h3></td>
    <td><center><INPUT class="myGreenButton" type="submit" name="Generate" value="Play"></center></td>
    </FORM>
</tr>
*/

print <<<HERE
</table>
</center>
<div align='right'>
<FORM action="addStation.php" method="POST">
<INPUT class="myGreenButton" type="submit" name="Generate" value="Add A Station">
</FORM>
</div>
HERE;
return 0;
} // end function print_form()

function raise_volume(){
    $command = "amixer set PCM 2dB+";
    exec($command);
    return 0;
}

function lower_volume(){
    $command = "amixer set PCM 2dB-";
    exec($command);
    return 0;
}

function stop_player(){
    $command = "killall vlc";
    
    //debug
    //var_dump($command);
    
    exec($command);
    return 0;
}

function start_player($stationUrl){
    // stop the player in case it's running
    stop_player();
    $command = "cvlc $stationUrl";
    exec($command . " > /dev/null &");
    return 0;
}

// HERE'S MAIN
$db = mysqli_connect($dbServer, "klucas", "8clock9", "radio");

// are we adjusting the volume?
$volumeAdjust = $_POST["volume"];
if ("down" == $volumeAdjust){
    lower_volume();
    print_form($db);
} else if ("up" == $volumeAdjust){
    raise_volume();
    print_form($db);
} else {
    // we're not adjusting the volume so move on
    if (empty($_POST["stopPlayer"])){
        $stopPlayer = "No";
    } else {
        $stopPlayer = $_POST["stopPlayer"];
    } // end else
    $stationUrl = $_POST["stationUrl"];

    if (empty($stationUrl)) {
        if ('Yes' == $stopPlayer){
            stop_player();
        } // end if
        print_form($db);
    } else {
        if ('Yes' == $stopPlayer){
            stop_player();
        } // end if
        print_form($db);
        start_player($stationUrl);
    } // end else
}

mysqli_close($db);
?>
