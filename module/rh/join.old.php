<?php

loadModule('rh.new');
leave();

global $Event;
use kjBot\SDK\CQCode;

requireLvl(1);

$g = $Event['group_id'];
$f = json_decode(getData('rh/'.$g),true);
if($f['status'] == 'started'){
    replyAndLeave("赛马场正在使用中～");
}else if($f['status'] == 'banned'){
    replyAndLeave("管理员关停了本群内赛马场…");
}else if(!$f){
	loadModule('rh');
	leave();
};

loadModule('credit.tools');

$u = $Event['user_id'];

if(in_array($u, $f['players'])){
    replyAndLeave('你的马已经加入赛场咯～');
}
if(count($f['players'])>=8){
    replyAndLeave("马太多了，赛马场要被塞爆了…");
}
if(coolDown("rh/user/{$Event['user_id']}")<0){
	$time = -coolDown("rh/user/{$Event['user_id']}");
	replyAndLeave('你的马正在休息，大约还需要'.(((intval($time/60)>0)?(intval($time/60).'分'):'')).((($time%60)>0)?($time%60).'秒':'钟').'～');
}

decCredit($Event['user_id'], 500);
coolDown("rh/user/{$Event['user_id']}", 8*60);
$f['players'][] = $u;
setData('rh/'.$g, json_encode($f));
replyAndLeave('加入赛马成功，消耗了500金币！现在赛马场有'.count($f['players']).'匹马了～'."\n赛马场疫情防控指挥部温馨提醒您：\n疫情期间关爱自己关爱他人，务必为自身和马做好防控措施，观看比赛时间隔入座，谢谢配合。");

?>
