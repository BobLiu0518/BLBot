<?php

global $Message, $Queue;

if(preg_match('/^比亚迪^/', $Message) || preg_match('/^BYD^/', $Message))
{
    $Queue[]= sendBack("佛山: 想要");
}

?>