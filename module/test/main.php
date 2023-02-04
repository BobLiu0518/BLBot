<?php

	global $Queue, $CQ;
	requireLvl(4);
	requireSeniorAdmin();

	$cqv = $CQ->getVersionInfo();
	$cqEdition = ucfirst($cqv->coolq_edition);
	$plgVersion = $cqv->plugin_version;
	$stat = $CQ->getStatus();

	if($stat->good && $stat->online)
		$reply = "酷Q ".$cqEdition." / CQHTTP ".$plgVersion." 正常运行中！";
	else
		$reply = "酷Q ".$cqEdition." / CQHTTP ".$plgVersion." 运行异常！";

	$Queue[]= replyMessage($reply);

?>
