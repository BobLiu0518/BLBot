<?php

requireLvl(1);

$color = nextArg();
if(!$color) $color = '#'.strtoupper(dechex(rand(0, 255)).dechex(rand(0, 255)).dechex(rand(0, 255)));

$image = new Imagick();
$image->newImage(640, 360, $color);
$image->setImageFormat('png');
$draw = new ImagickDraw();
$draw->setGravity(Imagick::GRAVITY_NORTHWEST);
$draw->setFont(getFontPath('consolab.ttf'));
$draw->setFontSize(56);
$size = $image->queryFontMetrics($draw, $color);
$draw->setFillColor((new ColorCompare\Color($color))->getLab()['L'] > 70 ? '#000000' : '#FFFFFF');
$draw->annotation((640 - $size['textWidth']) / 2, (360 - $size['textHeight']) / 2, $color);

$image->drawImage($draw);
replyAndLeave(sendImg($image->getImageBlob()));

