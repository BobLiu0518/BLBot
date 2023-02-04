<?php

global $Text, $Queue;

$args = '';
while($nextArg = nextArg()){
	$args .= ' '.$nextArg;
}
$Text = trim($args.$Text);

$Morse = new Morse\Text();
$Morse->setWordSeparator(' / ');
$result = $Morse->toMorse($Text);

$Queue[]= replyMessage($result);

$Wav = new Morse\Wav();
$audio = $Wav->generate($Text);
$Queue[]= sendBack(sendRec($audio));

?>
