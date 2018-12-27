<?php

global $Queue;
$msg=<<<EOT
傻×都能看懂的的帮助文档
用法：
#help

示例：
#help
EOT;

$Queue[]= sendBack($msg);

?>
