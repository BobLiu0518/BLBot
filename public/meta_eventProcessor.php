<?php

global $Queue, $CQ, $Config;

$cd = 1 * 60;

if(intval(date('s') < 5) && intval(date('i')) == 0) {
	$CQ->setGroupName(intval($Config['devgroup']), $Config['devgroupName'].' @'.date('H').'点了！');
}