<?php

global $Queue;
$msg=<<<EOT
报告一个bug
使用改命令但是不是报告bug的
将会受到黑名单的制裁
如果使用该命令没有给出回复
请直接联系2018962389
用法：
#issue
{标题}
[细节内容]

该命令有1小时冷却时间

示例：
#issue
BL1040Bot无法发送语音
在xxx情况下，xxx人使用了xxx命令，
但是没有返回应有的语音
（示例仅供参考，请勿模仿）
EOT;

$Queue[]= sendBack($msg);

?>
