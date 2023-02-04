<?php

global $Event, $Queue;
loadModule('credit.tools');
requireLvl(2);
use kjBot\SDK\CQCode;

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
if(!$QQ){
    replyAndLeave('要转账给谁呢？');
}

$transfer = abs((int)nextArg());
transferCredit($Event['user_id'], $QQ, $transfer);

$fee = intval(0.01 * $transfer + 1);

$Queue[]= replyMessage('转账 '.$transfer.' 金币给 '.CQCode::At($QQ).' 成功（手续费 '.$fee.' 金币），您的余额为 '.getCredit($Event['user_id']));

?>
