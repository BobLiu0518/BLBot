<?php

requireLvl(6);

$trainListApi = 'https://search.12306.cn/search/v1/train/search';
$trainDetailApi = 'https://kyfw.12306.cn/otn/queryTrainInfo/query';

$trainData = [];
$stationData = [];
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: BLBot',
    ],
]);
$weekdayTrainList = json_decode(file_get_contents("{$trainListApi}?keyword=S&date=".date('Ymd', strtotime('Next Monday'))), true);
$weekendTrainList = json_decode(file_get_contents("{$trainListApi}?keyword=S&date=".date('Ymd', strtotime('Next Sunday'))), true);
$trainList = [];
foreach($weekdayTrainList['data'] as $train) {
    if(!in_array($train[intval(substr($train['station_train_code'], -1)) % 2 ? 'from_station' : 'to_station'], ['上海南', '莘庄'])) continue;
    $trainList[$train['station_train_code']] = [
        'code' => $train['station_train_code'],
        'train_no' => $train['train_no'],
        'dates' => 'weekdays',
        'from' => $train['from_station'],
        'to' => $train['to_station'],
        'stations_count' => $train['total_num'],
    ];
}
foreach($weekendTrainList['data'] as $train) {
    if($trainList[$train['station_train_code']]) {
        $trainList[$train['station_train_code']]['dates'] = 'all';
    }
}

foreach($trainList as $train) {
    $trainDetail = json_decode(file_get_contents("{$trainDetailApi}?leftTicketDTO.train_no={$train['train_no']}&leftTicketDTO.train_date="
        .date('Y-m-d', strtotime($train['dates'] == 'weekdays' ? 'Next Monday' : 'Next Sunday')).'&rand_code=', false, $context), true);

    $stations = $trainDetail['data']['data'];
    if($train['stations_count'] == 2) {
        $trainType = '直达';
    } else if($train['stations_count'] >= 8 || $train[intval(substr($train['code'], -1)) % 2 ? 'to' : 'from'] != '金山卫') {
        $trainType = "站站停";
    } else {
        $trainType = implode('', array_map(fn($station) => mb_substr($station['station_name'], 0, 1), array_slice($stations, 1, -1))).'大站';
    }

    $trainData[$train['code']] = [
        'code' => $train['code'],
        'type' => $trainType,
        'dates' => $train['dates'],
        'from' => $train['from'],
        'to' => $train['to'],
        'stations' => $stations,
    ];

    foreach($stations as $station) {
        if(!$stationData[$station['station_name']]) $stationData[$station['station_name']] = [];
        $stationData[$station['station_name']][] = [
            'time' => $station['start_time'],
            'code' => $train['code'],
            'type' => $trainType,
            'dates' => $train['dates'],
            'from' => $train['from'],
            'to' => $train['to'],
        ];
    }
}

foreach($stationData as $stationName => $stationTrains) {
    usort($stationData[$stationName], fn($a, $b) => $a['time'] <=> $b['time']);
}

setData('jsr/train.json', json_encode($trainData));
setData('jsr/station.json', json_encode($stationData));

replyAndLeave('更新数据成功，共 '.count($trainData).' 车次');
