<?php

requireLvl(6);

$stations = json_decode(file_get_contents('https://tjgateway.tymetro.ltd/manage/manage_station/listByCache'), true)['data'];
$stationDataApi = 'https://tjgateway.tymetro.ltd/manage/manage_station/getByStationNo?stationNo=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['太原地铁'] = [];

foreach($stations as $station){
	if(!preg_match('/^太原(东|南|西|北)?站(.+广场)?$/', $station['stationName'])){
		$station['stationName'] = preg_replace('/站$/', '', $station['stationName']);
	}
	$stationInfo = json_decode(file_get_contents($stationDataApi.$station['stationNo']), true)['data'];
	$data['太原地铁'][$station['stationName']] = $stationInfo['toilet'] ? '［卫生间］'.$stationInfo['toilet'] : '无数据，该站可能无卫生间';
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['太原地铁']).' 条数据');

?>
