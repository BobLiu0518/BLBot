<?php

require('request.php');

requireLvl(6);
$cityId = '1301';
$stations = json_decode(request($cityId, 'bas/dict/v1/query-stations-lines', ['page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['石家庄轨道交通'] = [];

foreach($stations as $station){
	if(!preg_match('/^石家庄(东)?站$/', $station['station_name'])){
		$station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
	}
	$toilets = [];
	$stationInfo = json_decode(request($cityId, 'bas/others/v1/pis/flow/congestion/and/schedule/station', ['service_id' => '01', 'station_id' => $station['station_id']]), true)['result']['dict_station_info'];
	foreach($stationInfo['facilities'] as $facility){
		if($facility['fac_name'] == '卫生间'){
			foreach(explode('；', $facility['fac_desc']) as $toilet){
				if(preg_match('/^(.+)：(.+)$/', $toilet, $match)){
					$toilets[] .= '［'.$match[1].'］'.$match[2];
				}else{
					$toilets[] .= '［卫生间］'.$toilet;
				}
			}
		}
	}
	if(count($toilets)){
		$data['石家庄轨道交通'][$station['station_name']] = implode("\n", $toilets);
	}else{
		$data['石家庄轨道交通'][$station['station_name']] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['石家庄轨道交通']).' 条数据');

?>
