<?php

require('request.php');

requireLvl(6);
$cityId = '3202';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['无锡地铁'] = [];

$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['station_name' => '', 'page_no' => 1, 'page_size' => 2000]), true)['result']['rows'];
foreach($stations as $station){
	if(!preg_match('/^(无锡.+站|.+高铁站)$/', $station['station_name'])){
		$station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
	}
	if($data['无锡地铁'][$station['station_name']]) continue;
	$toilets = [];
	$stationInfo = json_decode(request($cityId, 'bas/smartstation/v2/bas/station/detail', ['station_no' => $station['station_no'], 'service_id' => '01', 'train_plan_type' => '01,02,03', 'calendar_type' => '1']), true)['result'];
	foreach($stationInfo['device_list'] as $facility){
		if(preg_match('/^(洗手间|(普通)?卫生间)$/', $facility['device_name'])){
			foreach(explode("\n", $facility['description']) as $toilet){
				if($toilet){
					$toilets[] .= '［洗手间］'.$toilet;
				}
			}
		}
	}
	if(count($toilets)){
		$data['无锡地铁'][$station['station_name']] = implode("\n", $toilets);
	}else{
		$data['无锡地铁'][$station['station_name']] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['无锡地铁']).' 条数据');

?>
