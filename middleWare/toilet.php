<?php

global $Message;

if(preg_match('/洗手间$/', $Message)){
    $Message = str_replace('洗手间', '', $Message);
    if(mb_strlen($Message, 'UTF-8') < 12){
        if(preg_match('/黄陂南路/', $Message)) $Message = '一大会址·黄陂南路';
        else if(preg_match('/新天地/', $Message)) $Message = '一大会址·新天地';
        loadModule('toilet');
    }
    leave();
}

?>
