<?php

if(!config("alias",false))leave('功能不开放');

function setAlias($qq, $from, $to){
	$list = json_decode(getData("alias/".$qq.".json"),true);
	if(!$list) $list = [];
	if(count($list) >= 4){
		requireLvl(3, '设置更多别名', '使用 #alias.del 删掉一些');
		if(count($list) >= 8){
			requireLvl(4, '设置更多别名', '使用 #alias.del 删掉一些');
		}
	}
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
