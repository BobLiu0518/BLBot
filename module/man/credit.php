<?php

global $Queue;
$msg=<<<EOT
BL1040Bot 金币 系列命令
用法：
#credit
#credit.{check|transfer} [参数列表]

不指定下级模块时是 credit.check 模块的别名
具体用法请查看下一级 help。

示例：
#credit （等价于 #credit.check）
#credit @苟群主 （等价于 #credit.check @苟群主）
EOT;

$Queue[]= sendBack($msg);

?>
