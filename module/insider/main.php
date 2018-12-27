<?php

global $Queue, $User_id;

$agreement=<<<EOT
感谢您使用 BL1040Bot！

如果你愿意加入BL1040Bot
的测试工作，并且愿意执行
把bug报告给开发者的义务，
你可以加入BL1040Bot内测
计划，并且有权限使用内测
中的命令。你可以随时取消
自己的内测权限。

如果您愿意，请输入
#insider.verify
EOT;

if(trim(getData('insider/'.$User_id))=='')
setData('insider/'.$User_id, 'read');
$Queue[]= sendPM($agreement); //仅在私聊中发送用户协议

?>
