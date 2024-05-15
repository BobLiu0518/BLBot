<?php

requireLvl(6);

$timestamp = time().sprintf('%03d', rand(0, 999));
$docMosft = base64_encode('101-'.$timestamp.'-NJmetro');
$Md5Pwd = md5('101-'.$timestamp.'-NJmetro-Derensoft');
$host = 'http://ccbd.njmetro.net:9093/';
$stationNameApi = 'api/GetStationsName';
$stationInfoApi = 'api/GetStationInfo';
$tokenApi = 'token';

$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => 'Content-Type: application/x-www-form-urlencoded',
		'content' => 'client_id='.$docMosft.'&client_secret='.$Md5Pwd.'&grant_type=client_credentials',
	],
]);
$token = json_decode(file_get_contents($host.$tokenApi, false, $context), true)['access_token'];

$context = stream_context_create([
	'http' => [
		'method' => 'POST',
		'header' => implode("\n", [
			'Authorization: Bearer '.$token,
			'Content-Length: 0',
		]),
		'content' => '',
	],
]);
// $stationName = json_decode(file_get_contents($host.$stationNameApi, false, $context), true);
$stationInfo = json_decode(file_get_contents($host.$stationInfoApi, false, $context), true);

$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['南京地铁'] = [];

foreach($stationInfo as $station){
	$toilets = $station['stationInfo']['wc'];
	$toilets = preg_replace('/(；|\r\n|\s+)/', "\n", $toilets);
	$data['南京地铁'][$station['name']] = [];
	if(!$toilets){
		$data['南京地铁'][$station['name']] = '无数据，该站可能无卫生间';
	}else if(preg_match_all('/^(.+?线)(.+?：)?(.+?)$/m', $toilets, $match)){
		foreach($match[1] as $id => $lineName){
			$data['南京地铁'][$station['name']][] = '［'.trim($lineName).'］'.trim(preg_replace('/(；|。|：)/', '', $match[3][$id]));
		}
		$data['南京地铁'][$station['name']] = implode("\n", $data['南京地铁'][$station['name']]);
	}else{
		foreach(explode("\n", $toilets) as $toilet){
			$data['南京地铁'][$station['name']][] = '［卫生间］'.$toilet;
		}
		$data['南京地铁'][$station['name']] = implode("\n", $data['南京地铁'][$station['name']]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['南京地铁']).' 条数据');

?>
