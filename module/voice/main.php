<?php

require_once '../SDK/AipSpeech.php';

global $Event, $Queue, $Text, $Command;
global $CQ;
loadModule('credit.tools');
$countArg = count($Command)-1;

switch($countArg){
    case 1:
        $voice_type = 0;
        $lang = nextArg();
        break;
    case 2:
        $voice_type = nextArg();
        $lang = nextArg();
        if($lang != "zh")leave("只有 zh 语言可以使用额外的参数！");
        break;
    default:
        $Queue[]= sendBack("参数错误，请阅读以下内容！");
        loadModule('man.voice');
        leave();
}

$hash = $Event['message_id'];

$Text = str_replace("无迪", "脏话", $Text);
$Text = str_replace("物业费", "无迪吴逸飞", $Text);
$Text = removeCQCode(removeEmoji($Text));

$strength = strlen($Text);

$textWithoutSpace = preg_replace('# #','',$Text);

$fee = strlen($textWithoutSpace);

if($lang == NULL || !is_numeric($voice_type) || nextArg() !=NULL){
    $Queue[]= sendBack("参数错误，请阅读以下内容！请确保要朗读的内容在第二行！");
    loadModule('man.voice');
    leave();
}
if(0 == $fee)leave("没有要朗读的文字！");

if($lang == "zh"){

    $VOICE_APP_ID = config('voice_app_id');
    $VOICE_API_KEY = config('voice_api_key');
    $VOICE_SECRET_KEY = config('voice_secret_key');
    $client = new AipSpeech($VOICE_APP_ID, $VOICE_API_KEY, $VOICE_SECRET_KEY);

    $result = $client->synthesis($Text, $lang, 1, array('per' => $voice_type,'vol' => 15,));

    if(is_array($result)){
        leave("朗读失败，没有收取金币费用。请重试。");
    }
    decCredit($Event['user_id'], $fee);
    $Queue[]= sendBack(sendRec($result));
    if(fromGroup())$Queue[]= sendPM(sendRec($result));
}else{
    if($lang == 'zh-gl')$lang = 'zh';

    setCache($hash.'.txt', $Text);
    exec("export LC_ALL=C.UTF-8 && export LANG=C.UTF-8 && cd ../storage/cache/ && gtts-cli -f {$hash}.txt -o {$hash}.mp3 --nocheck -l {$lang}"); //So fucking hardcore py3
	decCredit($Event['user_id'], $fee);
    $Queue[]= sendBack(sendRec(getCache($hash.'.mp3')));
    if(fromGroup())$Queue[]= sendPM(sendRec(getCache($hash.'.mp3')));
}


$Queue[]= sendBack('共'.$strength.' 个字节，已收取 '.$fee.' 金币，您的余额为 '.getCredit($Event['user_id']));

?>
