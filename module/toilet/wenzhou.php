<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://app.wzmtr.com:6443/inner-service/line/searchLineList'), true)['data'];
$lineDataApi = 'https://app.wzmtr.com:6443/inner-service/siteDetails/searchSiteList?lineNo=';
$stationDataApi = 'https://app.wzmtr.com:6443/inner-service/siteDetails/searchStationInfo?siteId=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['温州轨道交通'] = [];

foreach($lines as $line){
	$stations = json_decode(file_get_contents($lineDataApi.$line['lineNo']), true)['data'];
	foreach($stations as $station){
		$station['name'] = preg_replace('/站$/', '', $station['name']);
		if(!$data['温州轨道交通'][$station['name']]){
			$data['温州轨道交通'][$station['name']] = [];
		}
		$stationData = json_decode(file_get_contents($stationDataApi.$station['staNo']), true)['data'];
		foreach($stationData['stationFacility'] as $facility){
			if(preg_match('/(洗手间|卫生间)/', $facility['facilityName'])){
				$data['温州轨道交通'][$station['name']][] = '［'.$line['name'].'］'.$facility['facilityLocation'];
			}
		}
	}
}
foreach($data['温州轨道交通'] as $stationName => $toilet){
	if(!count($toilet)){
		$data['温州轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['温州轨道交通'][$stationName] = implode("\n", $data['温州轨道交通'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['温州轨道交通']).' 条数据');

?>
