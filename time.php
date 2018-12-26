<?php

//设置自动报时
//需要在系统中设置任务计划程序(WIN)
//让任务计划程序(WIN)执行 php /path/to/time.php

$dir = __DIR__;
require_once 'vendor/autoload.php';
require_once 'SDK/CoolQ.php';
require_once 'SDK/CQCode.php';
use kjBot\SDK;
use kjBot\SDK\CoolQ;
use kjBot\SDK\CQCode;
use kjBot\Frame\MessageSender;
use kjBot\Frame\Message;
$Config = parse_ini_file('../config.ini', false);
$Event = json_decode(file_get_contents('php://input'), true);
$Event['message'] = CQCode::DecodeCQCode($Event['message']);
$User_id = $Event['user_id'];
$CQ = new CoolQ(config('API', '127.0.0.1:5700'), config('token', ''));
$Queue = [];
$MsgSender = new MessageSender($CQ);
$Debug = config('DEBUG', false);
$DebugListen = config('DebugListen', config('master'));
$Command = [];
$Text = '';

//修正时区到日本
date_default_timezone_set('Asia/Tokyo');

$minute=(int)date('i');
$hour=(int)date('H');

if($minute>=55)$hour++;
if($hour==24)$hour=0;

$groups = array("885444381");

foreach($groups as $group_id){
$CQ->sendGroupMsg($group_id, CQCode::Record('base64://'.base64_encode(file_get_contents($dir."/storage/data/time/".$hour.".mp3"))));
$CQ->sendGroupMsg($group_id, file_get_contents($dir."/storage/data/time/".$hour.".txt"));
}

?>