<?php

global $Queue, $Event;
use kjBot\SDK\CQCode;
loadModule('credit.tools');
loadModule('exp.tools');

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
$QQ = $QQ??$Event['user_id'];

$Queue[]= sendBack(CQCode::At($QQ)."\n您的金币余额为 ".getCredit($QQ)."\n您的经验值为 ".getExp($QQ)."，等级为 Lv".getLvl($QQ));

?>
