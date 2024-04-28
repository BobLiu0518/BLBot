<?php

requireLvl(6);

$html = file_get_contents('https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/stationInfo.shtml');
preg_match_all('/<li onclick="changeLine\(this\)" data-id="(\d+?)">(.+?)<\/li>/', $html, $match);
$lines = [];
foreach($match[2] as $id => $lineName){
	$lines[] = [
		'lineName' => $lineName,
		'lineId' => $match[1][$id],
	];
}
$stationsApi = 'https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/loadStationByLineId.shtml?ID=';
$stationDataPage = 'https://ckapp.0451dt.com/ditie/app/pub/station/AppStationInfo/stationInfomation.shtml?ID=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['哈尔滨地铁'] = [];

foreach($lines as $line){
	$stations = json_decode(file_get_contents($stationsApi.$line['lineId']), true);
	foreach($stations as $station){
		if(!preg_match('/^哈尔滨.*站$/', $station['STATION_NAME'])){
			$station['STATION_NAME'] = preg_replace('/站$/', '', $station['STATION_NAME']);
		}
		if($data['哈尔滨地铁'][$station['STATION_NAME']]) continue;
		$stationData = file_get_contents($stationDataPage.$station['ID']);
		preg_match('/<div id="tip_3" tip>(.|\n)+?<p>((.|\n)+?)<\/p>(.|\n)+?<\/div>/', $stationData, $match);
		if($match[2]){
			$data['哈尔滨地铁'][$station['STATION_NAME']] = '［洗手间］'.trim($match[2]);
		}else{
			$data['哈尔滨地铁'][$station['STATION_NAME']] = '无数据，该站可能无卫生间';
		}
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['哈尔滨地铁']).' 条数据');

?>
