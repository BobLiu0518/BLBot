<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['nanning'] = [];
$citiesMeta['nanning'] = [
    'name' => '南宁轨道交通',
    'support' => true,
    'source' => '南宁轨道交通 App',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#18BEF2',
        'secondary' => '#0056A4',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_nanning.svg',
];

$info = json_decode(file_get_contents('https://cms-api.nngdjtapp.com/api/subwaymap/v1/allInfo'), true)['data'];
foreach($info['subwayMap']['lines'] as $line){
    $citiesMeta['nanning']['color'][$line['ln']] = "#{$line['lc']}";
}
foreach($info['stations'] as $station) {
    $stationName = $station['stationName'];
    if(!preg_match('/(客运|火车)(东|南|西|北)?站$/', $stationName)) {
        $stationName = preg_replace('/站$/', '', $stationName);
    }
    if(isset($toiletInfo['nanning'][$stationName])){
        continue;
    }
    $toiletInfo['nanning'][$stationName] = ['toilets' => []];
    $detail = json_decode(file_get_contents('https://cms-api.nngdjtapp.com/api/v1/station/'.$station['sid']), true);
    foreach($detail['stationInfo']['infrastructures'] as $infrastructures){
        foreach($infrastructures['infrastuctures'] as $infrastructure){
            if($infrastructure['name'] == '卫生间'){
                $toiletInfo['nanning'][$stationName]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => $infrastructure['desc'],
                ];
            }
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['nanning']).' 条数据');