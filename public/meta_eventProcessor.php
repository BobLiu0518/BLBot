<?php

date_default_timezone_set('Asia/Shanghai');

$pool = Spatie\Async\Pool::create();
$time = time();
$path = '../scheduler/';
$files = scandir($path);

$Schedulers = [];
foreach($files as $file) {
    if(preg_match('/\.php$/', $file)) {
        include_once $path.$file;
    }
}

foreach($Schedulers as $scheduler) {
    $scheduler->setTime($time);
    if($scheduler->validate()) {
        $pool->add([$scheduler, 'run']);
    }
}

$pool->wait();
