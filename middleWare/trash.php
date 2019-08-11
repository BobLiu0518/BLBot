<?php

global $Message;

if(strrchr($Message, "是什么垃圾") == "是什么垃圾"){
    $Message = str_replace("是什么垃圾","",$Message);
    loadModule('trash');leave();
}

?>
