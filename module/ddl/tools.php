<?php

function getDdlDb() {
    static $db;
    if(!$db) $db = new BLBot\Database('ddl');
    return $db;
}

function getDdl(int $user_id) {
    return getDdlDb()->get($user_id)['ddls'];
}

function setDdl(int $user_id, string $name, int $time) {
    return getDdlDb()->push($user_id, 'ddls', [
        'name' => $name,
        'time' => $time,
    ], ['time' => 1]);
}

function finishDdl(int $user_id, string $name) {
    return getDdlDb()->pull($user_id, 'ddls', [
        'name' => $name,
    ]);
}

function classifyDdls($ddls, $timestamp = null) {
    if(!$ddls) return null;
    $reply = [];
    $result = [
        'expired' => [],
        'critical' => [],
        'urgent' => [],
        'regular' => [],
    ];
    foreach($ddls as $ddl) {
        $remainTime = $ddl['time'] - ($timestamp ?? time());
        $time = date('Y/m/d', $ddl['time']);
        $description = "{$time} {$ddl['name']}";
        if($remainTime < 0) {
            $result['expired'][] = $description;
        } else if($remainTime <= 3 * 86400) {
            $result['critical'][] = $description;
        } else if($remainTime <= 7 * 86400) {
            $result['urgent'][] = $description;
        } else {
            $result['regular'][] = $description;
        }
    }
    if(count($result['expired'])) {
        $reply[] = "———— 逾期 ————\n".implode("\n", $result['expired']);
    }
    if(count($result['critical'])) {
        $reply[] = "———— 紧急 ————\n".implode("\n", $result['critical']);
    }
    if(count($result['urgent'])) {
        $reply[] = "———— 优先 ————\n".implode("\n", $result['urgent']);
    }
    if(count($result['regular'])) {
        $reply[] = "———— 其他 ————\n".implode("\n", $result['regular']);
    }
    return implode("\n", $reply);
}
