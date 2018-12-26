<?php

global $Event, $Queue;
use kjBot\SDK\CQCode;

switch($Event['notice_type']){
    case 'group_increase':
        if($Event['user_id'] != config('bot')){
            $Queue[]= sendBack(CQCode::At($Event['user_id']).' 欢迎加入本群，请阅读群公告~ 我是 BL1040Bot，发送 #help 查看 BL1040Bot 的帮助~');
        }else{
            $Queue[]= sendBack('BL1040Bot 已入驻本群，发送 #help 查看帮助。');
        }
        break;
    case 'group_decrease':
        if($Event['sub_type']=='kick_me'){
            $Queue[]= sendMaster('Being kicked from group '.$Event['group_id'].' by '.$Event['operator_id']);
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
        }
        break;
    default:
        
}

?>