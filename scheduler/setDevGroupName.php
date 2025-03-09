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
        $time = mb_chr(0x32BF + intval(date('m', $timestamp))).mb_chr(0x33DF + intval(date('d', $timestamp))).mb_chr(0x3358 + intval(date('H', $timestamp)));
        $CQ->setGroupName(intval(config('devgroup')), config('devgroupName').' @'.$time.'了！');
    }
);
