<?php

loadModule('toilet.update.request');

requireLvl(6);
$cityId = '3601';
$stations = json_decode(request($cityId, 'bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'service_id' => '01']), true)['result']['rows'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['南昌地铁'] = [];

foreach($stations as $station){
	$station['station_name'] = preg_replace('/（阳光寄存点）$/', '', $station['station_name']);
	if(!preg_match('/^南昌(火车|西)站$/', $station['station_name'])){
		$station['station_name'] = preg_replace('/站$/', '', $station['station_name']);
	}
	$data['南昌地铁'][$station['station_name']] = "暂不支持南昌地铁车站查询\n（未找到官方卫生间位置数据）";
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['南昌地铁']).' 条数据');

?>
