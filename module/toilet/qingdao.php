<?php

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'content' => 'version=0',
	],
]);
$lines = json_decode(file_get_contents('https://api.qd-metro.com/ngstatic/station/toStation', false, $context), true)['data']['stationData'];
$stationDataApi = 'https://api.qd-metro.com/ngstatic/station/newStationInfo320';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['青岛地铁'] = [];

foreach($lines as $line){
	foreach($line['stationData'] as $station){
		if($data['青岛地铁'][$station['name']]){
			$data['青岛地铁'][$station['name']] = [$data['青岛地铁'][$station['name']]];
		}else{
			$data['青岛地铁'][$station['name']] = [];
		}
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'content' => 'version=0&stationId='.$station['id'],
			],
		]);
		$stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['data'];
		foreach($stationData['installation'] as $facility){
			if(preg_match('/卫生间/', $facility['name'])){
				array_splice($data['青岛地铁'][$station['name']], 0, 0, explode('、', $facility['address']));
			}
		}
		foreach($data['青岛地铁'][$station['name']] as $id => $toilet){
			$data['青岛地铁'][$station['name']][$id] = '［'.$line['lineName'].'］'.$toilet;
		}
		$data['青岛地铁'][$station['name']] = implode("\n", $data['青岛地铁'][$station['name']]);
	}
}
foreach($data['青岛地铁'] as $stationName => $toilet){
	if(!$toilet){
		$data['青岛地铁'][$stationName] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['青岛地铁']).' 条数据');

?>
