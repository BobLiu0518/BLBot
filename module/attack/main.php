<?php

global $Event, $Queue, $CQ;

requireLvl(1);

loadModule('credit.tools');
loadModule('exp.tools');
loadModule('jrrp.tools');
loadModule('attack.tools');

function randString(array $strArr){
	return $strArr[rand(0, sizeof($strArr)-1)];
}

$today = date('Ymd');
$from = $Event['user_id'];
$target = nextArg();
$magnification = floatval(getData('attack/group/'.$Event['group_id']));
if(!$magnification){
	$magnification = 1;
}
if(!(preg_match('/\d+/', $target, $match) && $match[0] == $target)){
	$target = parseQQ($target);
}
$target = intval($target);
if($target == config('bot')){
	replyAndLeave('你竟然想抢劫 Bot？！');
}else if($target === 0){
	replyAndLeave("要抢劫谁呢？\n(注：复制含有“@”的消息，@ 会失效。可以手动重新 @ 或者直接输入 QQ 号。)");
}
$groupMemberList = $CQ->getGroupMemberList($Event['group_id']);
$targetInGroup = false;
foreach($groupMemberList as $groupMember){
	if($groupMember->user_id == $target){
		$targetInGroup = true;
	}
}
if(!$targetInGroup){
	replyAndLeave("你并不知道要去哪里打劫 {$target}。\n(打劫目标不在本群内)");
}

$atTarget = '@'.($CQ->getGroupMemberInfo($Event['group_id'], $target)->card ? $CQ->getGroupMemberInfo($Event['group_id'], $target)->card : $CQ->getGroupMemberInfo($Event['group_id'], $target)->nickname);

if(!fromGroup() || $target == $from){
	$money = getCredit($from);
	replyAndLeave("你把自己洗劫一空。\n(金币 - $money, 金币 + $money)");
}else if(!$target){
	replyAndLeave('要抢劫谁呢？');
}

$data = getAttackData($from);

$message = '';
switch($data['status']){
	case 'imprisoned':
		if(rand(0, 1)){
			$message = "狱警发现了你的小动作，对你进行了口头警告。";
		}else{
			$data['status'] = 'confined';
			$message = "狱警发现了你的小动作，把你关进了禁闭室。";
		}
		break;
	case 'confined':
		$message = "你成功抢劫了 {$atTarget}，但当你数钱时，突然发现自己在禁闭室里做白日梦。";
		break;
	case 'hospitalized':
		$message = "在病床上，你没有力气活动身体。";
		break;
	case 'arknights':
		$message = "你刚想离开办公室看看能不能找到回原世界的路，但一推开门就看到".randString(['那位绿发猫耳女士用严厉的眼光看着你。', '一位兔耳少女对你投来了关切的眼神。'])."你不由自主回到了办公桌前。\n(理智 - 1)";
		break;
	case 'genshin':
		$message = '你刚想推开门回到原来的世界，但一开门就看到了一个漂浮的白色小东西，并对你说“前面的区域，以后再来探索吧”';
		break;
	case 'free':
		$data['count']['times'] += 1;

		$successRate = $data['count']['times'] > 3 ? 40 : (10 + $data['count']['times'] * 10);
		$prisonRate = pow(2, $data['count']['times']);
		$success = rand(1, 100) <= $successRate;
		$prison = rand(1, 100) <= $prisonRate;
		$getMoney = intval((getLvl($from) - getLvl($target) + 10) * (getRp($from, time()) - getRp($target, time()) + 100) * rand(100 * $magnification, 1000 * $magnification) / 200 + 1);
		if(getCredit($target) - 10000 <= $getMoney) $getMoney = getCredit($target) - 9999;
		if(getCredit($target) < 10000) $success = false;

		if($success && $prison){
			$fine = intval(sqrt($getMoney) * 10) + 500 * $magnification;
			decCredit($from, $fine, true);
			$data['status'] = 'imprisoned';
			$data['end'] = date('Ymd', time() + 86400 * 2);
			$message = "抢劫 {$atTarget} 很成功，但刚准备开润，你的手腕上就多了一副银镯子。\n(被罚款 {$fine} 金币，入狱 2 天)";
		}else if($success && !$prison){
			decCredit($target, $getMoney, true);
			addCredit($from, $getMoney);
			$message = randString(["你成功从 {$atTarget} 手上夺走了 {$getMoney} 金币。", "你从 {$atTarget} 口袋里摸走了 {$getMoney} 金币。", "{$atTarget} 立刻投降，你顺走了 {$getMoney} 金币。"]);
		}else if(!$success && $prison){
			$fine = 500 * $magnification;
			decCredit($from, $fine, true);
			$data['status'] = 'imprisoned';
			$data['end'] = date('Ymd', time() + 86400);
			$message = randString(["正在你向 {$atTarget} 喊出“打劫”的时候，一旁的警察瞥了你一眼。\n(被罚款 {$fine} 金币，入狱 1 天)"]);
		}else if(!$success && !$prison){
			if(rand(1, 100) <= 4){
				$event = rand(1, 5);
				switch($event){
					case 1:
						decCredit($from, 10000, true);
						$data['status'] = 'hospitalized';
						$data['end'] = date('Ymd', time() + 86400);
						$message = "你正在去打劫 {$atTarget} 的路上，突然有一匹失控的🐴从赛🐴场冲了出来，把你撞翻在地。\n(住院 1 天，支付医药费 10000 金币)";
						break;
					case 2:
						decCredit($target, 10000, true);
						addCredit($from, 10000);
						$message = "你试图打劫 {$atTarget}，但反被 {$atTarget} 打伤。\n(住院 1 天，获赔精神损失费 10000 金币)";
						break;
					case 3:
						decCredit($from, 200, true);
						$message = "你在 {$atTarget} 家门口蹲他，但他一整天都没有出现。\n(支付车费 200 金币)";
						break;
					case 4:
						$data['status'] = 'arknights';
						$data['end'] = date('Ymd', time() + 86400);
						$message = "你正在打劫 {$atTarget} 的路上，突然感觉到一阵晕眩。醒来时，你发现自己身处一艘陆上舰船，边上还有一位绿发猫耳女士催你去工作。";
						break;
					case 5:
						$data['status'] = 'genshin';
						$data['end'] = date('Ymd', time() + 86400);
						$message = '你正在打劫 {$atTarget} 的路上，突然感觉到一阵晕眩。醒来时，你听见有人正在声称自己不是应急食品。';
						break;
				}
			}else{
				$message = randString(["你试图打劫 {$atTarget}。他把钱包翻了出来，发现是空的。", "{$atTarget} 一看到你就溜了。","你正准备打劫 {$atTarget}，但突然发现旁边有警察，只好开始尬聊天气。"]);
			}
		}
		break;
}

setAttackData($from, $data);
$Queue[]= replyMessage($message);

?>


