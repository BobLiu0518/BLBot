<?php

global $Queue, $CQ;
use kjBot\SDK\CQCode;

date_default_timezone_set("Asia/Shanghai");
$hour = (int)date('g');
$minute = (int)date('i');
$second = (int)date('s');
/*
if($hour == 9 && $minute == 42 && $second <= 1){
    $groups = array("1059156858");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '叮');
}

if($hour == 9 && $minute == 43 && $second <= 1){
    $groups = array("1059156858");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '↓→↗↑ 欢迎乘坐942路公交车 方向 殷行路中原路');
}
*/
?>
