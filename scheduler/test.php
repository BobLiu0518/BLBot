<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'test',
    false,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5;
    },
    function ($timestamp) {
        global $Queue;
        $Queue[] = sendMaster('Scheduler 测试中…');
        throw new Exception('Scheduler 想不开了！');
    }
);