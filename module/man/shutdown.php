<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 M 权限
*高危险性

关闭 bot。请注意，由于这个命令
的原理是杀掉 CoolQ 进程，所以
无法恢复，除非手动打开进程。

用法：
#shutdown
EOT;
$Queue[]= sendBack($msg);

?>
