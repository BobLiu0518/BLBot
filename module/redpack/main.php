<?php

global $Event, $CQ;
date_default_timezone_set('Asia/Shanghai');
loadModule('credit.tools');
requireLvl(1);
if(!fromGroup()) replyAndLeave('?');
$amount = intval(nextArg());
$count = intval(nextArg());
$code = nextArg();
$redpacks = json_decode(getData('redpack/'.$Event['group_id']), true);
if(!$redpacks) $redpacks = [];

if(!$amount && !$count && !$code) {
	$reply = '';
	foreach($redpacks as $redpack) {
		if($redpack['count']) {
			$reply .= "\n「".$redpack['code'].'」（剩'.$redpack['count'].'个 '.(in_array($Event['user_id'], $redpack['got'])?'已领':'未领').'）';
		}
	}
	if($reply) {
		replyAndLeave('群内未领完红包：'.$reply);
	} else {
		replyAndLeave('群里没有未领完红包噢~');
	}
}

requireLvl(2);
if($amount <= 0 || $count <= 0 || !$code) {
	replyAndLeave("指令用法：\n查红包 #redpack\n发红包 #redpack <金额> <个数> <口令>");
} else if(count(array_filter($redpacks, function($redpack){return $redpack['count'];})) >= 5) {
	replyAndLeave('群内未领红包太多啦，先领掉一些吧');
} else if($amount < $count) {
	replyAndLeave('太小气了吧，不如多发点？');
} else if($count > count($CQ->getGroupMemberList($Event['group_id']))){
	replyAndLeave('个数…太…太多了吧？');
} else if(substr($code, 0, 1) == Config('prefix', '#')){
	replyAndLeave('口令不能以 '.Config('prefix', '#').' 开头噢~');
} else if(preg_match('/\[CQ:[a-z]+.*\]/', preg_replace('/\[CQ:(emoji|face),id=\d+\]/', '', $code))) {
	replyAndLeave('口令中不能包含特殊内容噢~');
} else if(preg_match('/\s/', $code)) {
	replyAndLeave('口令中不能包含空格噢~');
} else if(mb_strlen(preg_replace('/\[CQ:(emoji|face),id=\d+\]/', '1', $code), 'UTF-8') > 16) {
	replyAndLeave('口令太长啦…');
}

$fee = $count == 1 ? intval(0.01 * $amount + 1) : intval(0.0001 * $amount + 1);
decCredit($Event['user_id'], $amount + $fee);
$redpacks[] = [
	'sender' => $Event['user_id'],
	'time' => time(),
	'endTime' => 0,
	'count' => $count,
	'remain' => $amount,
	'avg' => $amount / $count,
	'code' => $code,
	'got' => [],
	'kingOfLuck' => [
		'user_id' => 0,
		'amount' => -1,
	],
];

setData('redpack/'.$Event['group_id'], json_encode($redpacks));
replyAndLeave('发红包成功，共'.$count.'个，'.$amount.'金币（手续费'.$fee."金币）~\n口令：「".$code.'」，发送口令即可抢红包！');

?>
