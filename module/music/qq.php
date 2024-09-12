<?php

global $Queue, $Event, $Command;
loadModule('credit.tools');

$api = 'https://c.y.qq.com/soso/fcgi-bin/client_search_cp?aggr=1&cr=1&flag_qc=0&p=1&n=1&w=';
$name = trim(implode(' ', array_splice($Command, 1)).$Text);

decCredit($Event['user_id'], 1000);
$result = json_decode(substr(file_get_contents($api.urlencode($name)), 9, -1), true)['data'];
$id = $result['song']['list'][0]['songid'];

if(!$id) {
    addCredit($Event['user_id'], 1000);
    replyAndLeave('没有找到歌曲 '.$name.' …');
}
if(sendBackImmediately('[CQ:music,type=qq,id='.$id.']')) {
    replyAndLeave('点歌成功，扣除 1000 金币~');
} else {
    addCredit($Event['user_id'], 1000);
    replyAndLeave('点歌失败…');
}