<?php

global $Event, $Queue, $User_id;
requireLvl(0);
loadModule('credit.tools');
loadModule('exp.tools');

$income = rand(10000, 100000);
if(10000==$income)
{
    $income = -1040;
}
if($Event['user_id'] == "2075552448")$income=34767;
clearstatcache();
$lastCheckinTime = filemtime('../storage/data/checkin/'.$Event['user_id']);
if(0 == (int)date('md')-(int)date('md', $lastCheckinTime)){
    $reply = rand(1,16);
    switch ($reply){
        case 1:
        $replyWord = '你今天签到过了！（震声';break;
        case 2:
        $replyWord = '签到过了www';break;
        case 3:
        $replyWord = '好像，签到，过了，呢？';break;
        case 4:
        $replyWord = '签到过了呢';break;
        case 5:
        $replyWord = '准备一直签到调戏我吗？';break;
        case 6:
        $replyWord = '一直签到还是嫌金币不够的话可以试试 #checkout';break;
        case 7:
        $replyWord = '给你讲个鬼故事，你今天签到过了。';break;
        case 8:
        $replyWord = '签到过了啦，到隔壁 YeziiBot 那里签到去。';break;
        case 9:
        $replyWord = '你…你失忆了？签到过了啊……';break;
        case 10:
        $replyWord = '还签到！再签到小心我扣光你的金币（';break;
        case 11:
        $replyWord = '签到过了啦（半恼）';break;
        case 12:
        $replyWord = '还签到…嫌金币不够是不是？去py2018962329啊';break;
        case 13:
        $replyWord = '老说签到签到，qiān dào，会读了吗？不要再问我了';break;
        case 14:
        $replyWord = '还签到？我签到你好不好？[CQ:at,qq='.$User_id.'] 签到！';break;
        case 15:
        $replyWord = '签到够了没…我都不知道说什么好……';break;
	case 16:
	$replyWord = '你事整天签到的屑[CQ:emoji,id=128052]？';break;
    };
    $Queue[]= sendBack($replyWord);
}else{
    addCredit($Event['user_id'], $income);
    addExp($Event['user_id'], 1);
    delData('checkin/'.$Event['user_id']);
    setData('checkin/'.$Event['user_id'], '');
    $Queue[]= sendBack("[CQ:at,qq=".$User_id."]
签到成功，获得 ".$income." 金币，1 经验!");
}

?>
