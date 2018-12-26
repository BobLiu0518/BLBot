<?php

requireInsider();

global $Queue, $Command;
$countArg = count($Command)-1;

switch($countArg){
    case 0:loadModule('vote.recent');break;
    case 1:loadModule('vote.detail');break;
    case 2:loadModule('vote.confirm');break;
    default:$Queue[]=sendBack('参数错误！');break;
}

?>
