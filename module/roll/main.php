<?php

global $Queue, $Command;
$countArg = count($Command)-1;
$min = 1;
$max = 100;

switch($countArg){
    case 1:
        $max = nextArg();
        break;
    case 2:
        $min = nextArg();
        $max = nextArg();
        break;
    default:

}

if($min == $max || !is_numeric($min) || !is_numeric($max))replyAndLeave('参数错误');

$min = intval($min);
$max = intval($max);

$Queue[]= replyMessage(rand($min, $max));

?>
