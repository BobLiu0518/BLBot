<?php

global $Queue;
$msg=<<<EOT
睡一觉吧！
用法：
#sleep {时间}

时间可以是以英文表示的一个具体时间。如"next day 7 am"。
如果要表示距离现在多久，请使用类似"+2 hours", "+10 minutes"的格式。
时间均为北京时间

示例：
#sleep 1minute
#sleep next day 7 am

输入“see you next time”有惊喜
输入“bot next door”有大惊喜
EOT;

$Queue[]= sendBack($msg);

?>
