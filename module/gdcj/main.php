<?php

requireLvl(1);
date_default_timezone_set('Asia/Shanghai');

$from = nextArg();
$to = nextArg();
$datetime = nextArg(true);
$datetime = $datetime ? strtotime($datetime) : time();

if(!$from || !$to) {
    replyAndLeave('参数不正确');
}
if ($datetime < strtotime(date('Y-m-d 00:00:00')) || $datetime >= strtotime(date('Y-m-d 00:00:00', strtotime('+4 days')))) {
    replyAndLeave('时间参数超出范围（仅支持 3 天内）');
}

$date = date('Ymd', $datetime);
$time = date('H:i:s', $datetime);
$trains = getCache("gdcj/{$date}/trains.json");
$stations = getCache("gdcj/{$date}/stations.json");
if(!$trains || !$stations) {
    mkdir(getCachePath("gdcj/{$date}"));
    $trains = file_get_contents("https://guangdong-intercity-data.bobliu.tech/{$date}/trains.json");
    $stations = file_get_contents("https://guangdong-intercity-data.bobliu.tech/{$date}/stations.json");
    setCache("gdcj/{$date}/trains.json", $trains);
    setCache("gdcj/{$date}/stations.json", $stations);
}
$trains = json_decode($trains, true);
$stations = json_decode($stations, true);

if($from == $to || !$stations[$from] || !$stations[$to]) {
    replyAndLeave('车站不正确');
}

$rounds = [[$from => ['arrive' => $time, 'route' => []]]];
$roundCount = 0;
while($roundCount < 20) {
    $roundCount ++;
    $rounds[$roundCount] = [...$rounds[$roundCount - 1]];

    foreach($rounds[$roundCount - 1] as $stationName => $roundStationData) {
        $departTime = date('H:i:s', strtotime($roundStationData['arrive'] . ' +3 minutes'));
        foreach($stations[$stationName] as $stationTrain) {
            if($stationTrain['depart'] < $departTime) continue;
            $stops = $trains[$stationTrain['code']]['stops'];
            foreach($stops as $stop) {
                if($stop['depart'] <= $stationTrain['depart']) continue;
                if(!$rounds[$roundCount][$stop['name']] || $rounds[$roundCount][$stop['name']]['arrive'] > $stop['arrive']) {
                    $rounds[$roundCount][$stop['name']] = [
                        'arrive' => $stop['arrive'],
                        'route' => [
                            ...$roundStationData['route'],
                            "{$stationTrain['code']} {$stationName}{$stationTrain['depart']} - {$stop['name']}{$stop['arrive']}",
                        ],
                    ];
                }
            }
        }
    }

    if ($rounds[$roundCount] == $rounds[$roundCount - 1]) {
        break;
    }
}

$datetime = date('Y/m/d H:i', $datetime);
$reply = "［广东城际］{$from} → {$to}\n @{$datetime}\n\n";
if($rounds[$roundCount][$to]['route']) {
    $reply .= implode("\n", $rounds[$roundCount][$to]['route']);
} else {
    $reply .= '☆☆☆ 今日は終了しました ☆☆☆';
}
$reply .= "\n\n功能调试中，不保证数据准确性。结果为最早到达时间的最早乘车方案，并非最优方案，且依赖列车准点和换乘速度，结果仅供参考。";
replyAndLeave($reply);