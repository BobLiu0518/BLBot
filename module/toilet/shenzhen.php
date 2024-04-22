<?php

requireLvl(6);

$lineInfoUrl = json_decode(file_get_contents('https://szmc-intapi.shenzhenmc.com/szmc-mtas/baseCfg/queryObsUrlByDataVersion'), true)['data']['url'];
$lineInfo = json_decode(file_get_contents($lineInfoUrl), true)['data'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['深圳地铁'] = [];

foreach($lineInfo as $line){
	foreach($line['stationVoCol'] as $station){
		if($data['深圳地铁'][$station['stationName']]) continue;;
		foreach($station['facilityCol'] as $facility){
			if($facility['facilityCategoryId'] == '0'){
				$data['深圳地铁'][$station['stationName']] = $facility['location'];
			}
		}
		if(!$data['深圳地铁'][$station['stationName']]){
			$data['深圳地铁'][$station['stationName']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['深圳地铁']).' 条数据');

?>
