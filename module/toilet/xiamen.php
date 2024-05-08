<?php

requireLvl(6);

// Packet capture: https://app.i-amtr.com/iets-web-app/pub/station/allStation
$stations = json_decode(getCache('toilet/xiamenStations.json'), true)['responseData'];
$stations = openssl_decrypt(hex2bin($stations), 'aes-128-ecb', 'QLJ1aZjhhTEm6RiN', OPENSSL_RAW_DATA);
$stations = json_decode($stations, true)['data'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['厦门地铁'] = [];

foreach($stations as $station){
	if(!$data['厦门地铁'][$station['name']]){
		$data['厦门地铁'][$station['name']] = [];
	}
	$facilities = json_decode($station['facilities'], true);
	if($facilities['洗手间']){
		$data['厦门地铁'][$station['name']][] = '［'.$station['lineName'].'］'.$facilities['洗手间'];
	}
}

foreach($data['厦门地铁'] as $stationName => $toilet){
	if(!$toilet){
		$data['厦门地铁'][$stationName] = '无数据，该站可能无卫生间';
	}else{
		$data['厦门地铁'][$stationName] = implode("\n", $data['厦门地铁'][$stationName]);
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['厦门地铁']).' 条数据');

?>
