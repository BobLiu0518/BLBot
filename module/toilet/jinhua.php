<?php

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/json',
		'content' => '{}',
	],
]);
$lines = json_decode(file_get_contents('https://app.jhmtr.net:65443/ht-app-support-news/app/news/station/getAllStation', false, $context), true)['lines'];
$stationDataApi = 'https://app.jhmtr.net:65443/ht-app-support-news/app/news/stationinside/infos';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['金华轨道交通'] = [];

foreach($lines as $line){
	foreach($line['stations'] as $station){
		if(!preg_match('/^(金华|义乌|横店)(东|南|西|北|高铁)?站$/', $station['stationName'])){
			$station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
		}
		if($data['金华轨道交通'][$station['stationName']]) continue;
		$toilets = [];
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode([
					'stationCode' => $station['stationCode'],
				]),
			],
		]);
		$stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['lineStationPeripheryList'][0];
		foreach($stationData['toiletList'] as $toilet){
			if($toilet['serviceName'] == '卫生间'){
				array_splice($toilets, -1, 0, explode('/', $toilet['address']));
			}
		}
		foreach($toilets as $id => $toilet){
			$toilets[$id] = '［卫生间］'.$toilet;
		}
		$data['金华轨道交通'][$station['stationName']] = implode("\n", $toilets);
	}
}
foreach($data['金华轨道交通'] as $stationName => $toilet){
	if(!$toilet){
		$data['金华轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['金华轨道交通']).' 条数据');

?>
