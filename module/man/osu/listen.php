<?php

global $Queue;

$msg=<<<EOT
享受音乐（全损音质）
用法：
#osu.listen {谱面集ID}

示例：
#osu.listen 688121
EOT;

$Queue[]= sendBack($msg);

?>
