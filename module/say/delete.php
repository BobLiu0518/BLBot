<?php

global $CQ;
requireSeniorAdmin();

if(!$msgID = nextArg())
    leave('没有消息ID！');
else
    $CQ->deleteMsg($msgID);

?>