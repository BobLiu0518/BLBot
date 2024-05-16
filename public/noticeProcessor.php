<?php

global $Event, $Queue, $CQ;
use kjBot\SDK\CQCode;

switch($Event['notice_type']){
    case 'group_increase':
        if($Event['user_id'] != config('bot')){
            $addGroupMsg = getData('addGroupMsg/'.$Event['group_id']);
            if(!$addGroupMsg)
                $Queue[]= sendBack(CQCode::At($Event['user_id']).config('addGroupMsg',' 欢迎加入本群，请阅读群公告~ 我是 BLBot，发送 #help 查看帮助~'));
            else
                $Queue[]= sendBack(CQCode::At($Event['user_id']).trim($addGroupMsg));
        }else{
            $Queue[]= sendBack('BLBot 已加入本群，发送 #help 查看指令列表～');
        }
        break;
    case 'group_decrease':
        if($Event['sub_type']=='kick_me'){
            $Queue[]= sendMaster('Being kicked from group '.$Event['group_id'].' by '.$Event['operator_id']);
            $Queue[]= sendDevGroup('Being kicked from group '.$Event['group_id'].' by '.$Event['operator_id']);
        }
        break;
    case 'group_admin':
        if($Event['user_id'] == config('bot')){
            if($Event['sub_type']=='set'){
                $prefix = 'Get ';
            }elseif($Event['sub_type']=='unset'){
                $prefix = 'Lost ';
            }
            $Queue[]= sendMaster($prefix.'admin in group '.$Event['group_id']);
            $Queue[]= sendDevGroup($prefix.'admin in group '.$Event['group_id']);
        }
        break;
    case 'notify':
        switch($Event['sub_type']){
            default:
        }
        break;
    default:

}

?>
