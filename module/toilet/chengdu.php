<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://cdmetro.cnzhiyuanhui.com/op/stations'), true)['data'];
$stationInfoApi = 'https://cdmetro.cnzhiyuanhui.com/op/stations/';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['成都地铁'] = [];

foreach($lines['list'] as $line){
	foreach($line['subLine'] as $subLine){
		foreach($subLine['stationList'] as $station){
			if($data['成都地铁'][$station['stationName']]) continue;
			$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['stationNo']), true)['data'];
			foreach($stationInfo['facilities']['stationFacilities'] as $facility){
				if($facility['type'] == 'TOILET'){
					$data['成都地铁'][$station['stationName']] = preg_replace('/^\s*$\r?\n/m', '', preg_replace('/(\S+)\：(\S+)/m', '［$1］$2', str_replace('；', "\n", $facility['description'])));
				}
			}
			if(!$data['成都地铁'][$station['stationName']]){
				$data['成都地铁'][$station['stationName']] = '无数据，该站可能无卫生间';
			}
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['成都地铁']).' 条数据');

?>
