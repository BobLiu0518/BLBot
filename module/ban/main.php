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

$Queue[]= sendBack($banId." 已被加入黑名单。");

?>