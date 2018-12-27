<?php

//注释：使用这个命令必须确保 Bot 在 IIS 上运行
//其他服务器软件如 Apache、Nginx 需要重写

global $Queue, $Event;
requireSeniorAdmin();

$CQ->sendMsg($Event['user_id'], "Processing…");

date_default_timezone_set('Asia/Shanghai');

$date = (int)date('Ymd')%1000000;

if($x = nextArg())$date = $x;

$log = file_get_contents("Z:\\Logs\\W3SVC1\\ex".$date.".log");


$send = str_split($log,500);

foreach($send as $str){//似乎有毛病
    $Queue[]= sendPM($str);
}

?>