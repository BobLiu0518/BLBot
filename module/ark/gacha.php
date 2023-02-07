<?php

function getPool($poolName){
	// 获取卡池数据
	$pools = json_decode(getData('ark/pool.json'), true);

	// 获取相应卡池
	if(!$poolName){
		$latest = 0;
		foreach($pools as $pool){
			if($pool['time'] > $latest){
				$latest = $pool['time'];
				$poolName = $pool['name'];
			}
		}
	}else if(!$pools[$poolName]){
		replyAndLeave('卡池不存在…');
	}

	$operators = json_decode(getData('ark/operator.json'), true);
	$pool = $pools[$poolName];
	foreach($operators as $operator){
		if($operator['type'] == 'other'){
			// 非抽卡干员
			continue;
		}else if($operator['type'] == 'limited' && $pool['type'] != 'limited'){
			// 限定干员 普池
			continue;
		}else if($operator['time'] > $pool['time']){
			// 时空战士
			continue;
		}else if($pool['type'] == 'special' && $operator['star'] >= 5){
			// 联合行动 高星
			continue;
		}else if($pool['operators'][$operator['star']]['except'] && in_array($operator['name'], $pool['operators'][$operator['star']]['except'])){
			// 排除列表
			continue;
		}else if($pool['operators'][$operator['star']]['up'] && in_array($operator['name'], $pool['operators'][$operator['star']]['up'])){
			// UP列表（无需再次添加）
			continue;
		}else{
			$pool['operators'][$operator['star']]['normal'][] = $operator['name'];
		}

		foreach($pool['operators'][$operator['star']]['other'] as $other){
			if($other['name'] == $operator['name']){
				for($n = 1; $n < $other['weight']; $n ++){
					// 按权重多次添加干员
					$pool['operators'][$operator['star']]['normal'][] = $operator['name'];
				}
			}
		}
	}

	return $pool;
}

function gacha($poolName, $times){
	global $Event;

	if($times > 10){
		return "最多抽十连哦…";
	}
	$pool = getPool($poolName);

	$userData = json_decode(getData('ark/user/'.$Event['user_id']), true);
	$image = getCache('ark/pool/'.$pool['name']);
	if(!$image){
		$image = file_get_contents($pool['image']);

		// 压缩一下 太大的会发不出去
		// 直接用 [CQ:image,url=×××] 也发不出
		// 很玄学 只能退而求其次了
		$Imagick = new Imagick();
		$Imagick->readImageBlob($image);
		$Imagick->setImageFormat('jpeg');
		$Imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
		$Imagick->setImageCompressionQuality(80);
		$image = $Imagick->getImageBlob();
		setCache('ark/pool/'.$pool['name'], $image);
	}
	$reply = sendImg($image);
	$reply .= $pool['name']." 寻访结果：\n";

	for($gacha = 0; $gacha < $times; $gacha += 1){
        	$star = '';
		$operatorDecided = false;

		// 计数
		$userData[$pool['name']]['counter'] += 1;
		if($pool['type'] == 'normal'){
			$userData['normal']['floor'] += 1;
		}else{
			$userData[$pool['name']]['floor'] += 1;
		}

		// 大保底判定
		foreach($pool['bonus'] as $n => $bonus){
			if($userData[$pool['name']]['counter'] == $bonus['counter'] && $userData[$pool['name']]['bonus'][$n] != true){
				$userData[$pool['name']]['bonus'][$n] = true;
				if($bonus['type'] == 'star'){
					$star = $bonus['star'];
				}else if($bonus['type'] == 'operator'){
					$operatorData = json_decode(getData('ark/operator.json'), true);
					$operator = $bonus['operator'];
					$star = $operatorData[$operator]['star'];
					$operatorDecided = true;
				}
			}
		}

		// 星级判定
		if(!$star){
			$r = rand(1, 100);
			$floor = ($pool['type'] == 'normal') ? $userData['normal']['floor'] : $userData[$pool['name']]['floor'];
			$fix = ($floor > 50) ? ($floor - 50) * 2 : 0;
			if($r <= 2 + $fix){
				$star = '6';
			}else if($r <= 10){
				$star = '5';
			}else if($r <= 60){
				$star = '4';
			}else{
				$star = '3';
			}
		}

		// 干员判定
		if(!$operatorDecided){
			$r = rand(1, 100);
			if($pool['operators'][$star]['up'] && ($r <= $pool['operators'][$star]['percentage'] || ($pool['type'] == 'special' && intval($star) >= 5))){
				// 没歪
				$operator = $pool['operators'][$star]['up'][array_rand($pool['operators'][$star]['up'], 1)];
			}else{
				// 歪了 / 没UP的
				$operator = $pool['operators'][$star]['normal'][array_rand($pool['operators'][$star]['normal'], 1)];
			}
		}

		// 发消息
		for($n = 6; $n > 0; $n --){
			if(intval($star) >= $n){
				$reply .= '★';
			}else{
				$reply .= '　';
			}
		}
		$reply .= ' '.$operator."\n";

		// 小保底检测
		if($star == '6'){
			if($pool['type'] == 'normal'){
				$userData['normal']['floor'] = 0;
			}else{
				$userData[$pool['name']]['floor'] = 0;
			}
		}

		// 大保底检测
		foreach($pool['bonus'] as $n => $bonus){
			if($bonus['type'] == 'star' && intval($star) >= intval($bonus['star']) && $userData[$pool['name']]['bonus'][$n] != true){
				$userData[$pool['name']]['bonus'][$n] = true;
			}else if($bonus['type'] == 'operator' && $operator == $bonus['operator'] && $userData[$pool['name']]['bonus'][$n] != true){
				$userData[$pool['name']]['bonus'][$n] = true;
			}
		}
	}

	// 大保底提示
	foreach($pool['bonus'] as $n => $bonus){
		if($userData[$pool['name']]['counter'] < $bonus['counter'] && $userData[$pool['name']]['bonus'][$n] != true){
			$reply .= ($bonus['counter'] - $userData[$pool['name']]['counter']).' 次内寻访内必得';
			$reply .= (($bonus['type'] == 'star') ? (' '.$bonus['star'].'★ 及以上干员') : ('干员 '.$bonus['operator']))."\n";
		}
	}

	// 小保底提示
	$reply .= (($pool['type'] == 'normal')?('标准寻访'):('“'.$pool['name'].'”')).'已连续 '.(($pool['type'] == 'normal') ? $userData['normal']['floor'] : $userData[$pool['name']]['floor']).' 次没有招募到 6★ 干员';
	setData('ark/user/'.$Event['user_id'], json_encode($userData));

	return $reply;
}

requireLvl(2, '模拟抽卡');

$poolName = nextArg();
$times = nextArg();

if(is_numeric($poolName) && !$times){
	$times = $poolName;
	$poolName = '';
}else if(!$times){
	$times = 1;
}

if(!rand(0, 100)){
	replyAndLeave('啊呜，你的寻访凭证被小刻吃掉了！');
}else{
	replyAndLeave(gacha($poolName, $times));
}

?>