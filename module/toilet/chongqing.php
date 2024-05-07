<?php

require('request.php');

requireLvl(6);
$cityId = '5000';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['重庆轨道交通'] = [];

foreach($stations as $station){
	$data['重庆轨道交通'][$station['station_name']] = [];
	$stationInfo = json_decode(request($cityId, 'bas/dict/v1/get-station', ['station_id' => $station['station_id']]), true)['result'];
	foreach($stationInfo['facilities'] as $facility){
		if($facility['fac_name'] == '卫生间'){
			$toilets = explode("\n", $facility['fac_desc']);
			foreach($toilets as $toilet){
				$data['重庆轨道交通'][$station['station_name']][] .= '［卫生间］'.preg_replace('/^\d+，/', '', $toilet);
			}
		}
	}
	if(!count($data['重庆轨道交通'][$station['station_name']])){
		$data['重庆轨道交通'][$station['station_name']] = '无数据，该站可能无卫生间';
	}else{
		$data['重庆轨道交通'][$station['station_name']] = implode("\n", $data['重庆轨道交通'][$station['station_name']]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['重庆轨道交通']).' 条数据');

?>
