<?php

global $Queue;
$img = trim(sendImg(getImg('help.png')));
$Queue[]= replyMessage($img);

?>
