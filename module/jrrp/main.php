<?php

global $Event, $Queue, $CQ;

$str = date("Ymd").$Event['user_id'];
$hash = hexdec(crc32($str));
$jrrp = ($hash % 100) + 1;

if(($nextArg = nextArg())== "--full" || $nextArg == "-f")
	$Queue[]= sendBack("[CQ:at,qq=".$Event['user_id']."] 你今天的人品是 ".$hash."！");
else
	$Queue[]= sendBack("[CQ:at,qq=".$Event['user_id']."] 你今天的人品是 ".$jrrp."！");

?>
