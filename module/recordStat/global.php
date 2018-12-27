<?php

global $Queue;
requireSeniorAdmin();

if(fromGroup()){
    $Queue[]= sendBack(getUserCommandCount(0, 10));
}else{
    $Queue[]= sendBack(getUserCommandCount(0, nextArg()));
}

?>