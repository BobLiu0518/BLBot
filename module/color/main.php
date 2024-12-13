<?php

$color = nextArg();
if($color === null){
    $color = '#'.substr('000000'.dechex(rand(0, pow(2, 24) - 1)), -6);
}else if(preg_match('/^[0-9a-f]{6}$/i', $color)){
    $color = '#'.$color;
}

try{
    $pixel = new ImagickPixel($color);
}catch(\Exception $e){
    replyAndLeave('无法识别颜色…');
}

$image = new Imagick();
$image->newImage(640, 360, $pixel);
$image->setImageFormat('png');
$draw = new ImagickDraw();
$draw->setGravity(Imagick::GRAVITY_NORTHWEST);
$draw->setFont(getFontPath('consolab.ttf'));
$draw->setFontSize(56);

$rgb = $pixel->getColor();
unset($rgb['a']);
$colorCompare = new ColorCompare\Color($rgb);
$hex = strtoupper($colorCompare->getHex());

$size = $image->queryFontMetrics($draw, $hex);
$draw->setFillColor($colorCompare->getLab()['L'] > 70 ? '#000000' : '#FFFFFF');
$draw->annotation((640 - $size['textWidth']) / 2, (360 - $size['textHeight']) / 2, $hex);
$image->drawImage($draw);

replyAndLeave(sendImg($image->getImageBlob()));
