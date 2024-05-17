<?php

global $Command;
requireLvl(1);

if(count($Command) == 1){
	replyAndLeave('不知道你想查询什么呢…');
}else{
	$char = trim(implode(' ', array_splice($Command, 1)).$Text);
}
replyAndLeave(trim(shell_exec('../module/unicode/unidata.py '.escapeshellarg($char))));

?>
