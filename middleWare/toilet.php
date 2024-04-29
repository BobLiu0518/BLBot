<?php

global $Message, $Command;

if(preg_match('/^(.+)(洗手间|卫生间|厕所|洗手間|衛生間|廁所)$/', $Message, $match)){
    $Message = trim($match[1]);
    if(mb_strlen($Message, 'UTF-8') < 12){
        $Command[0] = 'middleWare/toilet';
        loadModule('toilet');
    }
    leave();
}

?>
