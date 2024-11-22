<?php

function getMottoDb() {
    static $db;
    if(!$db) {
        $db = new BLBot\Database('motto', [
            'default' => ['motto' => null],
        ]);
    }
    return $db;
}

function getMotto($user_id) {
    return getMottoDb()->get(intval($user_id))['motto'];
}

function setMotto($user_id, $motto) {
    return getMottoDb()->set(intval($user_id), [
        'motto' => $motto,
    ]);
}

function delMotto($user_id) {
    return getMottoDb()->delete(intval($user_id));
}