<?php

requireLvl(6);
$lines = json_decode(file_get_contents('https://oms-gateway.lzgdjt.com/manage/manage_line/list'), true)['data'];
$stationsApi = 'https://oms-gateway.lzgdjt.com/manage/manage_station/listByCache?lineNo=';
$stationDataApi = 'https://oms-gateway.lzgdjt.com/manage/manage_station/getByStationNo?stationNo=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['兰州轨道交通'] = [];

foreach($lines as $line){
	$stations = json_decode(file_get_contents($stationsApi.$line['lineNo']), true)['data'];
	foreach($stations as $station){
		if(!preg_match('/^.+火车站$/', $station['stationName'])){
			$station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
		}
		if(!$data['兰州轨道交通'][$station['stationName']]){
			$data['兰州轨道交通'][$station['stationName']] = [];
		}
		$stationData = json_decode(file_get_contents($stationDataApi.$station['stationNo']), true)['data'];
		if($stationData['toilet']){
			$data['兰州轨道交通'][$station['stationName']][] = '［'.$line['lineName'].'］'.$stationData['toilet'];
		}
	}
}
foreach($data['兰州轨道交通'] as $stationName => $toilet){
	if(!$toilet){
		$data['兰州轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['兰州轨道交通'][$stationName] = implode("\n", $data['兰州轨道交通'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['兰州轨道交通']).' 条数据');

?>
