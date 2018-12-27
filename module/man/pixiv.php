<?php

global $Queue;
$msg=<<<EOT
Pixiv !
用法：
#pixiv.{search|IID}

可能会因为网络原因无法实现
具体用法请查看下一级 help
示例请看下一级 help
EOT;

$Queue[]= sendBack($msg);

?>
