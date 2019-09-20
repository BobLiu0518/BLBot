<?php

global $Command, $Queue;

$choices = array();
$count = count($Command)-1;
if(!$count)leave("没有选项！");
for($n=1; $n <= $count; $n++)
	$choices[] = nextArg();
$Queue[]= sendBack($choices[rand(0, $count-1)]);

?>
