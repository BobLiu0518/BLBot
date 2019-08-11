<?php

global $CQ, $Event, $Queue;

date_default_timezone_set('Asia/Shanghai');

if(fromGroup())
{
    if(preg_match('/see you next time/', $Event['message']))
    {
        if(true/*!isSeniorAdmin()*/)
        {
            try
            {
                $CQ->setGroupBan($Event['group_id'], $Event['user_id'], strtotime(((date('H')>=0&&date('H')<=7)?'':'next day').' 7 am')-time());
            }catch(\Exception $e){leave();}
        } 
    }
    if(preg_match('/bot next door/', $Event['message']))
    {
        if(true/*!isSeniorAdmin()*/)
        {
            try
            {
                $CQ->setGroupKick($Event['group_id'], $Event['user_id']);
                $Queue[]= sendBack(":D");
            }catch(\Exception $e){leave();}
        }
    }
}

?>
