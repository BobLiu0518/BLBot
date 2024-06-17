<?php

requireLvl(6);

$context = stream_context_create([
	'http' => [
		'method' => 'GET',
		'header' => 'User-Agent: Mozilla/5.0',
	],
]);
$stationsPage = file_get_contents('https://www.nngdjt.com/html/service1c/', false, $context);
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['南宁轨道交通'] = [];

preg_match_all('/<span class="station" id="\d+"\s*>\s*(.+)\s*<\/span>/', $stationsPage, $match);
foreach($match[1] as $station){
	if(!preg_match('/(客运|火车)(东|南|西|北)?站$/', $station)){
		$station = preg_replace('/站$/', '', $station);
	}
	$data['南宁轨道交通'][trim($station)] = "暂不支持南宁轨道交通车站查询\n（未找到官方卫生间位置数据）";
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('占位成功，共 '.count($data['南宁轨道交通']).' 条数据');

?>
