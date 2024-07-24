<?php

global $Queue;
loadModule('bv.tools');

$bv = nextArg();
if(!preg_match('/^BV[a-km-zA-HJ-NP-Z1-9]+$/', $bv)){
	replyAndLeave('请输入有效的BV号哦～');
}
$Queue[]= replyMessage(B23::BV2AV($bv));

?>
