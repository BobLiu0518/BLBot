<?php

global $Message, $Queue;

$mol = array("生命的意义是什么","what's the meaning of life");

foreach($mol as $word)
    if(preg_match('/'.$word.'/', $Message))
    {
        $Queue[]= sendBack("是活够了以后自杀（溜）");
        break;
    }
?>
