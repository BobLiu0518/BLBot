<?php

global $Event, $Queue, $CQ;
requireLvl(1);

loadModule('jrrp.tools');
$mrrp = getRp($Event['user_id'], time() + 86400);

$offsetP = hexdec(crc32($str.'offsetP')) % 16;
$offsetN = 15 - $offsetP;

$Queue[]= replyMessage("Bot 觉得你明天的人品大概在 ".(($mrrp - $offsetN) <= 0 ? 0 : ($mrrp - $offsetN))."~".(($mrrp + $offsetP) > 100? 100: ($mrrp + $offsetP))." 之间");

?>
