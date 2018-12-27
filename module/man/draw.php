<?php

global $Queue;
$msg=<<<EOT
*未知命令

效果未知

用法：
#draw {模板名} [数量]
数量是 1-10 的整数，
不指定或超限时默认 1。

#draw.generate
含有参数，但参数未知
*高级命令
*需要 SA 权限

EOT;
$Queue[]= sendBack($msg);

?>
