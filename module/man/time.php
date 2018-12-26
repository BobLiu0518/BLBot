<?php

global $Queue;
$msg=<<<EOT
让舰娘报告当前时间（东京时间）
需要整点报时服务的请联系2018962389

用法：
#time
报告时间

#time.update
{
    网址
}
*高级命令
*需要 M 权限
更新舰娘语音


EOT;

$Queue[]= sendBack($msg);

?>
