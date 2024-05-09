<?php

loadModule('toilet.tools');

$station = nextArg();
if(!$station){
	replyAndLeave('要搜索什么车站呢？');
}else if(mb_strlen($station, 'UTF-8') < 3){
	replyAndLeave('搜索内容至少三个字哦…');
}

$stations = getFuzzyStationNames($station);
if(count($stations)){
	replyAndLeave("找到以下车站：\n".implode(' ', $stations));
}else{
	replyAndLeave('没有找到名称相似的车站…');
}

?>
