<?php

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['huhhot'] = [];
$citiesMeta['huhhot'] = [
    'name' => '呼和浩特地铁',
    'support' => true,
    'source' => '青城地铁 App',
    'time' => time(),
    'color' => [
        'main' => '#0078B5',
    ],
    'font' => 'CN',
    'logo' => 'metro_logo_huhhot.svg',
];

// Download http://appqiniuyun.hhhtmetro.com/HS_latest.apk
// And simply extract it. Data will be found in assets/ folder.
$files = getCacheFolderContents('toilet/huhhot/');

// Get data
foreach($files as $fileName) {
    if(preg_match('/^line\d+_zdxx.json$/', $fileName)) {
        $citiesMeta['huhhot']['time'] = min(filemtime(getCachePath('toilet/huhhot/'.$fileName)), $citiesMeta['huhhot']['time']);
        $stations = json_decode(getCache('toilet/huhhot/'.$fileName), true)['List'];
        foreach($stations as $station) {
            if(array_key_exists($station['stationName'], $toiletInfo['huhhot'])) continue;
            $toiletInfo['huhhot'][$station['stationName']] = ['toilets' => []];
            foreach(explode('、', $station['stationwc']) as $toilet) {
                $toiletInfo['huhhot'][$station['stationName']]['toilets'][] = [
                    'title' => '卫生间',
                    'content' => $toilet,
                ];
            }
        }
    }
}

// Station name aliases
foreach(array_keys($toiletInfo['huhhot']) as $stationName) {
    if(preg_match('/^(.+?)（(.+)）$/u', $stationName, $match)) {
        foreach([1, 2] as $n) {
            if(!array_key_exists($match[$n], $toiletInfo['huhhot'])) {
                $toiletInfo['huhhot'][$match[$n]] = [];
            }
            $toiletInfo['huhhot'][$match[$n]]['redirect'] = [$stationName];
        }
    }
}

// Set time
$citiesMeta['huhhot']['time'] = date('Y/m/d', $citiesMeta['huhhot']['time']);

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['huhhot']).' 条数据（数据更新时间 '.$citiesMeta['huhhot']['time'].'）');