<?php

global $Queue, $CQ, $Event;
loadModule('ban.tools');
requireSeniorAdmin();

/*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 加载命令成功！");

$banList = loadBanList();
/*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 加载列表成功！");
if($banId = nextArg())
{
    /*****/$CQ->sendGroupMsg($Event['group_id'],  (",",$banList));
    $banList['Blacklist'][] = $banId;
    /*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 修改列表成功！");
    saveBanList($banList);
    /*****/$CQ->sendGroupMsg($Event['group_id'], "Debug: 保存列表成功！");
}
else
{
    leave("没有QQ号！");
}

$Queue[]= sendBack($banId." 已被加入黑名单。");

?>