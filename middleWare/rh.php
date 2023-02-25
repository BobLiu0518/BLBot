<?php

global $Message;

if(fromGroup()){
	$rh = ["赛马","🐎","🏇","🐴","🦄"];

	$rhData = json_decode(getData('rh/'.$Event['group_id']), true);
	if($rhData['status'] == 'starting'){
		$rh[]= $rhData['horse'];
		$rh[]= '赛'.$rhData['horse'];
	}

	foreach($rh as $word){
		if($word == $Message){
	    		loadModule('rh.join');leave();
		}
	}
}

?>
