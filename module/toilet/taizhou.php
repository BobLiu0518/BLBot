<?php

requireLvl(6);

$stations = json_decode(file_get_contents('https://www.tz-mtr.com/tzsgdjt/gdjt/api/sidebarTree/stationList'), true)['result'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['台州轨道交通'] = [];

foreach($stations as $station){
	$stationName = preg_replace('/站$/', '', $station['name']);
	preg_match('/^洗手间位置：(.+?);?$/m', $station['facility'], $match);
	if(!$match[1]){
		$data['台州轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['台州轨道交通'][$stationName] = '［卫生间］'.$match[1];
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['台州轨道交通']).' 条数据');

?>
