<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'setDevGroupName',
    true,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5 && intval(date('i', $timestamp)) == 0;
    },
    function ($timestamp) {
        global $CQ;
        $CQ->setGroupName(intval(config('devgroup')), config('devgroupName').' @'.date('H', $timestamp).'点了！');
    }
);
