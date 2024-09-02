<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shenzhen'] = [];
$citiesMeta['shenzhen'] = [
    'name' => '深圳地铁',
    'support' => true,
    'source' => '深圳地铁 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#009D35',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shenzhen.svg',
];

// Load lines
$linesUrl = json_decode(file_get_contents('https://szmc-intapi.shenzhenmc.com/szmc-mtas/baseCfg/queryObsUrlByDataVersion'), true)['data']['url'];
$lines = json_decode(file_get_contents($linesUrl), true)['data'];

// Process data
foreach($lines as $line) {
    // Set line color
    $citiesMeta['shenzhen']['color'][$line['lineName']] = $line['color'];

    foreach($line['stationVoCol'] as $station) {
        // Skip repeated stations
        if($toiletInfo['shenzhen'][$station['stationName']]) continue;
        else $toiletInfo['shenzhen'][$station['stationName']] = ['toilets' => []];

        foreach($station['facilityCol'] ?? [] as $facility) {
            if($facility['facilityCategoryId'] == 0) {
                // Split content
                $toilets = preg_split('/。(?!）|$)|；|\n/u', $facility['location']);

                foreach($toilets as $toilet) {
                    // Remove prefix
                    $toilet = preg_replace('/^\s*\d+[.:：]\s*/u', '', $toilet);
                    // Remove suffix
                    $toilet = preg_replace('/。?\s*$/u', '', $toilet);

                    if($toilet && !preg_match('/^（.+）$/u', $toilet)) {
                        $toiletInfo['shenzhen'][$station['stationName']]['toilets'][] = [
                            'title' => '洗手间',
                            'content' => $toilet,
                        ];
                    }
                }
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['shenzhen']).' 条数据');