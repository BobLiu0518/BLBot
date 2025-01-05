<?php

requireLvl(1);
use zjkal\ChinaHoliday;

$search = nextArg();
if(!$search) replyAndLeave('不知道你要查询什么呢…（可以查询金山铁路车次或车站）');
if(preg_match('/^(G|D|C|Z|T|K|Y|L|X|N)(\d+)$/i', $search, $match)) {
    replyAndLeave('本指令仅用于查询金山铁路车站和车次，如欲查询其他车次，请使用 '.Config('prefix').'cr 指令。');
} else if(preg_match('/^S?(\d+)$/i', $search, $match)) {
    $code = 'S'.$match[1];
    $trains = json_decode(getData('jsr/train.json'), true);
    if(!$trains[$code]) replyAndLeave('［金山铁路］车次 '.$code.' 不存在或已停开…');
    $train = $trains[$code];
    $reply = $train['code'].'次 金山铁路';
    $reply .= "\n".$train['from'].' → '.$train['to'].' '.preg_replace('/ \(.+\)$/', '', $train['type']);
    if($train['dates'] == 'weekdays') {
        $reply .= "\n* 仅工作日开行";
    } else if($train['dates'] == 'weekends') {
        $reply .= "\n* 仅双休日开行";
    }
    foreach($train['stations'] as $n => $station) {
        $reply .= "\n".$station['station_no'].' '.$station['station_name'];
        for($i = 0; $i < 4 - mb_strlen($station['station_name']); $i++) $reply .= '　';
        if($n != 0) $reply .= ' '.$station['arrive_time'].'到';
        if($n != count($train['stations']) - 1) $reply .= ' '.$station['start_time'].'发';
    }
    replyAndLeave($reply);
} else {
    $stations = json_decode(getData('jsr/station.json'), true);
    $station = preg_replace('/站$/', '', $search);
    if(!$stations[$station]) replyAndLeave('［金山铁路］车站 '.$station." 不存在…\n可选车站：".implode(' ', array_keys($stations)));
    $trains = $stations[$station];

    $time = nextArg(true);
    $time = $time ? strtotime($time) : (time() - 5 * 60);
    $isWorkday = ChinaHoliday::isWorkday($time);
    $trains = array_filter($trains,
        fn($train) =>
        ($train['dates'] == 'all' || $isWorkday && $train['dates'] == 'weekdays' || !$isWorkday && $train['dates'] == 'weekends')
        && (date('H:i', $time) <=> $train['time']) <= 0
    );
    $result = [[], []];
    foreach($trains as $train) {
        $direction = intval(substr($train['code'], -1)) % 2;
        $result[$direction][] = $train;
    }
    $reply = '　　［金山铁路 '.$station."站］\n".date('n月j日 H:i', $time).' 起最近 5 次列车：';
    foreach(in_array($station, ['莘庄', '上海南']) ? [1, 0] : [0, 1] as $direction) {
        array_splice($result[$direction], 5);
        $reply .= "\n‣ 往".($direction ? '金山卫' : '上海南').'方向：';
        foreach($result[$direction] as $train) {
            $reply .= "\n· {$train['time']} {$train['code']}";
            $reply .= $train['to'] == $station ? ' (终到)' : ' 往'.$train['to'];
            $reply .= ' '.$train['type'];
        }
    }
    replyAndLeave($reply);
}
