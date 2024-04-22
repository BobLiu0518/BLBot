<?php

requireLvl(6);

// 该 API 需要验证 sign，尚不清楚计算机制
// $lines = json_decode(file_get_contents('https://96123.bmncc.com.cn/bjtt-subway-app/api/baseline/queryAllLinesChildStations'), true)['r'];
$lines = json_decode(getCache('toilet/beijingStations.json'), true)['r'];
$lines = json_decode(base64_decode($lines), true)['result'];
$stationInfoApi = 'https://96123.bmncc.com.cn/bjtt-subway-app/api/inside/info';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['北京轨道交通'] = [];

foreach($lines as $line){
	foreach($line['listStations'] as $station){
		if($data['北京轨道交通'][$station['stationNameCn']]) continue;
		$data['北京轨道交通'][$station['stationNameCn']] = [];
		$options = [
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode(['stationName' => $station['stationNameCn']]),
			],
		];
		$context = stream_context_create($options);
		$stationInfo = json_decode(file_get_contents($stationInfoApi, false, $context), true)['r'];
		$stationInfo = json_decode(base64_decode($stationInfo), true)['result'];
		foreach($stationInfo['insideInfoList'] as $insideInfo){
			if($insideInfo['insideCode'] == 'SF_TOILET'){
				foreach($insideInfo['insideInfoDesc'] as $insideInfoDesc){
					$data['北京轨道交通'][$station['stationNameCn']][] = '［'.$insideInfoDesc['lineName'].'］'.preg_replace('/(\n|\r)+/', '；', trim($insideInfoDesc['insideDesc']));
				}
			}
		}
		if(!count($data['北京轨道交通'][$station['stationNameCn']])) $data['北京轨道交通'][$station['stationNameCn']] = '无数据，该站可能无卫生间';
		else $data['北京轨道交通'][$station['stationNameCn']] = implode("\n", $data['北京轨道交通'][$station['stationNameCn']]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['北京轨道交通']).' 条数据');

?>
