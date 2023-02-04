<?php

function getList($poolName){
	// 获取卡池数据
	$pools = json_decode(getData('ark/pool.json'), true)['pools'];

	// 获取相应卡池
	if($poolName){
		foreach($pools as $n => $pool){
			if($pool['name'] == $poolName){
				$selected = $n;
			}
		}
	}else{
		$selected = count($pools) - 1;
	}
	if(!$selected) replyAndLeave('卡池不存在…');
	$operators = json_decode(getData('ark/operator.json'), true);
	$pool = array(
		"name" => $pools[$selected]['name'],
		"type" => $pools[$selected]['type'],
		"start" => $pools[$selected]['start']
	);

	// 三星都有
	foreach($operators['3'] as $op){
		$pool['3']['normal'][] = $op['name'];
	}
	// 其他星级需要考虑UP情况
	$starRanks = array('4', '5', '6');

	switch($pools[$selected]['type']){ // 把该放的人放在池子里
		case 'R6': // 彩六联动池
			// replyAndLeave('暂不支持彩虹六号联动寻访…');
			$pool['5']['up'] = array('霜华', '闪击');
			$pool['6']['up'] = array('灰烬');
		case 'normal': // 普通池
			foreach($starRanks as $starRank){ // 每个星级分别考虑
				foreach($operators[$starRank] as $op){ // 每个干员分别考虑
					if($op['type'] == 'normal' && ($op['time'] - 8*60*60) <= $pools[$selected]['start']){ // 这个干员该出现在这个池子里
						if((!$pools[$selected]['up'][$starRank]) || !in_array($op['name'], $pools[$selected]['up'][$starRank])){ // 不是UP
							$pool[$starRank]['normal'][] = $op['name'];
						}else{
							$pool[$starRank]['up'][] = $op['name'];
						}
					}
				}
			}
			break;
		case 'newYear': // 新年回首
			replyAndLeave('暂不支持跨年欢庆系列寻访…');
			break;
		case 'joint': // 联合寻访
			foreach($starRanks as $starRank){ // 每个星级分别考虑
				foreach($operators[$starRank] as $op){ // 每个干员分别考虑
					if($op['type'] == 'normal' && ($op['time'] - 8*60*60) <= $pools[$selected]['start']){ // 这个干员该出现在这个池子里
						if((!$pools[$selected]['up'][$starRank]) || !in_array($op['name'], $pools[$selected]['up'][$starRank])){ // 不是UP
							if($starRank != '5' && $starRank != '6'){ // 联合寻访只有三四星的非UP才在卡池里
								$pool[$starRank]['normal'][] = $op['name'];
							}
						}else{
							$pool[$starRank]['up'][] = $op['name'];
						}
					}
				}
			}
			break;
		case 'limited': // 限定池
			foreach($starRanks as $starRank){ // 每个星级分别考虑
				foreach($operators[$starRank] as $op){ // 每个干员分别考虑
					if(($op['type'] == 'normal' || in_array($op['name'], $pools[$selected]['up'][$starRank])) && ($op['time'] - 8*60*60) <= $pools[$selected]['start']){ // 这个干员该出现在这个池子里
						if((!$pools[$selected]['up'][$starRank]) || !in_array($op['name'], $pools[$selected]['up'][$starRank])){ // 不是UP
							$pool[$starRank]['normal'][] = $op['name'];
						}else{
							$pool[$starRank]['up'][] = $op['name'];
						}
					}
				}
			}
			foreach($pools[$selected]['up2']['6'] as $recentLimitedOp){
				for($n = 0; $n < 5; $n ++){
					$pool['6']['normal'][] = $recentLimitedOp;
				}
			}
			break;
		default:
			replyAndLeave('未知卡池类型：'.$pools[$selected]['type']);
			break;
	}

	setData('ark/pools/'.$pools[$selected]['name'], json_encode($pool));

	return $pool;
}

function gacha($poolName, $times){
	global $Event;

	if($times > 10) replyAndLeave("最多抽十连哦…");
	$list = getList($poolName);
	$reply = $list['name']." 寻访结果：\n";

	$floorSuffix = ($list['type'] == 'normal')?'':'.'.$list['start'];
	$floor = getData('ark/floor/'.$Event['user_id'].$floorSuffix);
	if(!$floor) $floor = 0;

	if($list['type'] == 'R6'){ // Ash保底
		$ashFloor = getData('ark/floor/'.$Event['user_id'].'.R6');
		if(!$ashFloor) $ashFloor = 0;
	}

	$fourStarCount = 0; // 四星保底
	for($n = 0; $n < $times; $n ++){
		$r = rand(1, 100);

		// 六星保底
		$floor ++;
		if($list['type'] == 'R6' && $ashFloor >= 0){
			$ashFloor ++;
		}

		if($floor >= 50){
			$fix = ($floor - 50) * 2;
		}else{
			$fix = 0;
		}

		// 算抽出几星
		if($r <= 2 + $fix){
			$star = '6';
			$floor = 0;
		}else if($r <= 10 + $fix){
			$star = '5';
		}else if($r <= 60){
			$star = '4';
		}else{
			$star = '3';
		}

		// 四星保底
		if($star != '3') $fourStarCount ++;
		else if($star == '3' && $fourStarCount == 9) $star = '4';

		if($list['type'] == 'R6' && $ashFloor == 120){
			$star = 6;
		}

		// 加入星级
		for($i = 6; $i > 0; $i --){
			if($i <= $star)
				$reply .= '★';
			else
				$reply .= '　';
		}
		$reply .= ' ';

		if($list[$star]['up']){	// 该星级下有UP干员
			$r = rand(1, 100);
			if($list['type'] == 'R6' && $ashFloor == 120){ // 彩六池120必出Ash
				$reply .= '灰烬';
				$ashFloor = -1;
			}else if((($list['type'] == 'normal' || $list['type'] == 'R6') && ($star != '4' && $r <= 50) || ($star == '4' && $r <= 20)) ||
			   ($list['type'] == 'limited' && ($star != '4' && $r <= 70) || ($star == '4' && $r <= 20)) ||
			   ($list['type'] == 'joint' && ($star == '5' || $star == '6'))){ // 抽到UP干员
				$reply .= $list[$star]['up'][rand(0, count($list[$star]['up']) - 1)];
				if($list['type'] == 'R6'){
					$ashFloor = -1;
				}
			}else{ // 歪了
				$reply .= $list[$star]['normal'][rand(0, count($list[$star]['normal']) - 1)];
			}
		}else{ // 该星级下没有UP干员
			$reply .= $list[$star]['normal'][rand(0, count($list[$star]['normal']) - 1)];
		}

		$reply .= "\n";
	}

	$reply .= (($list['type'] == 'normal')?('标准寻访'):('“'.$list['name'].'”'))."已连续 ".$floor." 次没有招募到6★干员";

	if($list['type'] == 'R6'){
		if($ashFloor > 0){
			$reply .= "\n".(120 - $ashFloor).' 次寻访内必得干员灰烬（仅一次）';
		}
		setData('ark/floor/'.$Event['user_id'].'.R6', $ashFloor);
		$reply .= "\n\n注：五星互保机制没做（别问，问就是懒）";
	}

	setData('ark/floor/'.$Event['user_id'].$floorSuffix, $floor);
	replyAndLeave($reply);
}

requireLvl(1);

$poolName = nextArg();
$times = nextArg();

if(is_numeric($poolName) && !$times){
	$times = $poolName;
	$poolName = '';
}else if(!$times){
	$times = 1;
}

if(!rand(0, 500)){
	replyAndLeave('啊呜，你的寻访凭证被小刻吃掉了！');
}else{
	replyAndLeave(gacha($poolName, $times));
}

?>
