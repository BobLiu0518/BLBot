<?php

global $Message, $Command;

if(preg_match('/^(.*)天气$/', $Message, $match)){
    $Message = trim($match[1]);
    if(mb_strlen($Message, 'UTF-8') < 12){
        $Command[0] = 'middleWare/weather';
        loadModule('weather');
    }
    leave();
}

?>
