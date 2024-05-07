<?php

require('request.php');

requireLvl(6);
$cityId = '3301';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['杭州地铁'] = [];
$dataOld = [];

$lines = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/line/list', ['service_id' => '01', 'city_id' => $cityId]), true)['result'];
foreach($lines as $line){
	$stations = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'line_no' => $line['line_no'], 'service_id' => '01', 'city_id' => $cityId]), true)['result']['rows'];
	foreach($stations as $station){
		if($data['杭州地铁'][$station['station_name']]) continue;
		$toilets = $dataOld[$station['station_name']] ? [$dataOld[$station['station_name']]] : [];
		$stationInfo = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/detail', ['station_no' => $station['station_no'], 'service_id' => '01', 'map_type' => '01', 'train_plan_type' => '01,02,03', 'city_id' => $cityId]), true)['result'];
		foreach($stationInfo['device_list'] as $facility){
			if($facility['device_name'] == '卫生间'){
				foreach(explode("\n", $facility['description']) as $toilet){
					$toilets[] .= '［卫生间］'.$toilet;
				}
			}
		}
		if(count($toilets)){
			$data['杭州地铁'][$station['station_name']] = implode("\n", $toilets);
		}else{
			$data['杭州地铁'][$station['station_name']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['杭州地铁']).' 条数据');

?>
