<?php

global $Queue;
$msg=<<<EOT
让 BL1040Bot 为你在某群聊解除禁言
用法：
#unsleep {群号}

该命令有24小时冷却

示例：
#unsleep 885444381
EOT;

$Queue[]= sendBack($msg);

?>
