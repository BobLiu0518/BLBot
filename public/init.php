<?php

include('../vendor/autoload.php'); //避免没有vendor的用户出错
use kjBot\SDK\CoolQ;
use kjBot\SDK\CQCode;
use kjBot\Frame\MessageSender;

$pattern = "/[\x{007f}-\x{009f}]|\x{00ad}|[\x{0483}-\x{0489}]|[\x{0559}-\x{055a}]|\x{058a}|[\x{0591}-\x{05bd}]|\x{05bf}|[\x{05c1}-\x{05c2}]|[\x{05c4}-\x{05c7}]|[\x{0606}-\x{060a}]|[\x{063b}-\x{063f}]|\x{0674}|[\x{06e5}-\x{06e6}]|\x{070f}|[\x{076e}-\x{077f}]|\x{0a51}|\x{0a75}|\x{0b44}|[\x{0b62}-\x{0b63}]|[\x{0c62}-\x{0c63}]|[\x{0ce2}-\x{0ce3}]|[\x{0d62}-\x{0d63}]|\x{135f}|[\x{200b}-\x{200f}]|[\x{2028}-\x{202e}]|\x{2044}|\x{2071}|[\x{f701}-\x{f70e}]|[\x{f710}-\x{f71a}]|\x{fb1e}|[\x{fc5e}-\x{fc62}]|\x{feff}|\x{fffc}/u";

//全局变量区
$Config = parse_ini_file('../config.ini', false);
$Event = json_decode(file_get_contents('php://input'), true);
$Event['message'] = preg_replace($pattern, '', trim(CQCode::DecodeCQCode($Event['raw_message'] ?? $Event['message'] ?? '')));
$User_id = $Event['user_id'];
$CQ = new CoolQ(config('API', '127.0.0.1:5700'), config('token', ''));
$Queue = [];
$MsgSender = new MessageSender($CQ);
$Debug = config('DEBUG', false);
$DebugListen = config('DebugListen', config('master'));
$Command = [];
$Text = '';
$Referer = null;
$dbClient = new MongoDB\Client('mongodb://localhost:'.config('dbPort', 27017), [
    'appName' => 'BLBotLite',
    'username' => config('dbUsername'),
    'password' => config('dbPassword'),
    'authSource' => 'BLBotLite',
]);
$Database = $dbClient->selectDatabase('BLBotLite', ['typeMap' => ['array' => 'array', 'document' => 'array', 'root' => 'array']]);

$Database->group->updateOne(
    ['group_id' => $Event['group_id']],
    [
        '$set' => [
            "members.{$Event['user_id']}" => [
                'real_user_id' => $Event['real_user_id'],
                ...$Event['sender']
            ]
        ]
    ],
    ['upsert' => true]
);