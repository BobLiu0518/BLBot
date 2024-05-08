<?php

require('request.php');

requireLvl(6);
$cityId = '4103';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['station_name' => '', 'page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['洛阳轨道交通'] = [];
$dataOld = [];

foreach($stations as $station){
	if(!preg_match('/(火车|高铁)站$/', $station['station_name'])){
		$station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
	}
	$toilets = [];
	$stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => explode(',', $station['station_no'])[0], 'service_id' => '01', 'train_plan_type' => '01,02,03', 'calendar_type' => '1,6']), true)['result'];
	foreach($stationInfo['device_list'] as $facility){
		if($facility['device_name'] == '卫生间'){
			foreach(explode('；', $facility['description']) as $toilet){
				$toilets[] .= '［卫生间］'.trim($toilet);
			}
		}
	}
	if(count($toilets)){
		$data['洛阳轨道交通'][$station['station_name']] = implode("\n", $toilets);
	}else{
		$data['洛阳轨道交通'][$station['station_name']] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['洛阳轨道交通']).' 条数据');

?>
