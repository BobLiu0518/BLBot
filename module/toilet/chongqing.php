<?php

require('request.php');

requireLvl(6);
$cityId = '5000';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['重庆轨道交通'] = [];
$dataOld = [];

foreach($stations as $station){
	if(!in_array($station['station_name'], ['重庆西站'])){
		preg_replace('/站$/', '', $station['station_name']);
	}
	$toilets = [];
	$stationInfo = json_decode(request($cityId, 'bas/dict/v1/get-station', ['station_id' => $station['station_id']]), true)['result'];
	foreach($stationInfo['facilities'] as $facility){
		if($facility['fac_name'] == '卫生间'){
			foreach(explode("\n", $facility['fac_desc']) as $toilet){
				$toilets[] .= '［卫生间］'.preg_replace('/^\d+，/', '', $toilet).'（旧版数据）';
			}
		}
	}
	if(count($toilets)){
		$dataOld[$station['station_name']] = implode("\n", $toilets);
	}
}

$lines = json_decode(request($cityId, 'bas/smartstation/v1/bas/line/list', ['service_id' => '01']), true)['result'];
foreach($lines as $line){
	$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 200, 'line_no' => $line['line_no'], 'service_id' => '01']), true)['result']['rows'];
	foreach($stations as $station){
		if($data['重庆轨道交通'][$station['station_name']]) continue;
		$toilets = $dataOld[$station['station_name']] ? [$dataOld[$station['station_name']]] : [];
		$stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => $station['station_no'], 'service_id' => '01', 'train_plan_type' => '01,02,03']), true)['result'];
		foreach($stationInfo['device_list'] as $facility){
			if($facility['device_name'] == '卫生间'){
				foreach(explode("\n", $facility['description']) as $toilet){
					$toilets[] .= '［卫生间］'.$toilet;
				}
			}
		}
		if(count($toilets)){
			$data['重庆轨道交通'][$station['station_name']] = implode("\n", $toilets);
		}else{
			$data['重庆轨道交通'][$station['station_name']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['重庆轨道交通']).' 条数据');

?>
