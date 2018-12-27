<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 M 权限

向所有群发送广告，
会自动添加头尾。

用法：
#AD
{
    广告内容
}
EOT;
$Queue[]= sendBack($msg);

?>
