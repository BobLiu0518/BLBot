<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Extract database from /storage/emulated/0/Android/data/com.mtr.mtrmobile/files/databases
$facilityDb = new SQLite3('../storage/cache/toilet/MTR/E_Info.db');
$context = stream_context_create([
	'http' => [
		'method' => 'GET',
		'header' => 'api-key: a070514789c14e22a4e54dbcce6dec81',
	],
]);
$stations = json_decode(file_get_contents('https://proxy001.api.mtr.com.hk./ttp-api/v2/api/StationBaseInfo/HRStations', false, $context), true)['stations'];
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['香港鐵路'] = [];

$toiletData = $facilityDb->query(<<<EOT
SELECT STATION_ID, STATION_FACILITY_URL_TC
FROM stationFacilitiesInfo
WHERE STATION_FACILITY_ID = 6
ORDER BY CAST(STATION_ID as INT) ASC;
EOT);
$toilets = [];
while($row = $toiletData->fetchArray(SQLITE3_ASSOC)){
	$toilets[$row['STATION_ID']] = '［洗手間］'.$row['STATION_FACILITY_URL_TC'];
}

foreach($stations as $station){
	$data['香港鐵路'][$station['nameTC']] = $toilets[$station['ID']] ?? '［洗手間］無';
	if(OpenCC::hk2s($station['nameTC']) != $station['nameTC']){
		$data['香港鐵路'][OpenCC::hk2s($station['nameTC'])] = 'Redirect='.$station['nameTC'];
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['香港鐵路']).' 条数据');

?>
