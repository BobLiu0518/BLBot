<?php

requireLvl(6);

$context = stream_context_create([
	'http' => [
		'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
	],
]);
$lines = json_decode(file_get_contents('https://www.czmetro.net.cn/Html/Admin/Web/Station/getStationList', false, $context), true)['data'];
$stationInfoApi = 'https://www.czmetro.net.cn/Html/Admin/Web/Station/getStationDetail?station_id=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['常州地铁'] = [];
foreach($lines as $line){
	foreach($line['station_data'] as $station){
		$station['name'] = preg_replace('/站$/', '', $station['name']);
		$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['id'], false, $context), true)['data'];
		$data['常州地铁'][$station['name']] = ($data['常州地铁'][$station['name']] ? $data['常州地铁'][$station['name']]."\n" : '').'［'.$stationInfo['line_name'].'］'.($stationInfo['wc_position'] ?? '无数据，该站可能无卫生间');
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['常州地铁']).' 条数据');

?>
