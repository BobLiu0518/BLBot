<?php

requireLvl(6);

$stations = json_decode(file_get_contents('https://m.shmetro.com/core/shmetro/mdstationinfoback_new.ashx?act=getAllStations'), true);
$stationInfoApi = 'https://m.shmetro.com/interface/metromap/metromap.aspx?func=stationInfo&stat_id=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['上海地铁'] = [];

foreach($stations as $station){
	$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['key']), true)[0];
	$toiletInfo = json_decode($stationInfo['toilet_position'], true)['toilet'];
	if($station['value'] == '浦电路') $station['value'] = '浦电路（4号线）';
	else if($station['value'] == '浦电路 ') $station['value'] = '浦电路（6号线）';
	$data['上海地铁'][$station['value']] = [];
	foreach($toiletInfo as $toiletInfoDetail){
		if($toiletInfoDetail['lineno'] == 41) $toiletInfoDetail['lineno'] = '浦江线';
		$data['上海地铁'][$station['value']][] .= '［'.(is_numeric($toiletInfoDetail['lineno']) ? $toiletInfoDetail['lineno'].'号线' : $toiletInfoDetail['lineno']).'］'.$toiletInfoDetail['description'];
	}
	$data['上海地铁'][$station['value']] = implode("\n", $data['上海地铁'][$station['value']]);
}
$data['上海地铁']['浦电路'] = 'Redirect=浦电路（4号线）&浦电路（6号线）';
$data['上海地铁']['黄陂南路'] = 'StationName=一大会址·黄陂南路';
$data['上海地铁']['新天地'] = 'StationName=一大会址·新天地';

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['上海地铁']).' 条数据');

?>
