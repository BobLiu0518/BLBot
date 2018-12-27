<?php

//voice留下的一大堆cache得清理啊
//需要在系统中设置任务计划程序(WIN)(建议每天2点)
//让任务计划程序(WIN)执行 php /path/to/restart.php

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

$CQ->sendPrivateMsg("2018962389","Bot定时重启");
$CQ->setRestart(true);

?>