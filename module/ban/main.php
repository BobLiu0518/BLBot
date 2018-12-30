<?php

global $Queue;
loadModule('ban.tools');
requireSeniorAdmin();

$banList = loadBanList();
if($banId = nextArg())
{
    $banList['Blacklist'][] = $banId;
    saveBanList($banList);
}
else
{
    leave("没有QQ号！");
}

$Queue[]= sendBack($banId." 已被加入黑名单。");

?>