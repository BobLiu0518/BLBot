<?php

requireLvl(6);

$stations = json_decode(file_get_contents('https://metroinfo.ditiego.net/Api/StationLines/GetStationList', false, $context), true)['Data'];
$stationInfoApi = 'https://metroinfo.ditiego.net/Api/Stations/';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['宁波地铁'] = [];

foreach($stations as $station){
	if($data['宁波地铁'][$station['StationName']]) continue;
	$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['Id'].'/Around'), true)['Data'];
	$toilets = [];
	foreach($stationInfo['StationFacilties'] as $facility){
		if($facility['SubCategory'] == 104){
			$toilets = explode('，', $facility['Description']);
		}
	}
	if(!count($toilets)){
		$data['宁波地铁'][$station['StationName']] = '无数据，该站可能无卫生间';
	}else{
		foreach($toilets as $id => $toilet){
			$toilets[$id] = '［卫生间］'.$toilet;
		}
		$data['宁波地铁'][$station['StationName']] = implode("\n", $toilets);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['宁波地铁']).' 条数据');

?>
