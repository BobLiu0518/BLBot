<?php

global $Queue;
loadModule('ban.tools');
requireSeniorAdmin();

/*****/$Queue[]= sendBack("Debug: 加载命令成功！");

$banList = loadBanList();
/*****/$Queue[]= sendBack("Debug: 加载列表成功！");
if($banId = nextArg())
{
    $banList['Blacklist'][] = $banId;
    /*****/$Queue[]= sendBack("Debug: 更改列表成功！");
    saveBanList($banList);
    /*****/$Queue[]= sendBack("Debug: 保存列表成功！");
}
else
{
    leave("没有QQ号！");
}

$Queue[]= sendBack($banId." 已被加入黑名单。");

?>