<?php

requireLvl(6);

// Order from MetroMan
$order = [
    'beijing',
    'shanghai',
    'shanghai_suburban',
    'guangzhou',
    'foshan',
    'guangdong',
    'shenzhen',
    'hongkong',
    'taipei',
    'nanjing',
    'chongqing',
    'wuhan',
    'chengdu',
    'tianjin',
    'dalian',
    'suzhou',
    'hangzhou',
    'shaoxing',
    'haining',
    'zhengzhou',
    'xian',
    'kunming',
    'ningbo',
    'changsha',
    'changchun',
    'hefei',
    'wuxi',
    'shenyang',
    'nanning',
    'nanchang',
    'qingdao',
    'kaohsiung',
    'dongguan',
    'shijiazhuang',
    'xiamen',
    'fuzhou',
    'harbin',
    'guiyang',
    'urumqi',
    'wenzhou',
    'jinan',
    'lanzhou',
    'changzhou',
    'xuzhou',
    'macau',
    'huhhot',
    'taichung',
    'taiyuan',
    'luoyang',
    'wuhu',
    'jinhua',
    'nantong',
    'taizhou'
];

$newToiletInfo = $newCitiesMeta = [];
$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);
setCache('toilet/'.time().'.bak', json_encode($toiletInfo));

foreach($order as $city) {
    if(!array_key_exists($city, $toiletInfo)) {
        replyAndLeave('[Error] Cannot find city '.$city);
    }
    $newToiletInfo[$city] = $toiletInfo[$city];
    $newCitiesMeta[$city] = $citiesMeta[$city];
    unset($toiletInfo[$city]);
}
if(count($toiletInfo)) {
    replyAndLeave('[Error] Remaining cities: '.implode(', ', array_keys($toiletInfo)));
}

setData('toilet/toiletInfo.json', json_encode($newToiletInfo));
setData('toilet/citiesMeta.json', json_encode($newCitiesMeta));
replyAndLeave('Done.');
