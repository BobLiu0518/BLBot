<?php

global $Queue;

$msg=<<<EOT
在 BL1040Bot 上绑定你的 osu!
用法：
#osu.bind {用户名}

此处用户名不应该有任何额外字符

示例：
#osu.bind qwertyuiop
EOT;

$Queue[]= sendBack($msg);

?>
