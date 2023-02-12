<?php

global $Event, $Queue;

requireLvl(2);

function handleSpecialChar($str){
    global $CQ, $Event;
    return str_replace("'", '', str_replace('<br>', '', str_replace('DrName', $CQ->getStrangerInfo($Event['user_id'])->nickname, $str)));
}

function getVoiceData($operator){
    $url = 'https://prts.wiki/index.php?action=raw&title='.urlencode($operator.'/语音记录');
    $prtsData = file_get_contents($url);
    if(!$prtsData){
        return false;
    }
    $prtsData = explode("\n", str_replace('{{DrName}}', 'DrName', $prtsData));

    $title = '';
    $operatorVoiceData = [];
    foreach($prtsData as $line){
        $lineData = explode('=', mb_substr($line, 1));
        if($lineData[0] == '路径'){
            $langPath = explode(',', $lineData[1]);
            foreach($langPath as $lang){
                $path = explode(':', $lang);
                $operatorVoiceData['path'][$path[0]] = $path[1];
            }
        }else if(mb_substr($lineData[0], 0, 2) == '标题'){
            $title = $lineData[1];
            $operatorVoiceData['voice'][$title] = [];
        }else if(mb_substr($lineData[0], 0, 2) == '台词'){
            $langLines = explode('}}', $lineData[1]);
            foreach($langLines as $lines){
                if($lines){
                    $linesData = explode('|', $lines);
                    $operatorVoiceData['voice'][$title][$linesData[1]] = $linesData[2];
                    if($operator == '泥岩'){
                        $operatorVoiceData['voice'][$title][$linesData[1].'(摘下头盔时)'] = $linesData[2];
                    }else if($operator == '罗小黑'){
                        $operatorVoiceData['voice'][$title][$linesData[1].'(猫形态)'] = $linesData[2];
                    }
                }
            }
        }else if(mb_substr($lineData[0], 0, 2) == '语音' && $lineData[0] != '语音key'){
            $operatorVoiceData['voice'][$title]['file'] = $lineData[1];
        }
    }

    return $operatorVoiceData;
}

$operator = nextArg();
$lang = null;

if(!$operator){
    $waifu = json_decode(getData('ark/waifu/'.$Event['user_id']), true);
    if(!$waifu){
        replyAndLeave('没有设定看板哦… 可以使用 #ark.waifu 设定看板，或者使用干员名和语言的参数调用 #ark.voice');
    }
    $operator = $waifu['operator'];
    $lang = $waifu['lang'];
}else{
    $lang = nextArg();
    if(!$lang){
        $lang = '日文';
    }
}
$lang = str_replace('语', '文', str_replace('汉语', '中文', $lang));
$langSuffix = '';
if(preg_match('/\(.*\)/', $lang, $matchResult)){
    $langSuffix = $matchResult[0];
}

$title = nextArg();

$data = json_decode(getData('ark/voice/'.$operator), true);
if(!$data){
    $data = getVoiceData($operator);
    if(!$data){
        replyAndLeave('没有找到干员 '.$operator.' 的语音数据哦…');
    }
    setData('ark/voice/'.$operator, json_encode($data));
}

$voice = null;
if(!$title){
    $title = array_rand($data['voice'], 1);
}
if(!$data['path'][$lang]){
    replyAndLeave('没有找到干员'.$operator.'的 '.$lang.' 语音数据哦…'."\n".'可选语言：'.implode(array_keys($data['path']), ' '));
}
if(!$data['voice'][$title]){
    replyAndLeave('没有找到干员'.$operator.'的 '.$title.' 语音哦…'."\n".'可选语音：'.implode(array_keys($data['voice']), ' '));
}

$Queue[]= replyMessage('［'.$operator.' '.$title."］\n中文".$langSuffix.' - '.handleSpecialChar($data['voice'][$title]['中文'.$langSuffix].($lang == '中文'.$langSuffix ? '' : "\n".$lang.' - '.(($data['voice'][$title][$lang]) ? $data['voice'][$title][$lang] : '(暂无'.$lang.'文本)'))));
$Queue[]= sendBack('[CQ:record,file='.'https://static.prts.wiki/'.$data['path'][$lang].'/'.$data['voice'][$title]['file'].']');

?>
