<?php

global $CQ, $Event, $Queue;

if(fromGroup())
{
    if(preg_match('/收到福袋，请使用新版手机QQ查看/', $Event['message']))
    {
        try
        {
            $CQ->setGroupBan($Event['group_id'], $Event['user_id'], 60*60);
        }catch(\Exception $e){leave();}
    }
}

?>
