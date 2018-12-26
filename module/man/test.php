<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

如果具有权限则返回一句 test。
可用于测试权限以及判断 bot 是
否还活着。

用法：
#test
EOT;
$Queue[]= sendBack($msg);

?>
