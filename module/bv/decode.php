<?php

global $Queue;

$bv = nextArg();
if(strpos($bv, 'BV') !== 0)replyAndLeave('请输入有效的BV号哦～');

$table = 'fZodR9XQDSUm21yCkr6zBqiveYah8bt4xsWpHnJE7jL5VG3guMTKNPAwcF';
$tr = array();
for($i = 0; $i < 58; $i += 1)
        $tr[$table[$i]] = $i;
$s = array(11, 10, 3, 8, 4, 6);
$xorVal = 177451812;
$addVal = 8728348608;
$r = 0;
for($i = 0; $i < 6; $i += 1)
	$r += $tr[$bv[$s[$i]]] * pow(58, $i);
$av = ($r - $addVal) ^ $xorVal;

$Queue[]= replyMessage('av'.$av);

?>
