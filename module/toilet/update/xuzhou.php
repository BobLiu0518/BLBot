<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://mallmetroinfo.xzsmartmetro.com/Api/StationLines/StationLineTimes'), true)['Data'];
$stationInfoApi = 'https://mallmetroinfo.xzsmartmetro.com/Api/Stations/';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['徐州地铁'] = [];

foreach($lines as $line){
	if($line['Flag'] == 2) continue;
	foreach($line['StationTimes'] as $station){
		if(!$data['徐州地铁'][$station['StationName']]){
			$data['徐州地铁'][$station['StationName']] = [];
		}
		$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['StationId'].'/Around'), true)['Data'];
		$toilets = [];
		foreach($stationInfo['StationFacilties'] as $facility){
			if($facility['SubCategory'] == 104){
				$toilets = explode('、', str_replace('，', '、', $facility['Description']));
			}
		}
		foreach($toilets as $id => $toilet){
			if(preg_match('/(.+)（(.+号线)）/', $toilet, $match)){
				$toilets[$id] = '［'.$match[2].'］'.$match[1];
			}else{
				$toilets[$id] = '［'.$station['LineName'].'］'.$toilet;
			}
		}
		array_splice($data['徐州地铁'][$station['StationName']], -1, 0, $toilets);
	}
}

foreach($data['徐州地铁'] as $stationName => $toilet){
	if(!$toilet){
		$data['徐州地铁'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['徐州地铁'][$stationName] = implode("\n", array_unique($data['徐州地铁'][$stationName]));
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['徐州地铁']).' 条数据');

?>
