<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

显示 bot 宿主机的信息。

用法：
#status
EOT;
$Queue[]= sendBack($msg);

?>
