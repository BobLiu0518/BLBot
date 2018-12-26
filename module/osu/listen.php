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

//通过 Sayobot 提供的转存服务进行加速 （好不人道啊）
//但是为了避免解释起来费劲就叫加速通道好了
//下面这行代码需要电脑里有 curl 并且加到 path 里 (WIN)
//TODO: 把绝对路径换成相对路径
$command = 'curl --output E:\\BL1040Bot\\storage\\cache\\'.$beatmapSetID.'.zip https://cdn1.sayobot.cn:25225/maps/osz/'.intval($beatmapSetID/10000).'/'.($beatmapSetID%10000).'.osz';
exec($command);
//$CQ->sendGroupMsg($Event['group_id'], 'E:\\BL1040Bot\\storage\\cache\\'.$beatmapSetID.'.zip');
try{
    $osz->openFromString(getCache($beatmapSetID.'.zip'));
}catch(\Exception $e){
    sleep(15);
    exec($command);
    try{
        $osz->openFromString(file_get_contents('E:\\BL1040Bot\\storage\\cache\\'.$beatmapSetID.'.zip'));
    }catch(\Exception $e){
        if(fromGroup()){
            $CQ->sendGroupMsg($Event['group_id'], "加速通道进入失败，正在尝试普通通道！");
        }else{
            $CQ->sendPrivateMsg($Event['user_id'], "加速通道进入失败，正在尝试普通通道！");
        }
        /*
        $web = file_get_contents('https://osu.ppy.sh/beatmapsets/'.$beatmapSetID.'/download', false, stream_context_create($webHeader));
        if(!$web){addCredit($Event['user_id'], 500);leave('没有这个谱面ID！');}
        try{
            $osz->openFromString($web);
        }catch(\Exception $e){addCredit($Event['user_id'], 500);leave('无法打开谱面，请联系'.config('master').'！');}
        */
        leave('普通通道关闭！');
    }
}
//代码乱得要死……下面相对好一点，至少不会有三重try-catch（汗）

$oszFiles = $osz->matcher();

$mp3FileName = $oszFiles->match('~\S*\.mp3~')->getMatches()[0];

$mp3 = $osz->getEntryContents($mp3FileName);
setData("osu/listen/{$beatmapSetID}.mp3", $mp3);

$Queue[]= sendBack(sendRec($mp3));
$Queue[]= sendBack('点歌成功，收费500金币，你现在的余额为 '.getCredit($Event['user_id']));

?>