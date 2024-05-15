<?php

requireLvl(6);

$lines = json_decode(file_get_contents('http://app.ccetravel.cn/invoices/subwayline/lines'), true)['lines'];
$stationInfoApi = 'http://app.ccetravel.cn/api-truetime/trainRunTime/v107?qtype=1&standcode=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['长春轨道交通'] = [];

foreach($lines as $line){
	foreach($line['standList'] as $station){
		if(!$data['长春轨道交通'][$station['cname']]){
			$data['长春轨道交通'][$station['cname']] = [];
		}
		$stationInfo = json_decode(file_get_contents($stationInfoApi.$station['standcode']), true);
		foreach($stationInfo['rimMap']['facilitiesInfo'] as $facility){
			if($facility['facilitiesno'] == '3'){
				$data['长春轨道交通'][$station['cname']][] = '［'.$line['cname'].'］'.$facility['facilitiesexplain'];
			}
		}
	}
}

foreach($data['长春轨道交通'] as $stationName => $toilets){
	$data['长春轨道交通'][$stationName] = count($toilets) ? implode("\n", $toilets) : '无数据，该站可能无卫生间';
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['长春轨道交通']).' 条数据');

?>
