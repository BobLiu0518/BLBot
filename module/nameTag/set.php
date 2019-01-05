<?php

global $CQ, $Event, $Queue;

if($tag = nextArg())leave('请输入需要的头衔！');
if(mb_strlen($tag) > 6)leave('头衔过于长！');
$CQ->setGroupSpecialTitle($Event['group_id'], $Event['user_id'], $tag);
$Queue[]= sendBack('[CQ:at,qq='.$Event['user_id'].'] 已设置群头衔 '.$tag.' ！');

?>