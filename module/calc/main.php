<?php

requireLvl(1);

global $Queue, $Message, $Text;
$args = '';
while($nextArg = nextArg()){
	$args .= ' '.$nextArg;
}
$exp = trim($args.$Text);
if(!$exp){
	if(!$Message){
		replyAndLeave('想让 Bot 算什么呢？');
	}
	$exp = $Message;
}

$map = [
	'（' => '(',
	'）' => ')',
	'＋' => '+',
	'－' => '-',
	'＊' => '*',
	'×' => '*',
	'／' => '/',
	'％' => '%',
	'＾' => '^',
	'π' => '$pi',
];
foreach($map as $from => $to){
	$exp = str_replace($from, $to, $exp);
}
$exp = rtrim($exp, '=＝');

use NXP\MathExecutor;
$executor = new MathExecutor();

try{
	$Queue[]= replyMessage($exp.' = '.round($executor->execute($exp), 10));
}catch(\Exception $e){
	replyAndLeave('计算 '.$exp.' 时发生错误：'.preg_replace('/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/', ' $1', str_replace('Exception', '', array_pop(explode('\\', get_class($e))))));
}

?>
