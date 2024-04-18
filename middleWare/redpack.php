<?php

global $Message, $Event, $Queue;
loadModule('credit.tools');
date_default_timezone_set('Asia/Shanghai');
if(fromGroup()){
	$redpacks = json_decode(getData('redpack/'.$Event['group_id']), true);
	if(!$redpacks) $redpacks = [];
	$empty = 0;
	$got = 0;
	$count = count($redpacks);
	$time = time();
	$expired = [];
	$redpacks = array_filter($redpacks, function($redpack){
		global $expired, $time;
		if($time - $redpack['time'] > 86400 && $redpack['count']){
			$expired[] = $redpack;
			return false;
		}
		return ($redpack['count'] || $time - $redpack['endTime'] < 2 * 60);
	});
	$deleted = $count - count($redpacks);
	foreach($redpacks as $n=> $redpack){
		if($Message == $redpack['code']){
			requireLvl(1);
			if(!$redpack['count'] && $empty != -1){
				$empty = 1;
			}
			if(in_array($Event['user_id'], $redpack['got'])){
				$got = 1;
			}else if($redpack['count'] && !in_array($Event['user_id'], $redpack['got'])){
				$get = 0;
				if($redpack['count'] == 1){
					$get = $redpack['remain'];
				}else{
					$get = rand(ceil($redpack['avg'] * 0.01), min($redpack['remain'] - $redpack['count'] + 1, $redpack['avg'] * 2));
				}
				$redpacks[$n]['count'] --;
				$redpacks[$n]['remain'] -= $get;
				$redpacks[$n]['got'][] = $Event['user_id'];
				$empty = -1;
				addCredit($Event['user_id'], $get);
				$Queue[]= replyMessage('恭喜抢到 '.$get.' 金币~	');
				if($get > $redpacks[$n]['kingOfLuck']['amount']){
					$redpacks[$n]['kingOfLuck']['amount'] = $get;
					$redpacks[$n]['kingOfLuck']['user_id'] = $Event['user_id'];
				}
				if(!$redpacks[$n]['count']){
					$Queue[]= sendBack('红包抢完啦，[CQ:at,qq='.$redpacks[$n]['kingOfLuck']['user_id'].'] 是运气王~');
					$redpacks[$n]['endTime'] = $time;
				}
			}
		}
	}
	if($expired){
		$reply = '已清理过期红包：';
		foreach($expired as $redpack){
			$reply .= "\n「".$redpack['code'].'」（剩'. $redpack['remain'].'金币）';
			addCredit($redpack['sender'], $redpack['remain']);
		}
		$reply .= "\n金币已退回~";
		$Queue[]= sendBack($reply);
	}
	if($got == 1 && $empty != -1){
		replyAndLeave('你已经领过这个红包啦！');
	}if($empty == 1){
		replyAndLeave('手慢了，红包派完了…');
	}else if($empty == -1 || $deleted >= 1){
		setData('redpack/'.$Event['group_id'], json_encode($redpacks));
		if($empty == -1){
			leave();
		}
	}
}

?>
