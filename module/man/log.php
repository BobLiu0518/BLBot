<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

发送 IIS 产生的 log。

用法：
#log [日期]

日期格式：yymmdd，如：
181008（2018/10/08）
不指定日期时默认当前日期。
EOT;
$Queue[]= sendBack($msg);

?>
