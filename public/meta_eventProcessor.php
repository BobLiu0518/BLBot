<?php

global $Queue, $CQ;
use kjBot\SDK\CQCode;

date_default_timezone_set("Asia/Tokyo");
$hour = (int)date('H');
$minute = (int)date('i');
$second = (int)date('s');
$weekd = (int)date('N');
if($weekd == 2 && $hour == 17 && $minute == 50 && $second <= 1){
    $groups = array("1029944828");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '[CQ:at,qq=all] まもなくLMS利用期間です。今日は19時半まで。'.rand());
}

if($weekd == 6 && $hour == 11 && $minute == 50 && $second <= 1){
    $groups = array("1029944828");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '[CQ:at,qq=all] まもなくLMS利用期間です。今回は13時半まで。'.rand());
}

if($weekd == 6 && $hour == 20 && $minute == 50 && $second <= 1){
    $groups = array("1029944828");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '[CQ:at,qq=all] まもなくLMS利用期間です。今回は明日4時までです。メンテンナンス時間にご注意ください。ごゆっくりどうぞ。'.rand());
}

if($weekd == 7 && $hour == 13 && $minute == 20 && $second <= 1){
    $groups = array("1029944828");
    foreach($groups as $group_id)
        $CQ->sendGroupMsg($group_id, '[CQ:at,qq=all] まもなくLMS利用期間です。今回は15時までです。体育レポートご注意を。'.rand());
}

?>
