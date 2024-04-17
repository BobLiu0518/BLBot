<?php

global $Queue, $Event;
loadModule('credit.tools');

replyAndLeave('点歌功能暂时停止服务……');

ini_set('user_agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');
$context = stream_context_create(['http' => ['header' => 'Cookie: '.getData('song/cookie')]]);
$api = "http://music.163.com/api/search/pc?offset=0&limit=1&type=1&s=";
$name = "";
do{
	$nextArg = nextArg();
	$name .= " ".$nextArg;
}while($nextArg);
if(!$name)replyAndLeave("没有歌曲名…");

decCredit($Event['user_id'], 1000);
$result = json_decode(file_get_contents($api.urlencode(trim($name)), false, $context), true)['result'];
$id = $result['songs'][0]['id'];

if(!$id){
	addCredit($Event['user_id'], 1000);
	replyAndLeave("没有找到歌曲 ".$name." …");
}

coolDown("song/{$Event['user_id']}", 60*3);

$Queue[]= sendBack("[CQ:music,type=163,id=".$id."]");
// if(fromGroup())$Queue[]= sendPM("[CQ:music,type=163,id=".$id."]");
replyAndLeave("点歌成功，扣除1000金币~");

?>
