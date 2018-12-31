<?php

global $Queue;

$img = sendImg(getData("dt/pd".rand(1,23).".jpg"));
switch(rand(1,3))
{
    case 1:
    $message = $img."权限不足！停止你的瞎玩行为！";break;
    case 2:
    $message = $img."Permission denied...";break;
    case 3:
    $message = $img."没有权限…一定是哪里不对头";break;
}

$Queue[]= sendBack($message);

?>