<?php

global $Event, $Queue, $User_id;
loadModule('credit.tools');

//leave("因签出bug签出功能暂时关闭");

$fuck = rand(9999,10000);
clearstatcache();
$lastCheckoutTime = filemtime('../storage/data/checkout/'.$Event['user_id']);
if(0 == (int)date('d')-(int)date('d', $lastCheckoutTime)){
    $reply = rand(1,14);
    switch ($reply){
        case 1:
        $replyWord = '你今天签出过了！（震声';break;
        case 2:
        $replyWord = '小可爱，你今天签出过了，不要再戳人家啦';break;
        case 3:
        $replyWord = '手也太勤快了吧亲，都已经签出过啦!';break;
        case 4:
        $replyWord = '签出过了呢';break;
        case 5:
        $replyWord = '今日已经签出，请明日再来!';break;
        case 6:
        $replyWord = '不要再戳签出啦，人家怕痛呢~';break;
        case 7:
        $replyWord = '给你讲个鬼故事，你今天签出过了。';break;
        case 8:
        $replyWord = '签出过了啦，到隔壁kjBot那里签出去。（好吧隔壁没有）';break;
        case 9:
        $replyWord = '你…你失忆了？签出过了啊……';break;
        case 10:
        $replyWord = '还签出！再签出小心我加满你的金币（不可能的）';break;
        case 11:
        $replyWord = '签出过了啦（半恼）';break;
        case 12:
        $replyWord = '还签出…嫌金币太多是不是？去py2018962389啊';break;
        case 13:
        $replyWord = '老说签出签出，qiān chū，会读了吗？不要再问我了';break;
        case 14:
        $replyWord = '还签出？我签出你好不好？[CQ:at,qq='.$User_id.'] 签出！';break;
        case 15:
        $replyWord = '签出够了没…我都不知道说什么好……';break;
    };
    $Queue[]= sendBack($replyWord);
}else{
    decCredit($Event['user_id'], $fuck);
    delData('checkout/'.$Event['user_id']);
    setData('checkout/'.$Event['user_id'], '');
    $Queue[]= sendBack("[CQ:at,qq=".$User_id."]
签出成功，失去 ".$fuck." 个金币!
Tips: 不知道怎么玩可以发送 #help");
}

?>