<?php

global $Queue;
loadModule('bv.tools');

$av = nextArg();
if(preg_match('/^\d+$/', $av)){
	$av = 'av'.$av;
}
if(!preg_match('/^(av)?\d+$/i', $av)){
	replyAndLeave('请输入有效的av号哦～');
}
$Queue[]= replyMessage(B23::AV2BV($av));

?>
