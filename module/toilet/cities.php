<?php

global $Queue;
$img = trim(sendImg(getImg('toilet.png')));
$Queue[]= replyMessage($img);

?>
