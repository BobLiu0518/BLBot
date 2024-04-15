<?php

global $Queue, $Event;
loadModule('credit.tools');

$api = "https://c.y.qq.com/soso/fcgi-bin/client_search_cp?aggr=1&cr=1&flag_qc=0&p=1&n=1&w=";
$name = "";
do{
	$nextArg = nextArg();
	$name .= " ".$nextArg;
}while($nextArg);
if(!$name)leave("没有歌曲名！");

decCredit($Event['user_id'], 1000);
$result = json_decode(substr(file_get_contents($api.urlencode($name)), 9, -1), true)['data'];

$id = $result['song']['list'][0]['songid'];

if(!$id){
	addCredit($Event['user_id'], 1000);
	replyAndLeave("没有找到歌曲 ".$name." …");
}
//leave($id);
$Queue[]= sendBack("[CQ:music,type=qq,id=".$id."]");
// if(fromGroup())$Queue[]= sendPM("[CQ:music,type=qq,id=".$id."]");
replyAndLeave("点歌成功，扣除1000金币~");

?>
