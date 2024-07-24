<?php

global $Queue;

$av = str_replace('av', '', str_replace('AV', '', nextArg()));
if(!is_numeric($av))replyAndLeave('请输入有效的av号哦～');

$table = 'fZodR9XQDSUm21yCkr6zBqiveYah8bt4xsWpHnJE7jL5VG3guMTKNPAwcF';
$s = array(11, 10, 3, 8, 4, 6);
$xorVal = 177451812;
$addVal = 8728348608;
$x = ($av ^ $xorVal) + $addVal;
$r = array('B', 'V', '1', ' ', ' ', '4', ' ', '1', ' ', '7', ' ', ' ');
for($i = 0; $i < 6; $i += 1)
	$r[$s[$i]] = $table[ceil($x / pow(58, $i) % 58)];
$bv = implode($r);

$Queue[]= replyMessage($bv);

?>
