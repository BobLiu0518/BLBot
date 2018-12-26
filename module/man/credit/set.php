<?php

global $Queue;
$msg=<<<EOT
*高级命令
*需要 SA 权限

设置余额
用法：
#credit.set [目标]

不指定目标时为设置自己的余额
目标可以使用 @
EOT;

$Queue[]= sendBack($msg);

?>
