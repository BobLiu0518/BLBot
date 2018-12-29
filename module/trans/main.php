<?php
global $Queue, $Text, $User_id, $Command;
use \Statickidz\GoogleTranslate;
loadModule('credit.tools');
$countArg = count($Command)-1;

switch($countArg){
    case 1:
        $source = "auto";
        $target = nextArg();
        break;
    case 2:
        $source = nextArg();
        $target = nextArg();
        break;
    default:
        $Queue[]= sendBack("参数错误！请阅读以下内容！");
        loadModule('man.trans');
        leave();
}
if($source == NULL || $target == NULL){
    $Queue[]= sendBack("参数错误！请阅读以下内容！");
    loadModule('man.trans');
    leave();
}
$textLength = strlen($Text);
if(0 == $textLength)leave("没有要翻译的内容！");
$fee = intval($textLength*0.1+1);
decCredit($User_id, $fee);
$trans = new GoogleTranslate();
$Queue[]= sendBack($trans->translate($source, $target, $Text));
$Queue[]= sendBack('共 '.$textLength.' 个字节，已收取 '.$fee.' 金币，您的余额为 '.getCredit($User_id));

?>