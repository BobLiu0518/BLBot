<?php

global $Queue;

$msg=<<<EOT
设置你的默认模式
用法：
#osu.setMode {模式名}

模式名有彩蛋~

示例：
#osu.setMode taiko
#osu.setMode 屙屎
EOT;

$Queue[]= sendBack($msg);

?>