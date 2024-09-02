<?php

use Overtrue\PHPOpenCC\OpenCC;

requireLvl(6);

// Init
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));
$toiletInfo['kaohsiung'] = [];
$citiesMeta['kaohsiung'] = [
    'name' => '高雄捷運',
    'support' => true,
    'source' => '高雄捷運網站',
    'time' => date('Y/m/d'),
    'color' => [
        'main' => '#005AB2',
        'secondary' => '#00A1F0',
    ],
    'font' => 'TW',
    'logo' => 'metro_logo_kaohsiung.svg',
];

// Load lines
$host = 'https://www.krtc.com.tw';
$links = [
    'metro' => $host.'/Guide/station_guide',
    'lightRail' => $host.'/KLRT/station_guide',
];

// Load stations
foreach($links as $type => $link) {
    $html = file_get_contents($link, false, $context);
    preg_match('/<div class="stationList">([\s\S]+?)<\/div>\s*<!-- End stationList -->/u', $html, $match);
    $stationsHtml = $match[1];
    preg_match_all('/<li>([\s\S]+?)<\/li>/u', $stationsHtml, $match);
    $stations = $match[1];
    foreach($stations as $station) {
        // Load data
        preg_match('/<a href="(.+?)">/u', $station, $match);
        $stationLink = $host.str_replace('station_info', $type == 'metro' ? 'station_plan' : 'station_news', $match[1]);
        preg_match('/<p>(.+?)<\/p>/u', $station, $match);
        $stationName = $match[1];
        if(array_key_exists($stationName, $toiletInfo['kaohsiung'])) continue;
        $toiletInfo['kaohsiung'][$stationName] = ['toilets' => []];
        $stationHtml = file_get_contents($stationLink, false, $context);
        if($type == 'metro') {
            preg_match_all('/【(.+)】([\s\S]+?)<\/div>/u', $stationHtml, $match);
            $details = $match[2];
            foreach($match[1] as $n => $location) {
                $detail = $details[$n];
                foreach(preg_split('/(?=●)/u', $detail) as $description) {
                    if(preg_match('/廁所/u', $description)) {
                        preg_match('/●(.+)\s*[：:]/u', $description, $match);
                        $toiletInfo['kaohsiung'][$stationName]['toilets'][] = [
                            'title' => $location,
                            'content' => $match[1],
                        ];
                    }
                }
            }
        } else {
            preg_match('/➤(.+?)。/u', $stationHtml, $match);
            $toilet = preg_replace('/\s|<.+?>/u', '', $match[1]);
            $toiletInfo['kaohsiung'][$stationName]['toilets'][] = [
                'title' => '周邊廁所',
                'content' => $toilet,
            ];
        }

        // TC -> SC
        if(OpenCC::hk2s($stationName) != $stationName) {
            $toiletInfo['kaohsiung'][OpenCC::tw2s($stationName)] = ['redirect' => [$stationName]];
        }
    }
}

// Save data
setData('toilet/toiletInfo.json', json_encode($toiletInfo));
setData('toilet/citiesMeta.json', json_encode($citiesMeta));
replyAndLeave('更新数据成功，共 '.count($toiletInfo['kaohsiung']).' 条数据');