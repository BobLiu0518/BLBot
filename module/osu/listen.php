<?php

global $Queue, $Event, $CQ;
use PhpZip\ZipFile;
loadModule('credit.tools');
!set_time_limit(360);

$beatmapSetID = (int)nextArg();

if(fromGroup()){
    $CQ->sendGroupMsg($Event['group_id'], "正在获取歌曲 ".$beatmapSetID."！");
}else{
    $CQ->sendPrivateMsg($Event['user_id'], "正在获取歌曲 ".$beatmapSetID."！");
}

$webHeader = [
    "http" => [
        "header" => 'Cookie: '.config('osu_cookie')
    ]
];

$temp = getData("osu/listen/{$beatmapSetID}.mp3");
if($temp !== false){
    decCredit($Event['user_id'], 100);
    $Queue[]= sendBack(sendRec($temp));
    $Queue[]= sendBack('点歌成功，收费100金币，你现在的余额为 '.getCredit($Event['user_id']).'！');
    leave();
}
decCredit($Event['user_id'], 500); 

if(fromGroup()){
    $CQ->sendGroupMsg($Event['group_id'], "可能需要一定时间，请勿重复发送指令！");
}else{
    $CQ->sendPrivateMsg($Event['user_id'], "可能需要一定时间，请勿重复发送指令！");
}

$osz = new ZipFile();

$osz = file_get_contents('https://osu.ppy.sh/beatmapsets/'.$beatmapSetID.'/download', false, stream_context_create($webHeader));
if(!$osz){addCredit($Event['user_id'], 500);leave('没有这个谱面ID！');}
try{
    $osz->openFromString($web);
}catch(\Exception $e){addCredit($Event['user_id'], 500);leave('无法打开谱面，请联系'.config('master').'！');}
//$CQ->sendGroupMsg($Event['group_id'],"114514");
$oszFiles = $osz->matcher();

$mp3FileName = $oszFiles->match('~\S*\.mp3~')->getMatches()[0];

$mp3 = $osz->getEntryContents($mp3FileName);
setData("osu/listen/{$beatmapSetID}.mp3", $mp3);

$Queue[]= sendBack(sendRec($mp3));
$Queue[]= sendBack('点歌成功，收费500金币，你现在的余额为 '.getCredit($Event['user_id']));

?>
