<?php
function lower_volume(){
    $command = "amixer -c 1 set PCM 2dB-";
    $command = escapeshellcmd($command);
    exec($command);
    return 0;
}
lower_volume();
?>
