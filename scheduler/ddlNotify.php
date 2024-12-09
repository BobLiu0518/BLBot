<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'ddlNotify',
    true,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5 && intval(date('i', $timestamp)) % 5 == 0;
    },
    function ($timestamp) {
        global $CQ, $Database;
        loadModule('ddl.tools');
        $data = $Database->ddl->find([
            'notify' => ['$ne' => false, '$exists' => true],
        ]);
        foreach($data as $userData) {
            $timeDiff = $timestamp - strtotime($userData['notify']);
            if($timeDiff < 0 || $timeDiff >= 5 * 60) continue;
            $reply = classifyDdls($userData['ddls'], $timestamp);

            if($reply) {
                $CQ->sendPrivateMsg($userData['user_id'], $reply);
            }
        }
    }
);