<?php

global $Event, $Queue;
loadModule('credit.tools');
use kjBot\SDK\CQCode;

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
$transfer = abs((int)nextArg());
transferCredit($Event['user_id'], $QQ, $transfer);

$fee = intval(0.05 * $transfer);

$Queue[]= sendBack('转账给 '.CQCode::At($QQ).' 成功（手续费 '.$fee.' 金币），您的余额为 '.getCredit($Event['user_id']));
?>
