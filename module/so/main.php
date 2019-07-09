<?php

global $Queue, $CQ, $Text, $Event, $User_id;
loadModule('credit.tools');
$wyyyy = true;$cd = true;

//if(fromGroup() && !isAdmin())leave('由于过多点歌会导致腾讯吞消息，现暂停非Bot管理的点歌功能，具体恢复时间请联系签名大号。感谢支持！');
//leave('由于过多点歌会导致腾讯吞消息，现暂停点歌功能，具体恢复时间请联系签名大号。感谢支持！');

do{

    $nextArg = trim(nextArg());

    switch($nextArg){//写了一坨屎，但是没什么卵用

        case '-qq':
        case '-QQ':
        case '-QQMusic':
        case '-qqmusic':
        $wyyyy = false; break;

        case '-163':
        case '-NetEasy':
        case '-neteasy':
        case '-wangyi':
        case '-WangYi':
        case '-WangYiCloud':
        case '-wangyicloud':
        case '-NetEasyCloud':
        case '-neteasycloud':
        case '-WangYiCloudMusic':
        case '-wangyicloudmusic':
        case '-NetEasyCloudMusic':
        case '-neteasycloudmusic':
        $wyyyy = true; break;

        case '-noCoolDown':
        $cd = false;

        default:
        $Text = trim($nextArg.' '.$Text);
}
}while($nextArg);

if($Text == NULL)leave('请填写歌曲信息！');

if($cd){
    if(coolDown("song/user/{$Event['user_id']}")<0)leave('本命令每人每150秒只能使用一次！');
    if(fromGroup())
    {
        if(coolDown("song/group/{$Event['group_id']}")<0)leave('本命令每群每30秒只能使用一次！');
        coolDown("song/group/{$Event['group_id']}",30);
    }
    coolDown("song/user/{$Event['user_id']}",150);
}

decCredit($User_id,500);

$Text = rawurlencode($Text);

if(!$wyyyy){
    $Queue[]= sendBack('QQ音乐 源测试中，播放成功概率 0%');/*
    $apiurl = 'https://api.mlwei.com/music/api/?key=523077333&id='.$Text.'&type=so&cache=0&size=mp3&nu=1';
    $song = json_decode(file_get_contents($apiurl),true)['Body'][0];

    $mid = $song['mid'];
    if(!$mid){*/addCredit($User_id,500);/*leave('没有搜索到歌曲，没有扣除金币！');}
    $url = 'https://y.qq.com/n/yqq/song/'.$mid.'.html';
    //$audio = $song['url'];
    $audio = file_get_contents($song['url']);
    $title = $song['title'];
    $content = $song['author'].' / '.$song['album'];
    $image = $song['pic'];
    $Queue[]= sendBack('[CQ:music,type=custom,url='.$url.',audio='.$audio.',title='.$title.',content='.$content.',image='.$image.']');
    $Queue[]= sendBack('点歌成功，扣除 50 金币，你的余额为 '.getCredit($User_id));*/
}else{
    $apiurl = 'https://api.mlwei.com/music/api/wy/?key=523077333&id='.$Text.'&type=so&cache=0&nu=1';
    $id = json_decode(file_get_contents($apiurl),true)['Body'][0]['id'];
    if(!$id){leave('没有搜索到歌曲，没有扣除金币！');addCredit($User_id,500);}
    $Queue[]= sendBack('[CQ:music,type=163,id='.$id.']');
    if(fromGroup())$Queue[]= sendPM('[CQ:music,type=163,id='.$id.']');
    $Queue[]= sendBack('点歌成功，扣除 500 金币，你的余额为 '.getCredit($User_id));
}


?>
