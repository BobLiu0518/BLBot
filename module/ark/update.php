<?php

global $Queue;

if(php_sapi_name() == "cli"){
	function setData($route, $file){
		file_put_contents("../../storage/data/".$route, $file);
	}
	function replyMessage($msg){
		echo $msg."\n";
		return null;
	}
}else{
	requireMaster();
}

$operatorSearch = 'https://prts.wiki/w/%E7%89%B9%E6%AE%8A:%E8%AF%A2%E9%97%AE/-5B-5B%E5%88%86%E7%B1%BB:%E5%B9%B2%E5%91%98-5D-5D/mainlabel%3D/limit%3D500/offset%3D0/format%3Djson';
$poolSearch = 'https://prts.wiki/w/%E7%89%B9%E6%AE%8A:%E8%AF%A2%E9%97%AE/-5B-5B%E5%88%86%E7%B1%BB:%E5%AF%BB%E8%AE%BF%E6%A8%A1%E6%8B%9F-5D-5D/mainlabel%3D/limit%3D500/offset%3D0/format%3Djson';
$detailSearch = 'https://prts.wiki/index.php?action=raw&title=';
$imageSearch = 'https://prts.wiki/api.php?action=query&format=json&list=allimages&aisort=name&ailimit=1&aiprop=url&aifrom=';

$operatorData = json_decode(file_get_contents($operatorSearch), true);
$poolData = json_decode(file_get_contents($poolSearch), true);
$operators = $pools = $ids = [];

foreach($operatorData['results'] as $operator){
	echo 'Getting operator data '.$operator['fulltext']."\n";
	$operatorDetail = file_get_contents($detailSearch.urlencode($operator['fulltext']));
	preg_match('/\|干员名=(.+)/', $operatorDetail, $operatorName);
	preg_match('/\|干员序号=(-?\d+)/', $operatorDetail, $operatorId);
	preg_match('/\|上线时间=(\d+)-(\d+)-(\d+)/', $operatorDetail, $operatorTime);
	preg_match('/\|稀有度=(\d+)/', $operatorDetail, $operatorStar);
	preg_match('/\|获得方式=(.+)/', $operatorDetail, $operatorObtainMethods);
	$limited = false;
	$gachaable = false;
	foreach(explode(' ', $operatorObtainMethods[1]) as $operatorObtainMethod){
		switch($operatorObtainMethod){
			case '限定寻访':
				$limited = true;
				$gachaable = true;
				break;
			case '标准寻访':
				$gachaable = true;
				break;
			case '无':
			case '主线剧情':
			case '公开招募':
			case '活动获得':
			case '预约奖励':
			case '周年奖励':
			case '信用交易所':
			case '凭证交易所':
			case '记录修复奖励':
			case '见习任务第八阶段奖励':
				break;
		}
	}
	$operators[$operatorName[1]]= [
		'name' => $operatorName[1],
		'id' => intval($operatorId[1]),
		'time' => intval($operatorTime[1].$operatorTime[2].$operatorTime[3]),
		'star' => strval(intval($operatorStar[1]) + 1),
		'type' => ($gachaable ? ($limited ? 'limited' : 'normal') : 'other')
	];
	$ids[$operatorId[1]] = $operatorName[1];
}
setData('ark/operator.json', json_encode($operators));
$Queue[]= replyMessage('更新干员数据成功');

foreach($poolData['results'] as $pool){
	$poolName = str_replace('寻访模拟/', '', $pool['fulltext']);
	echo 'Getting pool data '.$poolName."\n";
	$pools[$poolName] = [
		'name' => $poolName,
		'type' => 'normal',
		'bonus' => [['type' => 'star', 'star' => '5', 'counter' => 10]],
		'operators' => [
			'6' => ['percentage' => 50, 'up' => [], 'except' => [], 'other' => []],
			'5' => ['percentage' => 50, 'up' => [], 'except' => [], 'other' => []],
			'4' => ['percentage' => 50, 'up' => [], 'except' => [], 'other' => []],
			'3' => ['percentage' => 50, 'up' => [], 'except' => [], 'other' => []]
		]
	];

	$poolDetail = file_get_contents($detailSearch.urlencode($pool['fulltext']));
	if(preg_match('/{{寻访模拟器\|卡池参数=(.+)}}/', str_replace("\n", ';', $poolDetail), $poolConfig)){
		// 非自动设定的卡池
		foreach(explode(';', $poolConfig[1]) as $configCategory){
			$configItems = explode('>', $configCategory);
			switch($configItems[0]){
				case 'config':
					foreach(explode(',', $configItems[1]) as $configItem){
						$config = explode(':', $configItem);
						switch($config[0]){
							case 'gacha_type':
								$pools[$poolName]['type'] = $config[1];
								break;
							case 'operator_end_time':
								$pools[$poolName]['time'] = intval($config[1]);
								break;
							case 'gacha_image':
								$pools[$poolName]['image'] = str_replace('http://', 'https://', json_decode(file_get_contents($imageSearch.urlencode($config[1])), true)['query']['allimages'][0]['url']);
								break;
							case 'bonus_data':
								$pools[$poolName]['bonus'] = [];
								foreach(explode('.', $config[1]) as $n => $bonusItem){
									if(strstr($bonusItem, '#')){
										$pools[$poolName]['bonus'][] = ['type' => 'operator', 'operator' => explode('#', $bonusItem)[1], 'counter' => intval(explode('#', $bonusItem)[0])];
										if(is_numeric($pools[$poolName]['bonus'][$n]['operator'])){
											$pools[$poolName]['bonus'][$n]['operator'] = $ids[$pools[$poolName]['bonus'][$n]['operator']];
										}
									}else if(strstr($bonusItem, '@')){
										$pools[$poolName]['bonus'][] = ['type' => 'star', 'star' => strval(intval(explode('@', $bonusItem)[1]) + 1), 'counter' => intval(explode('@', $bonusItem)[0])];
									}
								}
								break;
							case 'max_time':
								break;
							case 'cost_per_draw':
								break;
							case null:
								break;
							default:
								$Queue[]= replyMessage($poolName.'中非预期的卡池配置项：'.$config[0]);
								break;
						}
					}
					continue 2;
				case 'star_5':
					$star = '6';
					break;
				case 'star_4':
					$star = '5';
					break;
				case 'star_3':
					$star = '4';
					break;
				case 'star_2':
					$star = '3';
					break;
				case null:
					continue 2;
				default:
					$Queue[]= replyMessage($poolName.'中非预期的卡池配置项：'.$configItems[0]);
					continue 2;
			}
			foreach(explode(',', $configItems[1]) as $operatorConfig){
				$config = explode(':', $operatorConfig);
				switch($config[0]){
					case 'percent':
						$pools[$poolName]['operators'][$star]['percentage'] = intval($config[1]);
						break;
					case 'up':
					case 'except':
						if($config[1] == 'none'){
							$pools[$poolName]['operators'][$star][$config[0]] = [];
							break;
						}
						$pools[$poolName]['operators'][$star][$config[0]] = explode('.', trim($config[1], '.'));
						foreach($pools[$poolName]['operators'][$star][$config[0]] as $n => $operator){
							if(is_numeric($operator)){
									$pools[$poolName]['operators'][$star][$config[0]][$n] = $ids[$operator];
							}
						}
						break;
					case 'other':
						if($config[1] == 'none'){
							$pools[$poolName]['operators'][$star]['other'] = [];
							break;
						}
						foreach(explode('.', trim($config[1], '.')) as $n => $operator){
							$pools[$poolName]['operators'][$star]['other'][]= ['name' => explode('@', $operator)[0], 'weight' => intval(explode('@', $operator)[1])];
							if(is_numeric($pools[$poolName]['operators'][$star]['other'][$n]['name'])){
								$pools[$poolName]['operators'][$star]['other'][$n]['name'] = $ids[$pools[$poolName]['operators'][$star]['other'][$n]['name']];
							}
						}
						break;
					case null:
						break;
					default:
						$Queue[]= replyMessage($poolName.'中非预期的卡池配置项：'.$config[0]);
						break;
				}
			}
		}

	}else if(preg_match('/{{寻访模拟器\/自动设定\|(.+)}}/', str_replace("\n", ';', $poolDetail), $poolConfig)){
		// 自动设定的卡池
		$suffix = 'jpg';
		$poolTime = $poolType = null;
		foreach(explode('|', $poolConfig[1]) as $n => $item){
			if($n == 0){
				$fileName = $item;
				if($fileName == '{{SUBPAGENAME}}'){
					$fileName = $poolName;
				}
			}else{
				switch(explode('=', $item)[0]){
					case 'suffix':
						$suffix = explode('=', $item)[1];
						break;
					case 'gacha_image':
						$fileName = explode('=', $item)[1];
						break;
					case 'gacha_type':
						$poolType = explode('=', $item);
						break;
					case 'operator_end_time':
						$poolTime = explode('=', $item);
						break;
					default:
						$Queue[]= replyMessage($poolName.'中非预期的卡池配置项：'.$item[0]);
						break;
				}
			}
		}
		$poolDetail = file_get_contents($detailSearch.urlencode('文件:'.$fileName).'.'.$suffix);
		if(!$poolTime){
			preg_match('/\|寻访开启时间cn=(\d+)-(\d+)-(\d+)/', $poolDetail, $poolTime);
		}
		if(!$poolType){
			preg_match('/\|寻访类型=(.+)/', $poolDetail, $poolType);
		}
		$pools[$poolName]['type'] = (($poolType[1] == '标准寻访' || $poolType[1] == '常驻标准寻访') ? 'normal' : 'limited');
		$pools[$poolName]['time'] = intval($poolTime[1].$poolTime[2].$poolTime[3]);
		$pools[$poolName]['image'] = str_replace('http://', 'https://', json_decode(file_get_contents($imageSearch.urlencode($fileName.'.'.$suffix)), true)['query']['allimages'][0]['url']);
		preg_match('/\|出率提升干员=(.+)/', $poolDetail, $upOperators);
		foreach(explode(',', $upOperators[1]) as $operator){
			if(is_numeric($operator)){
				$operator = $ids[$operator];
			}
			$pools[$poolName]['operators'][$operators[$operator]['star']]['up'][]= $operator;
		}
	}else{
		$Queue[]= replyMessage('无法解析卡池：'.$poolName);
	}
}
setData('ark/pool.json', json_encode($pools));

$Queue[]= replyMessage('更新卡池数据成功');

?>
