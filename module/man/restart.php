<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

重启 bot。

用法：
#restart [-cleanCache]

参数：
-cleanCache 顺带清理一下缓存
EOT;
$Queue[]= sendBack($msg);

?>
