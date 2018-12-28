<?php

global $Queue, $Text, $CQ, $User_id;
use kjBot\Frame\Message;
requireSeniorAdmin();
if(!set_time_limit(360))leave('设置不超时失败！');
if($Text == NULL){
    $Text = "这是一条测试消息";
}else{
    $Text = $Text."
——集体广播";
}

$Queue[]= sendMaster("{$User_id} 广播了一条消息：{$Text}");

$groupList = $CQ->getGroupList();
$success = 0;
$silence = 0;
$error = 0;


foreach($groupList as $group){
    try{
        $CQ->sendGroupMsg($group->group_id, $Text);
        $success++;
    }catch(\Exception $e){
        if(-34 === $e->getCode()){
            $silence++;
        }else{
            $error++;
        }
        $Queue[]= sendMaster("Query {$group->group_id} failed: ".$e->getCode());
    }
    if($error>5)leave('错误次数过多，终止');
    $sleepTime = rand(1,3);
    sleep($sleepTime); //1-3秒延迟
}
$groupCount = count($groupList);
$Queue[]= sendPM("目前共有 {$groupCount} 个群，成功 {$success} 个，异常原因失败 {$error} 个，被 {$silence} 个群禁言中");
$Queue[]= sendMaster("目前共有 {$groupCount} 个群，成功 {$success} 个，异常原因失败 {$error} 个，被 {$silence} 个群禁言中");
?>