<?php

global $Queue, $Event;
use kjBot\SDK\CQCode;
loadModule('credit.tools');
loadModule('exp.tools');
loadModule('attack.tools');

$QQ = nextArg();
if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
    $QQ = parseQQ($QQ);
}
$QQ = $QQ??$Event['user_id'];

$exp = getExp($QQ);
$level = getLvl($QQ);
$status = getStatus($QQ);
$statusEnd = getStatusEndTime($QQ);
$msg = "您的金币余额为 ".getCredit($QQ)."\n您的经验值为 ".$exp."，等级为 Lv".$level;
switch($level) {
	case 2: $msg .= "\n再签到 ".(30-$exp)." 天即可升级 Lv3～"; break;
	case 1: $msg .= "\n再签到 ".(7-$exp)." 天即可升级 Lv2～"; break;
	case 0: $msg .= "\n签到后即可升级 Lv1 哦～"; break;
}
switch($status) {
	case 'imprisoned': $msg .= "\n当前身处监狱中，预计 ".$statusEnd." 出狱"; break;
	case 'confined': $msg .= "\n当前身处监狱禁闭室中，预计 ".$statusEnd." 出狱"; break;
	case 'arknights': $msg .= "\n当前身处异世界"; break;
	case 'hospitalized': $msg .= "\n当前身处医院中，预计 ".$statusEnd." 出院"; break;
	case 'free': default: break;
}
$Queue[]= replyMessage($msg);

?>
