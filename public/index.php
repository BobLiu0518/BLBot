<?php

if(function_exists('fastcgi_finish_request'))fastcgi_finish_request();

require('init.php');

use kjBot\Frame\Message;

try{
    $listen = config('Listen');
    if($listen !== NULL && ($Event['group_id'] == $listen || $listen == $Event['user_id'])){
        $Queue[]= sendMaster('['.date('Y-m-d H:i:s', $Event['time']-86400)."] {$Event['user_id']} say:\n{$Event['message']}", false, true);
    }

    $whiteList = json_decode(getData('grouplist.json'), true)['groups'];
    if(!in_array($Event['group_id'], $whiteList) && $Event['group_id'] != NULL){
        $Queue[]= sendMaster('No access at '.$Event['group_id']);
        $CQ->setGroupLeave($Event['group_id']);
        exit();
    }

    switch($Event['post_type']){
        case 'message':
        case 'notice':
        case 'request':
        case 'meta_event':
            require($Event['post_type'].'Processor.php');
            break;
        default:
            $Queue[]= sendMaster('Unknown post type '.$Event['post_type'].', Event:'."\n".var_export($_SERVER, true));
    }

    //调试
    if($Debug && $Event['user_id'] == $DebugListen){
        $Queue[]= sendMaster(var_export($Event, true)."\n\n".var_export($Queue, true));
    }

}catch(\Exception $e){
    $Queue[]= sendBack($e->getMessage(), false, true);
}

try{
    //将队列中的消息发出
    foreach($Queue as $msg){
        $MsgSender->send($msg);
    }
}catch(\Exception $e){
    if($e->getCode() == -11){
        try{
            $MsgSender0->send($msg);
        }catch(\Exception $e){}
    }
    setData('error.log', var_dump($Event).$e.$e->getCode()."\n", true);
}

?>
