<?php

global $CQ, $Event, $Queue;

$CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id']);
$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已删除群头衔！');

?>