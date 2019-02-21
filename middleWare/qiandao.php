<?php

global $CQ, $Event, $Queue;

if(fromGroup() && $CQ->getGroupMemberInfo($Event['group_id'], config("bot","2094361499"))->role == "owner")
{
    if(preg_match('/群签到/', $Event['message']))
    {
        try
        {
            $CQ->setGroupBan($Event['group_id'], $Event['user_id'], 15*60);
        }catch(\Exception $e){leave();}
    }
}

?>
