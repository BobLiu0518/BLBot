<?php

global $Message, $Command;

loadModule('toilet.tools');

if($Command[0] == 'middleWare/toilet'){
	$station = trim($Message);
}else{
	$station = implode(' ', array_splice($Command, 1));
}
if(!$station){
	if($Command[0] == 'middleWare/toilet') leave();
	else replyAndLeave('要查询什么车站呢？');
}

$reply = getExactStationData($station);
if(!$reply){
	$reply = '没有查询到名为 '.$station.' 的车站哦…';
	$similarNames = getFuzzyStationNames($station);
	if(count($similarNames)){
		$reply .= "\n你可能想找：".implode(' ', $similarNames);
	}else if($Command[0] == 'middleWare/toilet'){
		leave();
	}
}

replyAndLeave($reply);

?>
