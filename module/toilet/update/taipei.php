<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);
$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/json',
		'content' => json_encode([
			'LineID' => '0',
			'Lang' => 'tw',
		]),
	],
]);
$lines = json_decode(file_get_contents('https://web.metro.taipei/apis/metrostationapi/menuline', false, $context), true);
$stationDataApi = 'https://web.metro.taipei/apis/metrostationapi/stationdetail';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['臺北捷運'] = [];

foreach($lines as $line){
	foreach($line['LineStations'] as $station){
		if($data['臺北捷運'][$station['StationName']]) continue;
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode([
					'SID' => $station['SID'],
					'Lang' => 'tw',
				]),
			],
		]);
		$stationData = json_decode(file_get_contents($stationDataApi, false, $context), true);
		$toilets = [];
		$belongingLine = null;
		foreach(explode('<br>', $stationData['stationInfo']['Restroom']) as $toilet){
			$toilet = trim($toilet);
			if($toilet){
				if(preg_match('/^(.+線)：$/', $toilet, $match)){
					$belongingLine = $match[1];
				}else{
					$toilets[] = '［'.($belongingLine ?? '廁所').'］'.$toilet;
				}
			}
		}
		$data['臺北捷運'][$station['StationName']] = implode("\n", $toilets);
		if(OpenCC::tw2s($station['StationName']) != $station['StationName']){
			$data['臺北捷運'][OpenCC::tw2s($station['StationName'])] = 'Redirect='.$station['StationName'];
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['臺北捷運']).' 条数据');

?>
