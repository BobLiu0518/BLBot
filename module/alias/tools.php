<?php

if(!config("alias",false))leave('功能不开放');

function setAlias($qq, $from, $to){
	$list = json_decode(getData("alias/".$qq.".json"),true);
	$list[$from] = $to;
	setData("alias/".$qq.".json", json_encode($list));
}

function delAlias($qq,$alias){
	$list = json_decode(getData("alias/".$qq.".json"),true);
	unset($list[$alias]);
	setData("alias/".$qq.".json", json_encode($list));
}

function chkAlias($qq){
	$list = json_decode(getData("alias/".$qq.".json"),true);
	return $list;
}

?>
