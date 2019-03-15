<?php

global $Event, $Queue, $Message;

if(fromGroup()){

	$ciyaCount = (int)getData('ciyaCount/'.$Event['user_id'])+1;
	if(preg_match('/\[CQ:face,id=13\]/', $Message)){
		setData('ciyaCount/'.$Event['user_id'], $ciyaCount);
		if($ciyaCount % 5 == 0)
			$Queue[]= sendBack(sendImg(getImg("ciya.gif")));
	}
}

?>
