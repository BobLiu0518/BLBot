<?php

requireLvl(6);
$context = stream_context_create([
	'http' => ['method' => 'POST'],
]);
$lines = json_decode(file_get_contents('https://advh5.whrtmpay.com/siteInfo/getLineAndStation', false, $context), true)['rtData'];
$facilitiesApi = 'https://advh5.whrtmpay.com/siteInfo/getStationFacility';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['武汉地铁'] = [];

foreach($lines as $line){
	foreach($line['stationInfos'] as $station){
		if($data['武汉地铁'][$station['siteName']]) continue;
		else $data['武汉地铁'][$station['siteName']] = [];
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode(['siteCode' => $station['siteCode']]),
			],
		]);
		$facilities = json_decode(file_get_contents($facilitiesApi, false, $context), true)['rtData'];
		foreach($facilities as $facility){
			if($facility['toiletPublic']){
				array_splice($data['武汉地铁'][$station['siteName']], 0, 0, explode('，', $facility['toiletPublic']));
			}
		}
		if(count($data['武汉地铁'][$station['siteName']])){
			$data['武汉地铁'][$station['siteName']] = array_unique($data['武汉地铁'][$station['siteName']]);
			sort($data['武汉地铁'][$station['siteName']]);
			foreach($data['武汉地铁'][$station['siteName']] as $id => $toilet){
				$data['武汉地铁'][$station['siteName']][$id] = '［卫生间］'.$toilet;
			}
			$data['武汉地铁'][$station['siteName']] = implode("\n", $data['武汉地铁'][$station['siteName']]);
		}else{
			$data['武汉地铁'][$station['siteName']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['武汉地铁']).' 条数据');

?>
