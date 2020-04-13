<?php

global $Queue, $Command;

switch(count($Command)-1){

case 1:

	$object = nextArg();
	$message = <<<EOT
{$object}是什么呢？相信很多小伙伴都好奇{$object}是什么，下面就让小编带大家一起了解吧。
{$object}，其实就是{$object}，大家可能会感到很惊讶，怎么会有{$object}呢？但事实就是这样，小编也感到非常惊讶。
那么这就是关于{$object}的事情了，大家有什么想法呢？欢迎在评论区告诉小编一起讨论哦！
EOT;
	break;

case 2:
	$noun = nextArg();
	$adjective = nextArg();

	$message = <<<EOT
{$noun}{$adjective}是怎么回事呢？{$noun}相信大家都很熟悉，但是{$noun}{$adjective}是怎么回事呢？下面就让小编带大家一起了解吧。
{$noun}{$adjective}，其实就是{$adjective}的{$noun}，大家可能会感到很惊讶，{$adjective}的{$noun}怎么会{$adjective}呢？但事实就是这样，小编也感到非常惊讶。
那么这就是关于{$noun}{$adjective}的事情了，大家有什么想法呢？欢迎在评论区告诉小编一起讨论哦！
EOT;
	break;

default:
	$message = <<<EOT
#yxh 命令，接受一个或两个参数，生成一段营销号风文本。
EOT;
	break;
}

$Queue[]= sendBack($message);

?>
