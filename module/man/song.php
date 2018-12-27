<?php

global $Queue;

$msg=<<<EOT
听首歌吧！
用法：
#roll [源] {搜索关键字}
源当前可选：-qq|-163，不指定默认 -163

示例：
#song China-E
#song -qq Fuck徐梦圆
EOT;

$Queue[]= sendBack($msg);

?>