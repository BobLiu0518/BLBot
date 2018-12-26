<?php

global $Queue;
$msg=<<<EOT
详细的帮助文档
用法：
#man {.下一级命令}[.下一级命令]
#man [-advanced]

示例：
#man
#man -advanced
#man.help
#man.pixiv.IID
EOT;

$Queue[]= sendBack($msg);

?>
