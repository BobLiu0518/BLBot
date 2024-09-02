<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nanjing'] = [];
$citiesMeta['nanjing'] = [
    'name' => '南京地铁',
    'support' => true,
    'source' => '南京地铁微信公众号乘车宝典页面',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#FF0000',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nanjing.svg',
];

// Get token
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

// Get stations
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
$stations = json_decode(file_get_contents($host.$stationInfoApi, false, $context), true);
foreach($stations as $station) {
    $toilets = trim($station['stationInfo']['wc']);
    $toilets = preg_replace('/(；|\r\n|\s+)/', "\n", $toilets);
    $toiletInfo['nanjing'][$station['name']] = ['toilets' => []];

    // Parse data
    if(preg_match_all('/^(.+?线)(.+?：)?(.+?)$/mu', $toilets, $match)) {
        foreach($match[1] as $id => $lineName) {
            $toiletInfo['nanjing'][$station['name']]['toilets'][] = [
                'title' => trim($lineName),
                'content' => trim(preg_replace('/[；。：]/u', '', $match[3][$id])),
            ];
        }
    } else {
        foreach(explode("\n", $toilets) as $toilet) {
            $toiletInfo['nanjing'][$station['name']]['toilets'][] = [
                'title' => '卫生间',
                'content' => $toilet,
            ];
        }
    }

    // Set color
    foreach($station['durations'] as $line) {
        if(!array_key_exists($line['lineName'], $citiesMeta['nanjing']['color'])) {
            $citiesMeta['nanjing']['color'][$line['lineName']] = str_replace('0x', '#', $line['lineColor']);
            if(preg_match('/^.+线(S\d+)$/u', $line['lineName'], $match)) {
                $citiesMeta['nanjing']['color'][$match[1].'线'] = str_replace('0x', '#', $line['lineColor']);
                $citiesMeta['nanjing']['color'][$match[1].'号线'] = str_replace('0x', '#', $line['lineColor']);
            }
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['nanjing']) as $stationName) {
    if(preg_match('/·/u', $stationName)) {
        foreach(explode('·', $stationName) as $subName) {
            if(!array_key_exists($subName, $toiletInfo['nanjing'])) {
                $toiletInfo['nanjing'][$subName] = [];
            }
            $toiletInfo['nanjing'][$subName]['redirect'] = [$stationName];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nanjing']).' 条数据');