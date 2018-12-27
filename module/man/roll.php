<?php

global $Queue;

$msg=<<<EOT
生成随机数
用法：
#roll
#roll [最小值]
#roll [最小值] [最大值]
不指定最大值或最小值时默认
最大值 10 最小值 1（好像）

示例：
#roll
#roll 0
#roll 114514 1919810
EOT;

$Queue[]= sendBack($msg);

?>