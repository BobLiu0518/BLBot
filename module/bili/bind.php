<?php

global $Event;

if($uid = getData("bili/user/".$Event['user_id']))leave("您已绑定账号".$uid."，如需解绑请使用 #bili.unbind ！");
$uid = nextArg();
if(!$uid)leave("请填写uid！");
setData("bili/user/".$Event['user_id'], $uid);
leave("绑定成功！");

?>
