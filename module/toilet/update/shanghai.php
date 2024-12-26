<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['shanghai'] = [];
$citiesMeta['shanghai'] = [
    'name' => '上海地铁',
    'support' => true,
    'source' => '上海地铁官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#EC1B23',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shanghai.svg',
];
$citiesMeta['shanghai_suburban'] = [
    'name' => '上海市域铁路',
    'support' => true,
    'source' => '上海地铁官方网站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#E60012',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_shanghai_suburban.svg',
];

// Load stations
$stations = json_decode(file_get_contents('https://m.shmetro.com/core/shmetro/mdstationinfoback_new.ashx?act=getAllStations'), true);
$stationInfoApi = 'https://m.shmetro.com/interface/metromap/metromap.aspx?func=stationInfo&stat_id=';
$lines = json_decode(file_get_contents('https://m.shmetro.com/interface/metromap/metromap.aspx?func=lines'), true);
$getCompany = fn($line) => intval($line) > 50 ? 'shanghai_suburban' : 'shanghai';
$lineName = fn($id) => $id == 41 ? '浦江线' : $id == 51 ? '机场联络线' : "{$id}号线";
foreach($stations as $station) {
    $company = $getCompany(substr($station['key'], 0, 2));

    // Load data
    $stationInfo = json_decode(file_get_contents($stationInfoApi.$station['key']), true)[0];
    $stationName = $station['value'];
    $toilets = json_decode($stationInfo['toilet_position'] ?? '', true)['toilet'];

    // Handle redundance space
    $stationName = preg_replace('/\s+$/u', '', $stationName);

    // Skip fake stations
    if(preg_match('/^[内外]圈(\(宜山路\))?/u', $stationName)) continue;

    // Match data
    if(!array_key_exists($stationName, $toiletInfo[$company] ?? [])) {
        $toiletInfo[$company][$stationName] = ['toilets' => []];
    }
    foreach($toilets as $toilet) {
        if($getCompany($toilet['lineno']) != $company) continue;
        $info = [
            'title' => $lineName($toilet['lineno']),
            'content' => $toilet['description'],
        ];
        if(!in_array($info, $toiletInfo[$company][$stationName]['toilets'])) {
            $toiletInfo[$company][$stationName]['toilets'][] = $info;
        }
    }
}

// Set line color
foreach($lines as $line) {
    $citiesMeta[$getCompany($line['line_no'])]['color'][$lineName($line['line_no'])] = $line['color'];
}

// Set aliases
$toiletInfo['shanghai']['济阳路'] = ['redirect' => ['东方体育中心']];
$toiletInfo['shanghai']['船厂路'] = ['redirect' => ['龙华中路']];
$toiletInfo['shanghai']['周家渡'] = ['redirect' => ['中华艺术宫']];
$toiletInfo['shanghai']['航天博物馆'] = ['redirect' => ['沈杜公路']];
$toiletInfo['shanghai']['卢浦大桥'] = ['redirect' => ['世博会博物馆']];
$toiletInfo['shanghai']['黄陂南路'] = ['redirect' => ['一大会址·黄陂南路']];
$toiletInfo['shanghai']['新天地'] = ['redirect' => ['一大会址·新天地']];
$toiletInfo['shanghai']['徐泾东'] = ['redirect' => ['国家会展中心']];
$toiletInfo['shanghai']['东昌路'] = ['redirect' => ['浦东南路']];
$toiletInfo['shanghai']['浦东国际机场'] = ['redirect' => ['浦东1号2号航站楼']];
$toiletInfo['shanghai_suburban']['浦东国际机场'] = ['redirect' => ['浦东1号2号航站楼']];
$toiletInfo['shanghai']['浦电路']['redirect'] = ['向城路'];
$toiletInfo['shanghai']['松江南站'] = ['redirect' => ['上海松江站']];
$toiletInfo['shanghai']['华泾西'] = ['redirect' => ['景洪路']];
$toiletInfo['shanghai']['诸光路'] = ['redirect' => ['国家会展中心']];

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，上海地铁 '.count($toiletInfo['shanghai']).' 条数据，上海市域铁路 '.count($toiletInfo['shanghai_suburban']).' 条数据');
