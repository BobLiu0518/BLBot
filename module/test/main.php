<?php

	global $Queue, $CQ;
	requireSeniorAdmin();

	$cqv = $CQ->getVersionInfo();
	$cqEdition = ucfirst($cqv->coolq_edition);
	$plgVersion = $cqv->plugin_version;

	$reply = "酷Q ".$cqEdition." / CQHTTP ".$plgVersion." 正常运行中！";

	$Queue[]= sendBack($reply);

?>
