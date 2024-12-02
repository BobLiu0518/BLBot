<?php

global $Queue, $CQ;
requireLvl(6);

$version = $CQ->getVersionInfo();
$login = $CQ->getLoginInfo();
$status = $CQ->getStatus();
$status = var_export($status, true);
$reply = <<<EOT
{$version->app_name} {$version->app_version}
OneBot Protocol {$version->protocol_version}
QQ NT {$version->nt_protocol}

Logged in as {$login->nickname} ({$login->user_id})

Status:
{$status}
EOT;

$Queue[] = replyMessage($reply);