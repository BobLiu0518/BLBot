<?php

global $Event, $Queue, $User_id, $Message, $CQ;
requireLvl(0);
loadModule('credit.tools');
loadModule('exp.tools');
loadModule('attack.tools');
loadModule('jrrp.tools');

switch(getStatus($User_id)) {
    case 'imprisoned':
        $reply = '监狱里貌似没法签到呢…';
        break;

    case 'confined':
        $reply = '禁闭室里貌似没法签到呢…';
        break;

    case 'arknights':
    case 'genshin':
        $reply = '身处异世界的你貌似找不到要去哪里签到…';
        break;

    case 'universe':
        $reply = '你已经不在地球上了…';
        break;

    case 'saucer':
        $reply = '你被外星人抓走了，无法签到了…';
        break;

    case 'hospitalized':
    case 'free':
    default:
        $credit = getCredit($User_id);

        if($credit < 1000000) {
            $income = rand(10000, 100000);
        } else if($credit < 10000000) {
            $income = rand(ceil(10000 - ($credit - 1000000) * 0.001), ceil(100000 - ($credit - 1000000) * 0.001));
        } else {
            $income = rand(1000, 10000);
        }
        $income = floor(1 + $income * getRp($Event['user_id']) / 50);
        $originLvl = getLvl($Event['user_id']);

        clearstatcache();
        $lastCheckinTime = filemtime('../storage/data/checkin/'.$Event['user_id']);
        if(0 == (int)date('Ymd') - (int)date('Ymd', $lastCheckinTime)) {
            $replys = [
                '你今天签到过了！（震声',
                '签到过了www',
                '好像，签到，过了，呢？',
                '签到过了呢',
                '准备一直签到调戏我吗？',
                '其实你再怎么签到也无人在意 0 人在意 NBCS 哈',
                '签到签到，你早八签到了吗？',
                '嫌自己金币不够可以试试 #attack 别人',
                'Tips: 其实签到获得的金币一点用都没有',
                'Tips: 签到的金币多少与今日人品有关哦！',
                '签签你的',
                '签到很积极，可是，你作业写完了吗？',
                '签到签到，希望你考试周复习也能跟签到一样积极^_^',
                '一直签到还是嫌金币不够的话可以试试 #checkout',
                '给你讲个鬼故事，你今天签到过了。',
                '你已经签到过了，但是你有没有听见孩子们的悲鸣？',
                '你…你失忆了？签到过了啊……',
                '还签到！再签到小心我扣光你的金币（',
                '签到过了啦（半恼）',
                '你不曾注意阴谋得逞者（指一直签到的你）在狞笑！',
                '签到成…失败！说不定今天你已经签到过了呢？',
                '还签到？我签到你好不好？@'.(fromGroup() ? ($CQ->getGroupMemberInfo($Event['group_id'], $Event['user_id'])->nickname) : ($CQ->getStrangerInfo($Event['user_id'])->nickname)).' 签到！',
                '签到够了没…我都不知道说什么好……',
                '你是整天签到的屑[CQ:emoji,id=128052]？',
            ];
            $reply = $replys[array_rand($replys)];
        } else {
            $checkinData = json_decode(getData('checkin/stat'), true);
            if((int)date('Ymd') > (int)$checkinData['date']) {
                $checkinData['date'] = date('Ymd');
                $checkinData['checked'] = 0;
            }
            $checkinData['checked'] += 1;
            setData('checkin/stat', json_encode($checkinData));

            // 被外星人抓走的概率
            $currentHour = date('G'); // 获取当前的小时 (0 - 23)
            $abductionProbability = 0;
            if($currentHour >= 0 && $currentHour < 2) {
                $abductionProbability = 1; // 1%
            } else if($currentHour >= 3 && $currentHour < 5) {
                $abductionProbability = 5; // 5%
            } else if($currentHour >= 20 || $currentHour < 6) {
                $abductionProbability = 1; // 1%
            }
            // 判断是否被抓走
            if(rand(1, 100) <= $abductionProbability) {
                $data = getAttackData($Event['user_id']);
                $data['status'] = 'saucer';
                $data['end'] = date('Ymd', time() + 86400); // 1 day
                $reply = '🛸天空上突然出现了一台飞碟，你被外星人抓走了…';
                $CQ->setGroupReaction($Event['group_id'], $Event['message_id'], '326');
                setAttackData($Event['user_id'], $data);
            } else {
                addCredit($Event['user_id'], $income);
                addExp($Event['user_id'], 1);
                $reply = '签到成功，获得 '.$income.' 金币，1 经验～';
                if(getLvl($Event['user_id']) > $originLvl) {
                    $reply .= "\n恭喜升级 Lv".getLvl($Event['user_id']).' 啦～';
                } else {
                    $exp = getExp($Event['user_id']);
                    $lvlMap = getLvlMap();
                    foreach($lvlMap as $lvl) {
                        if($lvl['lvl'] == $originLvl + 1) {
                            $expGap = $lvl['exp'] - $exp;
                            if($expGap <= 1e7) {
                                $reply .= "\n再签到 {$expGap} 天即可升级 Lv{$lvl['lvl']}～";
                            }
                            break;
                        }
                    }
                }
                $reply .= "\n你是今天第 ".$checkinData['checked'].' 个签到的～';
            }
            delData('checkin/'.$Event['user_id']);
            setData('checkin/'.$Event['user_id'], '');
        }
        break;
}

if($Message) {
    $reply = str_replace('签到', $Message, $reply);
}
$Queue[] = replyMessage($reply);
