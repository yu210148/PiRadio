<?php
require_once 'settings.php';
function stop_player($db){
    $command = "killall vlc";
    $command = escapeshellcmd($command);
    exec($command);
    $sql = "DELETE FROM NowPlaying";
    mysqli_query($db, $sql);
    return 0;
}
$db = mysqli_connect($dbServer, $user, $pass, $databaseName);
stop_player($db);
mysqli_close($db);