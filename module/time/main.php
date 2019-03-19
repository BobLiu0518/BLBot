﻿<?php

global $Queue;
use kjBot\SDK\CQCode;

//修正时区到日本
date_default_timezone_set('Asia/Tokyo');

$minute=(int)date('i');
$hour=(int)date('H');

if($minute>=45)$hour++;
if($hour==24)$hour=0;

$hour = nextArg()??$hour;

$Queue[]= sendBack(getData("time/{$hour}.txt"));
$Queue[]= sendBack(CQCode::Record('base64://'.base64_encode(getData("time/{$hour}.mp3"))));
if(fromGroup()){
    $Queue[]= sendPM(getData("time/{$hour}.txt"));
    $Queue[]= sendPM(CQCode::Record('base64://'.base64_encode(getData("time/{$hour}.mp3"))));
}
?>
