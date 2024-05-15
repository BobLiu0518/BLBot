<?php

global $Text, $Queue;

$args = '';
while($nextArg = nextArg()){
	$args .= ' '.$nextArg;
}
$Text = trim($args.$Text);

$Morse = new Morse\Text();
$Morse->setWordSeparator(' / ');
$result = $Morse->fromMorse($Text);

$Queue[]= replyMessage($result);

?>
