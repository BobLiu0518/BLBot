<?php

global $Message, $Queue;

if(strpos($Message, 'BL1040Bot 版本')!==false){
    loadModule('version');
    leave();
}
if(strpos($Message, 'BL1040Bot版本')!==false){
    loadModule('version');
    leave();
}

?>