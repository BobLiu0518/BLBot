<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://external-website.nsmetro.com/api/mp-api/line'), true)['result'];
$stationApi = 'https://external-website.nsmetro.com/api/mp-api/station';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['沈阳地铁'] = [];

foreach($lines as $line){
	$stations = json_decode(file_get_contents($stationApi.'?lineCode='.$line['lineCode']), true)['result'];
	foreach($stations as $station){
		if(!$data['沈阳地铁'][$station['stationName']]){
			$data['沈阳地铁'][$station['stationName']] = [];
		}
		$toilets = [];
		$stationData = json_decode(file_get_contents($stationApi.'/'.$station['id']), true)['result'];
		foreach($stationData['facilities'] as $facility){
			if($facility['facilityId'] == 16){
				array_splice($toilets, 0, 0, explode('、', $facility['facilityValue']));
			}
		}
		foreach($toilets as $id => $toilet){
			$toilets[$id] = '［'.preg_replace('/^地铁/', '', $line['lineName']).'］'.$toilet;
		}
		array_splice($data['沈阳地铁'][$station['stationName']], -1, 0, $toilets);
	}
}
foreach($data['沈阳地铁'] as $stationName => $toilet){
	if(!$toilet){
		$data['沈阳地铁'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['沈阳地铁'][$stationName] = implode("\n", $data['沈阳地铁'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['沈阳地铁']).' 条数据');

?>
