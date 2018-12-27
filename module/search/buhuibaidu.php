<?php

global $Queue;
$query='';
do{
    $arg = nextArg();
    $query.=urlencode($arg).'+';
}while($arg!==NULL);
$url = file_get_contents('http://suo.im/api.php?url=https://baidu.com/s?word='.rtrim($query, '+'));
$Queue[]= sendBack(str_replace("http://suo.im/", "http://www.suo.im/", $url));

?>
