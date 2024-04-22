<?php

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/json',
		'content' => json_encode(['stationName' => '']),
	],
]);
$stations = json_decode(file_get_contents('http://47.92.88.178:20089/api/app/lineStation/lineStationList', false, $context), true)['data'];
$stations = $stations[0]['stationList'];
$toiletsApi = 'http://47.92.88.178:20089/api/app/lineStation/restroomDetail';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['天津地铁'] = [];

foreach($stations as $station){
	$data['天津地铁'][$station['stationName']] = [];
	$context = stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => 'Content-Type: application/json',
			'content' => json_encode(['stationId' => $station['stationId']]),
		],
	]);
	$toilets = json_decode(file_get_contents($toiletsApi, false, $context), true)['data'];
	foreach($toilets as $toilet){
		if($toilet['classification'] == '0'){
			$data['天津地铁'][$station['stationName']][] = preg_replace('/(\S+)\——(\S+)/m', '［$1］$2', $toilet['address']);
		}
	}
	if(count($data['天津地铁'][$station['stationName']])){
		$data['天津地铁'][$station['stationName']] = implode("\n", $data['天津地铁'][$station['stationName']]);
	}else{
		$data['天津地铁'][$station['stationName']] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['天津地铁']).' 条数据');

?>
