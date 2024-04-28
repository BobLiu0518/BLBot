<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://app.jngdjt.cn:8889/app/appTicketLineController/queryAllMapLines'), true)['map'];
$stationDataApi = 'https://app.jngdjt.cn:8889/app/stationController/getLineStationDetailInfoAPP';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['济南轨道交通'] = [];

foreach($lines['lines'] as $line){
	foreach($line['stations'] as $station){
		if($data['济南轨道交通'][$station['name']]) continue;
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode([
					'stationCode' => $station['stationCode']
				]),
			],
		]);
		$stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['lineStationPeripheryList'][0];
		foreach($stationData['peripheralServiceList'] as $facility){
			if(preg_match('/卫生间/', $facility['serviceName'])){
				$data['济南轨道交通'][$station['name']] = '［卫生间］'.$facility['address'];
			}
		}
		if(!$data['济南轨道交通'][$station['name']]){
			$data['济南轨道交通'][$station['name']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['济南轨道交通']).' 条数据');

?>
