<?php

global $Event, $Queue, $CQ;
requireLvl(0);

$str = date("Ymd", (time() + 86400)).$Event['user_id'];
$hash = hexdec(crc32($str));
$mrrp = ($hash % 150) + 1;
if($mrrp > 100) $mrrp -= 50;

$offsetP = hexdec(crc32($str.'offsetP')) % 15;
$offsetN = hexdec(crc32($str.'offsetN')) % 15;

$Queue[]= replyMessage("Bot 觉得你明天的人品大概在 ".(($mrrp - $offsetN) <= 0 ? 0 : ($mrrp - $offsetN))."~".(($mrrp + $offsetP) > 100? 100: ($mrrp + $offsetP))." 之间");

?>
