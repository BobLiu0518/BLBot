<?php

global $Message, $Command;

if(preg_match('/(洗手间|卫生间|厕所)$/', $Message)){
    $Message = preg_replace('/(洗手间|卫生间|厕所)/', '', $Message);
    if(mb_strlen($Message, 'UTF-8') < 12){
        $Command[0] = 'middleWare/toilet';
        if(preg_match('/(黄陂南路|新天地)/', $Message, $match)) $Message = '一大会址·'.$match[1];
        loadModule('toilet');
    }
    leave();
}

?>
