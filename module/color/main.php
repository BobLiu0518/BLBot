<?php

requireLvl(1);

$color = nextArg();
if(!$color) $color = '#'.strtoupper(dechex(rand(0, pow(2, 24) - 1)));

$image = new Imagick();
$image->newImage(320, 180, $color);
$image->setImageFormat('png');
$draw = new ImagickDraw();

// $image->drawImage($draw);
replyAndLeave($color.sendImg($image->getImageBlob()));

