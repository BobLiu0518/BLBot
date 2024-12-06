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