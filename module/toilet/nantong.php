<?php

requireLvl(6);

$lines = json_decode(file_get_contents('https://service.ntrailway.com/api/ntopen/ignoreGateway/line.common/treeList'), true)['data']['data'];
$lineApi = 'https://service.ntrailway.com/api/ntopen/ignoreGateway/listByParentId/';
$facilityApi = 'https://service.ntrailway.com/api/ntopen/ignoreGateway/select/siteInformation/convenienceFacility?stationId=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['南通轨道交通'] = [];

foreach($lines as $line){
	$stations = json_decode(file_get_contents($lineApi.$line['id']), true)['data']['data'];
	foreach($stations as $station){
		if(!preg_match('/^(南通|汽车|火车)+(东|南|西|北)?站$/', $station['dictName'])){
			$station['dictName'] = preg_replace('/站$/', '', $station['dictName']);
		}
		if(!$data['南通轨道交通'][$station['dictName']]){
			$data['南通轨道交通'][$station['dictName']] = [];
		}
		$facilities = json_decode(file_get_contents($facilityApi.$station['id']), true)['data'][0];
		if($facilities['restRoom']){
			$data['南通轨道交通'][$station['dictName']][] = '［'.$line['dictName'].'］'.$facilities['restRoom'];
		}
	}
}
foreach($data['南通轨道交通'] as $stationName => $toilet){
	if(!count($toilet)){
		$data['南通轨道交通'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['南通轨道交通'][$stationName] = implode("\n", $data['南通轨道交通'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['南通轨道交通']).' 条数据');

?>
