<?php

global $CQ, $User_id, $Queue;
requireSeniorAdmin();

$Queue[]= sendMaster($User_id." restarts bot");

$cleanCache = false;
do{
    $arg = nextArg();
    switch($arg){
        case '-cleanCache':
            $cleanCache = true;
            break;
        default:
    }
}while($arg !== NULL);

$CQ->setRestart($cleanCache);

?>