<?php

loadModule('toilet.update.request');

requireLvl(6);
$cityId = '3205';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['service_id' => '01', 'page_no' => '1', 'page_size' => '2000']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['苏州轨道交通'] = [];

foreach($stations as $station){
	$data['苏州轨道交通'][$station['station_name']] = "暂不支持苏州轨道交通车站查询\n（未找到官方卫生间位置数据）";
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['苏州轨道交通']).' 条数据');

?>
