<?php

$stations = json_decode(file_get_contents('https://m.shmetro.com/core/shmetro/mdstationinfoback_new.ashx?act=getAllStations'), true);
$stationInfoApi = 'https://m.shmetro.com/interface/metromap/metromap.aspx?func=stationInfo&stat_id=';
$data = json_decode(getData('toilet/data.json'), true);
$data['上海'] = [];

foreach($stations as $station){
	$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['key']), true)[0];
	$toiletInfo = json_decode($stationInfo['toilet_position'], true)['toilet'];
	$data['上海'][$station['value']] = [];
	foreach($toiletInfo as $toiletInfoDetail){
		if($toiletInfoDetail['lineno'] == 41) $toiletInfoDetail['lineno'] = '浦江线';
		$data['上海'][$station['value']][] .= '【'.(is_numeric($toiletInfoDetail['lineno']) ? $toiletInfoDetail['lineno'].'号线' : $toiletInfoDetail['lineno'])."】".$toiletInfoDetail['description'];
	}
	$data['上海'][$station['value']] = implode("\n", $data['上海'][$station['value']]);
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['上海']).' 条数据');

?>
