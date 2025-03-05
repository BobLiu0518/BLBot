<?php

global $Event, $Queue;

requireLvl(2);
loadModule('nickname.tools');

function handleSpecialChar($str) {
    global $CQ, $Event;
    return preg_replace("/'|<br>/", '', str_replace('DrName', getNickname($Event['user_id']), $str));
}

function getVoiceData($operator) {
    $url = 'https://prts.wiki/index.php?action=raw&title='.urlencode($operator.'/语音记录');
    $prtsData = file_get_contents($url);
    if(!$prtsData) {
        return false;
    }
    $prtsData = explode("\n", preg_replace('/{{DrName(\|语言=.+?)?}}/', 'DrName', $prtsData));

    $title = '';
    $operatorVoiceData = [];
    foreach($prtsData as $line) {
        $lineData = explode('=', mb_substr($line, 1));
        if($lineData[0] == '路径') {
            $langPath = explode(',', $lineData[1]);
            foreach($langPath as $lang) {
                $path = explode(':', $lang);
                $operatorVoiceData['path'][$path[0]] = $path[1];
            }
        } else if(mb_substr($lineData[0], 0, 2) == '标题') {
            $title = $lineData[1];
            $operatorVoiceData['voice'][$title] = [];
        } else if(mb_substr($lineData[0], 0, 2) == '台词') {
            $langLines = explode('}}', $lineData[1]);
            foreach($langLines as $lines) {
                if($lines) {
                    $linesData = explode('|', $lines);
                    $operatorVoiceData['voice'][$title][$linesData[1]] = $linesData[2];
                }
            }
        } else if(mb_substr($lineData[0], 0, 2) == '语音' && $lineData[0] != '语音key') {
            $operatorVoiceData['voice'][$title]['file'] = $lineData[1];
        }
    }

    return $operatorVoiceData;
}

$operator = nextArg();
$lang = null;

if(!$operator) {
    $waifu = json_decode(getData("ark/waifu/{$Event['user_id']}"), true);
    if(!$waifu) {
        replyAndLeave('没有设定看板哦… 可以使用 #ark.waifu 设定看板，或者使用干员名和语言的参数调用 #ark.voice');
    }
    $operator = $waifu['operator'];
    $lang = $waifu['lang'];
} else {
    $lang = nextArg();
    if(!$lang) {
        $lang = '日语';
    }
}
$textLang = preg_replace('/\(.+?\)/', '', str_replace('语', '文', $lang));

$title = nextArg();

$data = json_decode(getData("ark/voice/{$operator}"), true);
if(!$data) {
    $data = getVoiceData($operator);
    if(!$data) {
        replyAndLeave("没有找到干员 {$operator} 的语音数据哦…");
    }
    setData("ark/voice/{$operator}", json_encode($data));
}

$voice = null;
if(!$title) {
    $title = array_rand($data['voice'], 1);
}
if(!$data['path'][$lang]) {
    if($data['path']['联动']) {
        $lang = '联动';
    } else {
        replyAndLeave("干员 {$operator} 的可选语言：".implode(' ', array_keys($data['path'])));
    }
}
if(!$data['voice'][$title]) {
    replyAndLeave("干员 {$operator} 的可选语音记录：".implode(' ', array_keys($data['voice'])));
}

$reply = "「{$operator} {$title} {$lang}」\n中文 - ".handleSpecialChar($data['voice'][$title]["中文"]);
if($textLang != '中文-普通话' && $textLang != '联动') {
    $reply .= "\n{$textLang} - ".handleSpecialChar($data['voice'][$title][$textLang] ?: "(暂无{$textLang}文本)");
}
$Queue[] = replyMessage($reply);
$Queue[] = sendBack("[CQ:record,file=https://torappu.prts.wiki/assets/audio/{$data['path'][$lang]}/".strtolower($data['voice'][$title]['file']).']');
