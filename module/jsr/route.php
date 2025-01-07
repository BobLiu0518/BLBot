<?php

global $Event;

use zjkal\ChinaHoliday;
requireLvl(1);

$station = nextArg() ?? (new BLBot\Database('jsr'))->get($Event['user_id'])['default'];
if(!$station) replyAndLeave("不知道你想查询什么车站呢…\n可以使用 #jsr.default 设置默认车站哦～");
$station = preg_replace('/站$/', '', $station);
if(!in_array($station, ['春申', '新桥', '车墩', '叶榭', '亭林', '金山园区', '金山卫'])) {
    replyAndLeave("只能查询设置春申~金山卫内的车站哦…\n如果想要查询莘庄或上海南的车次信息，请使用 #jsr 指令～");
}

$stations = json_decode(getData('jsr/station.json'), true);
$trainInfo = json_decode(getData('jsr/train.json'), true);
$time = nextArg(true);
$time = $time ? strtotime($time) : time();
$isWorkday = ChinaHoliday::isWorkday($time);

$trains = array_filter($stations[$station],
    fn($train) =>
    ($train['dates'] == 'all' || $isWorkday && $train['dates'] == 'weekdays' || !$isWorkday && $train['dates'] == 'weekends')
    && (date('H:i', $time) <=> $train['time']) <= 0
);

$result = [[], []];
foreach($trains as $train) {
    $direction = intval(substr($train['code'], -1)) % 2;
    $detail = $trainInfo[$train['code']];
    if(!$direction) {
        $terminus = $detail['stations'][count($detail['stations']) - 1];
        $result[$direction][] = "{$train['code']} {$station}{$train['time']} - {$terminus['station_name']}{$terminus['arrive_time']}";
    } else {
        $origin = $detail['stations'][0];
        if((date('H:i', $time) <=> $origin['start_time']) > 0) continue;
        $result[$direction][] = "{$train['code']} {$origin['station_name']}{$origin['start_time']} - {$station}{$train['time']}";
    }
}

$reply = "［金山铁路］{$station}站 出行指南\n @".date('Y/m/d H:i', $time);
foreach(intval(date('H', $time)) < 12 ? [0, 1] : [1, 0] as $direction) {
    array_splice($result[$direction], 5);
    $reply .= "\n‣ ".($direction ? "上海南/莘庄 → {$station}\n" : "{$station} → 莘庄/上海南\n");
    $reply .= implode("\n", $result[$direction]);
    if(!count($result[$direction])) {
        $reply .= '末班车时间已过';
    }
}
replyAndLeave($reply);
