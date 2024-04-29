<?php

requireLvl(6);

$lines = simplexml_load_string(file_get_contents('https://www.dlmetro.com/hb-air-web/html/wxgo/resource/dalian.xml'));
$stationInfoApi = 'https://www.dlmetro.com/hb-air-api/site/ShowSite.do?siteId=';
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['大连地铁'] = [];

foreach($lines->l as $line){
	foreach($line->p as $node){
		if(!$node['lb'] || $data['大连地铁'][strval($node['lb'])]) continue;
		$stationInfo = json_decode(file_get_contents($stationInfoApi.strval($node['acc'])), true)['result'];
		$data['大连地铁'][strval($node['lb'])] = $stationInfo['siteInfo']['toilet'] ? '［卫生间］'.str_replace('。', '', $stationInfo['siteInfo']['toilet']) : '无数据，该站可能无卫生间';
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['大连地铁']).' 条数据');

?>
