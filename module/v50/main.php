<?php

global $Event;
loadModule('credit.tools');
requireLvl(1);
$data = json_decode(getData('v50/'.$Event['user_id']) ?? '{"date": 0, "count": 0}', true);
if(date('N') != 4){
	replyAndLeave('今天不是周四哦？');
}else if($data['date'] != date('Ymd')){
	$data['date'] = date('Ymd');
	$date['times'] = 0;
}

if($data['times'] >= 10){
	replyAndLeave('今天已经 v50 太多次了，下次再来吧～');
}else if(getCredit($Event['user_id']) < 50){
	replyAndLeave('你的余额不够 v50 哦…');
}

$data['times']++;
setData('v50/'.$Event['user_id'], json_encode($data));
if(rand(1, 100) >= 40){
	addCredit($Event['user_id'], 50);
	replyAndLeave("Bot 请你吃 KFC，给你 v50！\n你的金币余额是：".getCredit($Event['user_id']));
}else{
	decCredit($Event['user_id'], 50, true);
	replyAndLeave("谢谢你 v50 请 Bot 吃 KFC！\n你的金币余额是：".getCredit($Event['user_id']));
}

?>
