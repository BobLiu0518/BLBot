<?php

global $Event, $Queue;
loadModule('credit.tools');
use kjBot\SDK\CQCode;
requireSeniorAdmin();

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
$transfer = abs((int)nextArg());
transferCredit($QQ, $Event['user_id'], $transfer, 1);

$Queue[]= sendBack('抢劫 '.CQCode::At($QQ).' 成功，对方的余额为 '.getCredit($QQ));
?>
