<?php

requireLvl(6);

// Install Guangzhou Metro App on Android phone
// Extract database from /data/data/com.infothinker.gzmetro/databases/gzmetro_YYYYMMDD.sqlite
$dbPath = getCachePath('toilet/guangzhou.sqlite');
$updateTime = date('Y/m/d', filemtime($dbPath));
$db = new SQLite3($dbPath);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['guangzhou'] = $toiletInfo['foshan'] = $toiletInfo['guangdong'] = [];
$citiesMeta['guangzhou'] = [
    'name' => '广州地铁',
    'support' => true,
    'source' => '广州地铁 App',
    'remark' => '含广州地铁、海珠有轨、黄埔有轨',
    'time' => $updateTime,
    'color' => [
        'main' => '#EC1B23',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_guangzhou.svg',
];
$citiesMeta['foshan'] = [
    'name' => '佛山地铁',
    'support' => true,
    'source' => '广州地铁 App',
    'remark' => '含佛山地铁、南海有轨',
    'time' => $updateTime,
    'color' => [
        'main' => '#EC0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_foshan.svg',
];
$citiesMeta['guangdong'] = [
    'name' => '广东城际',
    'support' => true,
    'source' => '广州地铁 App',
    'time' => $updateTime,
    'color' => [
        'main' => '#EC1B23',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_guangzhou.svg',
];

// Load toilets
$toiletData = $db->query(<<<EOT
SELECT station_id, location_cn, name_cn
FROM device
WHERE category_id = 6
ORDER BY station_id ASC;
EOT);
$toilets = [];
while($row = $toiletData->fetchArray(SQLITE3_ASSOC)) {
    if(!$toilets[$row['station_id']]) {
        $toilets[$row['station_id']] = [];
    }
    foreach(explode('。', $row['location_cn']) as $toilet) {
        if(!$toilet) continue;
        $toilets[$row['station_id']][] = [
            'title' => $row['name_cn'],
            'content' => preg_replace('/虫雷 岗/u', '𧒽岗', $toilet),
        ];
    }
}

// Load lines
$linesData = $db->query(<<<EOT
SELECT number, line_no, color
FROM line
ORDER BY number ASC;
EOT);
$lines = [];
while($row = $linesData->fetchArray(SQLITE3_ASSOC)) {
    if(preg_match('/^CJ\d+$/', $row['line_no'])) {
        $company = 'guangdong';
    } else if(preg_match('/^(F|TNH)\d+$/', $row['line_no'])) {
        $company = 'foshan';
    } else {
        $company = 'guangzhou';
    }
    $lines[$row['number']] = [
        'company' => $company,
        'color' => preg_replace('/^[0-9a-f]{6}ff$/i', '#${1}', $row['color']),
    ];
}

// Load line stations
$lineStationData = $db->query(<<<EOT
SELECT line_number, station_id
FROM line_station
ORDER BY station_id ASC, line_number ASC;
EOT);
$companies = [];
while($row = $lineStationData->fetchArray(SQLITE3_ASSOC)) {
    if(array_key_exists($row['station_id'], $companies)) continue;
    $companies[$row['station_id']] = $lines[$row['line_number']]['company'];
}

// Load stations
$stationData = $db->query(<<<EOT
SELECT station_id, name_cn
FROM station;
EOT);
while($row = $stationData->fetchArray(SQLITE3_ASSOC)) {
    $stationName = preg_replace('/^虫雷 岗/u', '𧒽岗', $row['name_cn']);
    $company = $companies[$row['station_id']];
    $toiletInfo[$company][$stationName] = ['toilets' => $toilets[$row['station_id']] ?? []];
}

foreach(['guangzhou', 'foshan', 'guangdong'] as $company) {
    foreach($toiletInfo[$company] as $station => $data) {
        if(preg_match('/^(.+)（(.+)）$/u', $station, $matches) && $matches[2] != '有轨') {
            $toiletInfo[$company][$matches[1]] = ['redirect' => [$station]];
        }
    }
}

// Handle tram redirect
$toiletInfo['guangzhou']['广州塔']['redirect'] = ['广州塔（有轨）'];
$toiletInfo['foshan']['林岳东']['redirect'] = ['林岳东（有轨）'];
$toiletInfo['foshan']['𧒽岗'] = ['redirect' => ['𧒽岗（有轨）']];

// Handle Leigang station redirect
$toiletInfo['guangzhou']['虫雷 岗']['redirect'] = ['𧒽岗'];
$toiletInfo['guangzhou']['虫雷岗']['redirect'] = ['𧒽岗'];
$toiletInfo['foshan']['虫雷 岗'] = ['redirect' => ['𧒽岗']];
$toiletInfo['foshan']['虫雷岗'] = ['redirect' => ['𧒽岗']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，广州地铁 '.count($toiletInfo['guangzhou']).' 条、佛山地铁 '.count($toiletInfo['foshan']).' 条、广东城际 '.count($toiletInfo['guangdong']).' 条数据（数据库更新时间 '.$updateTime.'）');