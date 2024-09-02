<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Install MTR Mobile App on Android phone
// Extract database from /storage/emulated/0/Android/data/com.mtr.mtrmobile/files/databases
$facilityDbPath = getCachePath('toilet/MTR/E_Info.db');
$updateTime = date('Y/m/d', filemtime($facilityDbPath));
$facilityDb = new SQLite3($facilityDbPath);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['hongkong'] = [];
$citiesMeta['hongkong'] = [
    'name' => '港鐵',
    'support' => true,
    'source' => 'MTR Mobile App',
    'time' => $updateTime,
    'color' => [
        'main' => '#B60036',
    ],
    'font' => 'HK',
    'logo' => 'metro_logo_hongkong.svg',
];

// Load stations
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'api-key: a070514789c14e22a4e54dbcce6dec81',
    ],
]);
$stations = file_get_contents('https://proxy001.api.mtr.com.hk./ttp-api/v2/api/StationBaseInfo/HRStations', false, $context);
$stations = json_decode($stations, true)['stations'];

// Load toilets
$toilets = [];
$toiletData = $facilityDb->query(<<<EOT
SELECT STATION_ID, STATION_FACILITY_URL_TC
FROM stationFacilitiesInfo
WHERE STATION_FACILITY_ID = 6
ORDER BY CAST(STATION_ID as INT) ASC;
EOT);
while($row = $toiletData->fetchArray(SQLITE3_ASSOC)) {
    $toilets[$row['STATION_ID']] = $row['STATION_FACILITY_URL_TC'];
}

// Match data
foreach($stations as $station) {
    $toiletInfo['hongkong'][$station['nameTC']] = ['toilets' => [['title' => '洗手間']]];
    $toiletInfo['hongkong'][$station['nameTC']]['toilets'][0]['content'] = $toilets[$station['ID']] ?? '無';

    // TC -> SC
    if(OpenCC::hk2s($station['nameTC']) != $station['nameTC']) {
        $toiletInfo['hongkong'][OpenCC::hk2s($station['nameTC'])] = ['redirect' => [$station['nameTC']]];
    }
}

// HK West Kowloon station has toilets but not recorded in MTR Mobile app
$toiletInfo['hongkong']['香港西九龍']['toilets'][0]['content'] = '有';

// HK West Kowloon & Kowloon & Austin: Unpaid area transfer
$toiletInfo['hongkong']['香港西九龍']['redirect'] = ['九龍', '柯士甸'];
$toiletInfo['hongkong']['九龍']['redirect'] = ['香港西九龍', '柯士甸'];
$toiletInfo['hongkong']['柯士甸']['redirect'] = ['香港西九龍', '九龍'];

// Tsim Sha Tsui & East Tsim Sha Tsui: Unpaid area transfer
$toiletInfo['hongkong']['尖沙咀']['redirect'] = ['尖東'];
$toiletInfo['hongkong']['尖東']['redirect'] = ['尖沙咀'];

// Central & Hong Kong: Paid area transfer
$toiletInfo['hongkong']['中環']['redirect'] = ['香港'];
$toiletInfo['hongkong']['香港']['redirect'] = ['中環'];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['hongkong']).' 条数据（数据库更新时间 '.$updateTime.'）');