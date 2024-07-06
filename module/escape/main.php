<?php

global $Event;
loadModule('jrrp.tools');
loadModule('credit.tools');
loadModule('attack.tools');

requireLvl(1);
if(!fromGroup()){
	replyAndLeave('本指令只能在群聊中使用哦…');
}

$jrrp = getRp($Event['user_id'], time());
$data = getAttackData($Event['user_id']);

switch(getStatus($Event['user_id'])){
	case 'free':
		if(rand(1, 100) <= 85){
			replyAndLeave('你现在不在监狱哦…难道还想再进去一次？');
		}else{
			$data['status'] = 'universe';
			$data['end'] = date('Ymd', time() + 86400);
			setAttackData($Event['user_id'], $data);
			replyAndLeave('你已成功逃离地球。');
		}
		break;
	case 'hospitalized':
		replyAndLeave('住院的时候还是以身体为重吧。');
		break;
	case 'arknights':
	case 'genshin':
		replyAndLeave('你并不知道从哪里可以逃出去…');
		break;
	case 'universe':
		replyAndLeave('你已经身处宇宙中了…还能逃向何方呢？');
		break;
	case 'saucer':
		replyAndLeave('你被外星人五花大绑了…还能逃向何方呢？');
		break;
	case 'imprisoned':
	case 'confined':
		if($data['escape']['date'] == date('Ymd') && $data['escape']['times'] > 0){
			replyAndLeave('你今天喜提狱警特别关照，别试了，没用的，洗洗睡吧。');
		}
		$data['escape']['date'] = date('Ymd');
		$data['escape']['times'] += 1;
		if(rand(1, 100) <= 2){
			// 进医院
			$message = '越狱时你感到一阵刺痛，等你醒来时已经元气大伤，躺在了手术台上。(支付 20000 金币手术费)';
			$data['status'] = 'hospitalized';
			$data['end'] = date('Ymd', time() + 86400);
			decCredit($Event['user_id'], 20000, true);
		}else if(rand(1, 100) <= 50 + 0.5 * $jrrp){
			// 越狱成功
			$message = '趁狱警不注意，你成功溜了出来。';
			$data['status'] = 'free';
			$data['end'] = '0';
			$data['count']['times'] = ceil($data['count']['times'] / 2 + 0.5);
		}else{
			// 越狱失败
			$message = '越狱失败了，';
			if(rand(1, 100) <= $jrrp){
				// 罚款
				$fine = rand(30000, 60000);
				decCredit($Event['user_id'], $fine, true);
				$message .= '你被罚款 '.$fine.' 金币';
			}else{
				// 加一天
				if(getStatusEndTime($Event['user_id']) != '∞'){
					$data['end'] = date('Ymd', strtotime(getStatusEndTime($Event['user_id'])) + 86400);
				}
				$message .= '你蹲监狱的时间延长了一天';
			}
			$message .= '，并被狱警特别关照…';
		}
		setAttackData($Event['user_id'], $data);
		replyAndLeave($message);
};

?>
