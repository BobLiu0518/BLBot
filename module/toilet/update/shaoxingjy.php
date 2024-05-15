<?php

requireLvl(6);

$stations = json_decode(file_get_contents('http://www.jymetro.com.cn/index/site/getsites.html?lineid=1'), true)['data'];
$stationDataPage = 'http://www.jymetro.com.cn/index/site/info.html?id=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['绍兴京越地铁'] = [];

foreach($stations as $station){
	$station['title'] = preg_replace('/站$/', '', $station['title']);
	$stationData = file_get_contents($stationDataPage.$station['id']);
	preg_match('/<p>卫生间<\/p><p>(.+?)<\/p>/', $stationData, $match);
	if($match[1]){
		$toilets = [];
		foreach(explode('、', $match[1]) as $toilet){
			$toilets[] = '［卫生间］'.$toilet;
		}
		$data['绍兴京越地铁'][$station['title']] = implode("\n", $toilets);
	}else{
		$data['绍兴京越地铁'][$station['title']] = '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['绍兴京越地铁']).' 条数据');

?>
