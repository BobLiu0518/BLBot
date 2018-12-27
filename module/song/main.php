<?php

global $Queue, $CQ, $Text, $Event;

$wyyyy = true;

if(fromGroup())
{
    date_default_timezone_set('Asia/Shanghai');
    $banList = json_decode(getData("funcBan/song.json"));
    foreach($banList as $banGroup)
    {
        if($Event['group_id'] == $banGroup)
        {
            if(time() > $banList[$banGroup])
            {
                $banList[$banGroup] = '';
                setData(json_encode($banList),"funcBan/song.json");
            }
            else
            {
                leave("本群点歌功能已被关闭，恢复时间：".date('y/m/d H:i:s',$banList[$banGroup]));
            }
        }
    }
}

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

        default:
        $Text = trim($nextArg.' '.$Text);
}
}while($nextArg);

if($Text == NULL)leave('请填写歌曲信息！');

$Text = rawurlencode($Text);

if(!$wyyyy){
    $Queue[]= sendBack('QQ音乐 源测试中，播放成功概率 0%');
    $apiurl = 'https://api.mlwei.com/music/api/?key=523077333&id='.$Text.'&type=so&cache=0&size=mp3&nu=1';
    $song = json_decode(file_get_contents($apiurl),true)['Body'][0];

    $mid = $song['mid'];
    if(!$mid)leave('没有搜索到歌曲！');
    $url = 'https://y.qq.com/n/yqq/song/'.$mid.'.html';
    //$audio = $song['url'];
    $audio = file_get_contents($song['url']);
    $title = $song['title'];
    $content = $song['author'].' / '.$song['album'];
    $image = $song['pic'];
    $Queue[]= sendBack('[CQ:music,type=custom,url='.$url.',audio='.$audio.',title='.$title.',content='.$content.',image='.$image.']');
}else{
    $apiurl = 'https://api.mlwei.com/music/api/wy/?key=523077333&id='.$Text.'&type=so&cache=0&nu=1';
    $id = json_decode(file_get_contents($apiurl),true)['Body'][0]['id'];
    if(!$id)leave('没有搜索到歌曲！');
    $Queue[]= sendBack('[CQ:music,type=163,id='.$id.']');
}


?>