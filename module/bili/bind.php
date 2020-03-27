<?php

global $Event;

if($uid = getData("bili/user/".$Event['user_id']))leave("您已绑定账号".$uid."，如需解绑请使用 #bili.unbind ！");
$uid = ltrim(ltrim(nextArg(), 'uid'), 'UID');
if(!$uid)leave("请填写uid！");
else if(!is_numeric($uid)) leave('uid不合法！');
setData("bili/user/".$Event['user_id'], $uid);
leave("绑定成功！");

?>
