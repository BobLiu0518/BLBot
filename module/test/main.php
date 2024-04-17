<?php

	global $Queue, $CQ;
	requireLvl(4);
	requireSeniorAdmin();

	$version = $CQ->getVersionInfo();
	$login = $CQ->getLoginInfo();
	$status = $CQ->getStatus();
	$status = var_export($status, true);
	$reply = <<<EOT
{$version->app_name} {$version->app_version}
Protocol {$version->protocol_version}
QQ NT Protocol {$version->nt_protocol}

Logged in as {$login->nickname} ({$login->user_id})

Status:
{$status}
EOT;

	$Queue[]= replyMessage($reply);

?>
