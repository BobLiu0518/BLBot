<?php

global $Event;
requireLvl(3);
loadModule('weather.tools');

$place = nextArg(true);

if($place) {
    ['name' => $name, 'address' => $address, 'typeName' => $typeName] = searchPoi($place);
    setData('weather/user/'.$Event['user_id'], $place);
    replyAndLeave("已将默认地点设置为 {$name}（{$address}，{$typeName}）～");
}

$place = getData('weather/user/'.$Event['user_id']);
if(!$place) {
    replyAndLeave('要设置什么为默认地点呢？');
}

delData('weather/user/'.$Event['user_id']);
replyAndLeave('已清除默认地点～');
