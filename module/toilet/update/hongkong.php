<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Extract database from /storage/emulated/0/Android/data/com.mtr.mtrmobile/files/databases
$facilityDb = new SQLite3('../storage/cache/toilet/MTR/E_Info.db');
$stationDb = new SQLite3('../storage/cache/toilet/MTR/E_Station.db');
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

$stationData = $stationDb->query(<<<EOT
SELECT STATION_ID, CHI_LONG_NAME
FROM stations
ORDER BY CAST(STATION_ID as INT) ASC;
EOT);
while($row = $stationData->fetchArray(SQLITE3_ASSOC)){
	$data['香港鐵路'][$row['CHI_LONG_NAME']] = $toilets[$row['STATION_ID']] ?? '［洗手間］無';
	if(OpenCC::hk2s($row['CHI_LONG_NAME']) != $row['CHI_LONG_NAME']){
		$data['香港鐵路'][OpenCC::hk2s($row['CHI_LONG_NAME'])] = 'Redirect='.$row['CHI_LONG_NAME'];
	}
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['香港鐵路']).' 条数据');

?>
