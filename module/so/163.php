<?php

global $Queue, $Event;
loadModule('credit.tools');
requireAdmin();

$api = "http://music.163.com/api/search/pc?offset=1&limit=1&type=1&s=";
$name = "";
do{
	$nextArg = nextArg();
	$name .= " ".$nextArg;
}while($nextArg);
if(!$name)leave("没有歌曲名！");

decCredit($Event['user_id'], 1000);
$result = json_decode(file_get_contents($api.urlencode($name)), true)['result'];

$id = $result['songs'][0]['id'];

if(!$id){
	addCredit($Event['user_id'], 1000);
	leave("点歌 ".$name." 失败！");
}
$Queue[]= sendBack("[CQ:music,type=163,id=".$id."]");
if(fromGroup())$Queue[]= sendPM("[CQ:music,type=163,id=".$id."]");
leave("点歌成功，扣除1000金币！");

?>
