<?php

global $Event, $Queue;
if($Event['user_id'] == "2854196306" && !preg_match("聊天", $Event['message']))
    leave("[CQ:at,qq=2854196306] 不玩了");

?>