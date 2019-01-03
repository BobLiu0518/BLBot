<?php

global $Queue, $Text, $CQ, $Event;
use kjBot\Frame\Message;
requireSeniorAdmin();

if($Text == '')leave('没有参数！');

$escape = false;
$async = false;
$toGroup = false;
$toPerson = false;

do{
    $arg = nextArg();
    switch($arg){
        case '-escape':
            $escape = true;
            break;
        case '-async':
            $async = true;
            break;
        case '-toGroup':
            $toGroup = true;
            $id = nextArg();
            break;
        case '-toPerson':
            $toPerson = true;
            $id = nextArg();
            break;
        default:

    }
}while($arg !== NULL);

if($toGroup){
    if($async)
        $retData = $CQ->sendGroupMsgAsync($id, $Text, $escape);
    else
        $retData = $CQ->sendGroupMsg($id, $Text, $escape);
}else if($toPerson){
    if($async)
        $retData = $CQ->sendPrivateMsgAsync($id, $Text, $escape);
    else
        $retData = $CQ->sendPrivateMsg($id, $Text, $escape);
}else{
    if(isset($Event['group_id']))
        if($async)
            $retData = $CQ->sendGroupMsgAsync($Event['group_id'], $Text, $escape);
        else
            $retData = $CQ->sendGroupMsg($Event['group_id'], $Text, $escape);
    else
        if($async)
            $retData = $CQ->sendPrivateMsgAsync($Event['user_id'], $Text, $escape);
        else
            $retData = $CQ->sendPrivateMsg($Event['user_id'], $Text, $escape);
    
}


$Queue[]= sendBack("消息ID：".$retData->message_id);

?>
