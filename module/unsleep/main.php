<?php

global $CQ, $Event, $Queue;

if(coolDown("unsleep/{$Event['user_id']}")<0)leave('该命令每24小时只能使用一次！');
coolDown("unsleep/{$Event['user_id']}", 60*60*24);

$group = nextArg();

if($group == NULL || nextArg() != NULL){
    $Queue[]= sendBack("参数错误，请阅读以下内容！请注意，只能为自己解除禁言！");
    loadModule('help.unsleep');
    leave();
}

$CQ->setGroupBan($group, $Event['user_id'], 0);
$Queue[]= sendBack('已为 '.$Event['user_id'].' 在 '.$group.' 解除禁言！');

?>