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

// Load data
$companies = [];
$lineStationData = $db->query(<<<SQL
SELECT 
    s.name_cn AS station_name, 
    l.line_no, 
    COALESCE(
        d1.name_cn, 
        d2.name_cn, 
        d3.name_cn
    ) AS device_name,
    COALESCE(
        d1.location_cn, 
        d2.location_cn, 
        d3.location_cn
    ) AS location_cn
FROM station s
JOIN (
    SELECT ls.station_id, MIN(ll.line_no) AS line_no
    FROM line_station ls
    JOIN line ll ON ls.line_number = ll.number
    GROUP BY station_id
) l ON s.station_id = l.station_id
LEFT JOIN device d1 ON s.station_id = d1.station_id AND d1.category_id = 6
LEFT JOIN device d2 ON s.station_id = d2.station_id AND d2.category_id = 98
LEFT JOIN device d3 ON s.station_id = d3.station_id AND d3.category_id = 99;
SQL);
while($row = $lineStationData->fetchArray(SQLITE3_ASSOC)) {
    $stationName = preg_replace('/^虫雷 岗/u', '𧒽岗', str_replace('（城际）', '', $row['station_name']));
    $company = $companies[$row['line_no']];
    if(!$company) {
        if(preg_match('/^CJ\d+$/', $row['line_no'])) {
            $company = 'guangdong';
        } else if(preg_match('/^(F|TNH)\d+$/', $row['line_no'])) {
            $company = 'foshan';
        } else {
            $company = 'guangzhou';
        }
        $companies[$row['line_no']] = $company;
    }
    if(!$row['device_name']) {
        $toiletInfo[$company][$stationName] = ['toilets' => []];
    } else {
        foreach(explode('。', $row['location_cn']) as $toilet) {
            if(!$toilet) continue;
            $toiletInfo[$company][$stationName]['toilets'][] = [
                'title' => $row['device_name'],
                'content' => preg_replace('/虫雷 岗/u', '𧒽岗', $toilet),
            ];
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

// Handle metro & intercity railway interchange
$toiletInfo['guangdong']['佛山西站'] = ['redirect' => ['佛山西']];
$toiletInfo['foshan']['佛山西'] = ['redirect' => ['佛山西站']];
$toiletInfo['guangdong']['东平'] = ['redirect' => ['顺德北']];
$toiletInfo['foshan']['顺德北'] = ['redirect' => ['东平']];
$toiletInfo['guangdong']['北滘西站'] = ['redirect' => ['北滘西']];
$toiletInfo['foshan']['北滘西'] = ['redirect' => ['北滘西站']];
$toiletInfo['guangdong']['广州南站'] = ['redirect' => ['番禺']];
$toiletInfo['guangzhou']['番禺'] = ['redirect' => ['广州南站']];
$toiletInfo['guangdong']['汉溪长隆'] = ['redirect' => ['广州长隆']];
$toiletInfo['guangzhou']['广州长隆'] = ['redirect' => ['汉溪长隆']];
$toiletInfo['guangdong']['官桥'] = ['redirect' => ['官桥北']];
$toiletInfo['guangzhou']['官桥北'] = ['redirect' => ['官桥']];
$toiletInfo['guangdong']['广州北站'] = ['redirect' => ['花都']];
$toiletInfo['guangzhou']['花都'] = ['redirect' => ['广州北站']];
$toiletInfo['guangdong']['机场北（2号航站楼）'] = ['redirect' => ['白云机场北']];
$toiletInfo['guangzhou']['白云机场北'] = ['redirect' => ['机场北（2号航站楼）']];
$toiletInfo['guangdong']['机场南（1号航站楼）'] = ['redirect' => ['白云机场南']];
$toiletInfo['guangzhou']['白云机场南'] = ['redirect' => ['机场南（1号航站楼）']];
$toiletInfo['guangdong']['大石'] = ['redirect' => ['大石东']];
$toiletInfo['guangzhou']['大石东'] = ['redirect' => ['大石']];
$toiletInfo['guangdong']['西平'] = ['redirect' => ['西平西']];

foreach(['guangzhou', 'foshan', 'guangdong'] as $company) {
    foreach($toiletInfo[$company] as $station => $data) {
        if(preg_match('/^(.+)（(.+)）$/u', $station, $matches) && $matches[2] != '有轨') {
            $toiletInfo[$company][$matches[1]] = ['redirect' => [$station]];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，广州地铁 '.count($toiletInfo['guangzhou']).' 条、佛山地铁 '.count($toiletInfo['foshan']).' 条、广东城际 '.count($toiletInfo['guangdong']).' 条数据（数据库更新时间 '.$updateTime.'）');
