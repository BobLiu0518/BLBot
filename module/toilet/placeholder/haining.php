<?php

loadModule('toilet.update.request');

requireLvl(6);
$cityId = '3301';
$realCityId = '3304';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['海宁轨道'] = [];
$dataOld = [];

$lines = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/line/list', ['service_id' => '01', 'city_id' => $realCityId]), true)['result'];
foreach($lines as $line){
	$stations = json_decode(request($cityId, 'v2/bas/smartstation/v1/bas/station/list', ['page_no' => 1, 'page_size' => 2000, 'line_no' => $line['line_no'], 'service_id' => '01', 'city_id' => $realCityId]), true)['result']['rows'];
	foreach($stations as $station){
		$data['海宁轨道'][$station['station_name']] = "暂不支持海宁轨道车站查询\n（未找到官方卫生间位置数据）";
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['海宁轨道']).' 条数据');

?>
