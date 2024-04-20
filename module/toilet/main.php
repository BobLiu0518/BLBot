<?php

global $Message;

$station = nextArg();
if(!$station) $station = $Message;
if(!$station) replyAndLeave('要查询什么车站呢？');
$data = json_decode(getData('toilet/data.json'), true);
$reply = '';
foreach($data as $cityName => $city){
	if($city[$station]) $reply .= "\n".$cityName.'地铁 '.$station." 站\n".$city[$station];
}
if(!strlen($reply)){
	replyAndLeave('没有查询到名为 '.$station.' 的车站哦…');
}else{
	replyAndLeave(trim($reply));
}

?>
