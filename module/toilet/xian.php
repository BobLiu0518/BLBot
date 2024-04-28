<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://xm-cdn.oss-cn-hangzhou.aliyuncs.com/json/stationData.json', false, $context), true)['data'];
$stationDataApi = 'https://xadt.i-xiaoma.com.cn/api/v2/app/stationInfo';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['西安地铁'] = [];

foreach($lines as $line){
	foreach($line['lineStationList'] as $station){
		if(!$data['西安地铁'][$station['stationName']]){
			$data['西安地铁'][$station['stationName']] = [];
		}
		$toilets = [];
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode(['stationId' => $station['stationId']]),
			],
		]);
		$stationData = json_decode(file_get_contents($stationDataApi, false, $context), true)['data'];
		foreach($stationData['facility'] as $facility){
			if(preg_match('/卫生间/', $facility['facilityName'])){
				array_splice($toilets, 0, 0, explode('；', $facility['facilityDesc']));
			}
		}
		foreach($toilets as $id => $toilet){
			$toilets[$id] = '［'.$line['lineName'].'］'.$toilet;
		}
		array_splice($data['西安地铁'][$station['stationName']], -1, 0, $toilets);
	}
}
foreach($data['西安地铁'] as $stationName => $toilet){
	if(!$toilet){
		$data['西安地铁'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['西安地铁'][$stationName] = implode("\n", $data['西安地铁'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['西安地铁']).' 条数据');

?>
