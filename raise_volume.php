<?php
function raise_volume(){
    $command = "amixer set PCM 2dB+";
    $command = escapeshellcmd($command);
    exec($command);
    return 0;
}

raise_volume();
?>