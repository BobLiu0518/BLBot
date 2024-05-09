<?php

requireLvl(6);
$lines = json_decode(file_get_contents('https://zhcx.km-metro.com/api/station_info/init'), true)['data']['stationSelectData'];
$stationDataApi = 'https://zhcx.km-metro.com/api/station_info/get_data?stationName=null&stationCode=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['昆明轨道交通'] = [];

foreach($lines as $line){
	foreach($line['children'] as $station){
		if(!preg_match('/^.+(汽车站|火车.*站)$/', $station['label'])){
			$station['label'] = preg_replace('/站$/', '', $station['label']);
		}
		if(!$data['昆明轨道交通'][$station['label']]){
			$data['昆明轨道交通'][$station['label']] = [];
		}
		$stationData = json_decode(file_get_contents($stationDataApi.$station['value']), true)['data'];
		foreach($stationData['stationServiceList'] as $stationService){
			if($stationService['serviceType'] == '4'){
				foreach($stationService['serviceInfos'] as $toilet){
					$data['昆明轨道交通'][$station['label']][] = '［'.$line['label'].'］'.$toilet['servicePosition'];
				}
			}
		}
	}
}
foreach($data['昆明轨道交通'] as $stationName => $toilet){
	if(!$toilet){
		$data['昆明轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['昆明轨道交通'][$stationName] = implode("\n", $data['昆明轨道交通'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['昆明轨道交通']).' 条数据');

?>
