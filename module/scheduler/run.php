<?php

global $Queue;
requireLvl(6);

$Schedulers = [];
$scheduler = nextArg();
if(!$scheduler) {
    replyAndLeave('No scheduler specified.');
}
try {
    require_once "../scheduler/{$scheduler}.php";
} catch (Exception $e) {
    replyAndLeave($e);
}
$Queue[] = sendBack("Running scheduler {$scheduler} ...");
$Schedulers[0]->setTime(time());
$Schedulers[0]->run();