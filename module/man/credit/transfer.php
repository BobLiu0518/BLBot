<?php

global $Queue;
$msg=<<<EOT
向目标转账
收取5%手续费
用法：
#credit.transfer {目标} {金额}

目标可以使用 @

示例：
#credit.transfer @群主 100
EOT;

$Queue[]= sendBack($msg);

?>
