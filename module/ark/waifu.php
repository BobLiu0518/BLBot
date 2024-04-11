<?php

global $Event;
requireLvl(2);

$operator = nextArg();
if(!$operator){
    replyAndLeave('没有指定看板哦…');
}

$lang = nextArg();
if(!$lang){
    $lang = '日文';
}
// $lang = str_replace('语', '文', str_replace('汉语', '中文', $lang));

setData('ark/waifu/'.$Event['user_id'], json_encode(array(
    'operator' => $operator,
    'lang' => $lang
)));

replyAndLeave('已设置干员 '.$operator.' ('.$lang.') 为你的看板～');

?>
