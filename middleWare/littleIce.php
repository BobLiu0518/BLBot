<?php

global $Event, $Queue;
if(!rand(0,25)){
	if($Event['user_id'] == "2854196306" && !preg_match("/@我说/", $Event['message']) && !preg_match("/💡/", $Event['message']) && !preg_match("/艾特我说/", $Event['message']))
	    leave("[CQ:at,qq=2854196306] 不玩了");
	else if($Event['user_id'] == "2854196306")
	    leave();
}

?>
