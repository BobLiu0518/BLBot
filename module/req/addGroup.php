<?php

	global $Queue, $CQ;
	requireSeniorAdmin();

	$flag = nextArg();
	$approve = nextArg();
	if($approve === NULL && $approve = (bool)intval($approve))leave("参数错误！");
	$CQ->setGroupAddRequest($flag, 'invite', $approve);
	$Queue[]= sendBack('已处理 flag '.$flag.'，操作为 '.$approve);
	$Queue[]= sendMaster($Event['user_id'].' 处理了 flag'.$flag.'，操作为 '.$approve);

?>
