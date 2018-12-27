<?php

global $CQ, $Event;

date_default_timezone_set('Asia/Shanghai');

if(preg_match('/see you next time/', $Event['message'])){
    if(!isSeniorAdmin()){
        try{
            $CQ->setGroupBan($Event['group_id'], $Event['user_id'], strtotime(((date('H')>=0&&date('H')<=7)?'':'next day').' 7 am')-time());
        }catch(\Exception $e){leave();}
    }
    
}

if(preg_match('/bot next door/', $Event['message'])){
    if(!isSeniorAdmin())
    {
        try{
            $CQ->setGroupKick($Event['group_id'], $Event['user_id']);
        }catch(\Exception $e){leave();}
    }
}


?>