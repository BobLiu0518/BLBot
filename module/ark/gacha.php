<?php

replyAndLeave('本功能已停止服务。');

function getImageCompressed($url, $cacheRoute) {
    $image = getCache($cacheRoute);
    if(!$image) {
        $image = file_get_contents($url);
        $Imagick = new Imagick();
        $Imagick->readImageBlob($image);
        $Imagick->setImageFormat('jpeg');
        $Imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $Imagick->setImageCompressionQuality(80);
        $image = $Imagick->getImageBlob();
        setCache($cacheRoute, $image);
    }
    return $image;
}

function getPool($poolName) {
    // 获取卡池数据
    $pools = json_decode(getData('ark/pool.json'), true);

    // 获取相应卡池
    if(!$poolName) {
        $latest = 0;
        foreach($pools as $pool) {
            if($pool['opEndTime'] > $latest) {
                $latest = $pool['opEndTime'];
                $poolName = $pool['name'];
            }
        }
    } else if(!$pools[$poolName]) {
        replyAndLeave('卡池不存在…');
    }

    $operators = json_decode(getData('ark/operator.json'), true);
    $pool = $pools[$poolName];
    foreach($operators as $operator) {
        if($operator['type'] == 'other') {
            // 非抽卡干员
            continue;
        } else if($operator['type'] == 'limited' && $pool['type'] != 'limited') {
            // 限定干员 普池
            continue;
        } else if($operator['time'] > $pool['opEndTime']) {
            // 时空战士
            continue;
        } else if($operator['time'] < $pool['opStartTime'] && intval($operator['star']) >= 5) {
            // 被移入中坚池的五六星老干员
            continue;
        } else if($pool['type'] == 'special' && $operator['star'] >= 5) {
            // 联合行动 高星
            continue;
        } else if($pool['operators'][$operator['star']]['except'] && in_array($operator['name'], $pool['operators'][$operator['star']]['except'])) {
            // 排除列表
            continue;
        } else if($pool['operators'][$operator['star']]['up'] && in_array($operator['name'], $pool['operators'][$operator['star']]['up'])) {
            // UP列表（无需再次添加）
            continue;
        } else {
            $pool['operators'][$operator['star']]['normal'][] = $operator['name'];
        }

        foreach($pool['operators'][$operator['star']]['other'] as $other) {
            if($other['name'] == $operator['name']) {
                for($n = 1; $n < $other['weight']; $n++) {
                    // 按权重多次添加干员
                    $pool['operators'][$operator['star']]['normal'][] = $operator['name'];
                }
            }
        }
    }

    return $pool;
}

function gacha($poolName, $times) {
    // 注：在本脚本注释中各个保底的含义：
    //	（代码方便起见的非官方叫法，在玩家群体中也不通用）
    // 小保底：多次寻访未出五/六星时，五/六星出率增加
    // 	（六星出率增加为公示保底，五星出率增加为统计发现的隐藏保底）
    // 中保底：多次寻访未获取UP干员时，下次对应星级必得对应UP
    // 	（统计发现的隐藏保底，限普池，早期卡池无）
    // 大保底：x次寻访必得xx干员/xx星级
    // 	（公示，如10次内必得五星及以上、120次内必得麒麟X夜刀）
    // 四星保底：十连至少得一名四星
    // 	（统计发现的隐藏保底，限十连；有日服新手池十连全三星的特例，暂不列入考虑）

    global $Event;
    if($times > 10) {
        return "最多抽十连哦…";
    }
    $pool = getPool($poolName);

    $userData = json_decode(getData('ark/user/'.$Event['user_id']), true);
    $operatorData = json_decode(getData('ark/operator.json'), true);
    // $image = getImageCompressed($pool['image'], 'ark/pool/'.$pool['name']);
    $reply = '【'.$pool['name']."】寻访 ".$times." 次结果：\n\n";
    // $reply = '【'.$pool['name']."】\n";
    // $reply .= sendImg($image);
    // $reply .= "\n\n寻访 ".$times." 次结果：\n";

    $result = new Imagick();
    $resultXPos = 78;
    $result->readImageBlob(getImg('ark/gacha/bg.png'));
    $professionData = [
        '先锋' => 'vanguard',
        '近卫' => 'guard',
        '重装' => 'defender',
        '狙击' => 'sniper',
        '术师' => 'caster',
        '医疗' => 'medic',
        '辅助' => 'supporter',
        '特种' => 'specialist'
    ];

    $floor4 = 0;

    for($gacha = 0; $gacha < $times; $gacha += 1) {
        $star = $operator = '';

        // 计数
        $userData[$pool['name']]['counter'] += 1;
        if($pool['type'] == 'normal' || $pool['type'] == 'normal2') {
            $userData['normal']['floor6'] += 1;
            $userData['normal']['floor5'] += 1;
        } else {
            $userData[$pool['name']]['floor6'] += 1;
            $userData[$pool['name']]['floor5'] += 1;
        }
        $floor4 += 1;
        if($pool['type'] == 'normal2' && $userData[$pool['name']]['direct'] != -1) {
            $userData[$pool['name']]['direct'] += 1;
        }

        // 大保底判定
        foreach($pool['bonus'] as $n => $bonus) {
            if($userData[$pool['name']]['counter'] == $bonus['counter'] && $userData[$pool['name']]['bonus'][$n] != true) {
                $userData[$pool['name']]['bonus'][$n] = true;
                if($bonus['type'] == 'star') {
                    $star = $bonus['star'];
                } else if($bonus['type'] == 'operator') {
                    $operator = $bonus['operator'];
                    $star = $operatorData[$operator]['star'];
                }
            }
        }

        // 星级判定
        // b23.tv/cv20251111
        if(!$star) {
            $r = rand(1, 10000);
            $floor6 = ($pool['type'] == 'normal' || $pool['type'] == 'normal2') ? $userData['normal']['floor6'] : $userData[$pool['name']]['floor6'];
            $floor5 = ($pool['type'] == 'normal' || $pool['type'] == 'normal2') ? $userData['normal']['floor5'] : $userData[$pool['name']]['floor5'];
            $w6 = $floor6 <= 50 ?
                200 :
                200 + 200 * ($floor6 - 50);
            $w5 = $floor5 <= 15 ?
                800 : (
                    $floor5 <= 20 ?
                    800 + 200 * ($floor5 - 15) :
                    1800 + 400 * ($floor5 - 20)
                );

            if($r <= $w6) {
                $star = '6';
            } else if($r <= $w6 + $w5) {
                $star = '5';
            } else if($r <= $w6 + $w5 + 5000) {
                $star = '4';
            } else {
                $star = '3';
            }
        }

        // 四星保底判定
        if($star != '3') {
            $floor4 = 0;
        }
        if($floor4 == 10) {
            // TODO: insert randomly
            $star = '4';
        }

        // 干员判定
        if(!$operator) {
            $r = rand(1, 100);
            if(!$userData[$pool['name']]['obtainedUps'][$star]) {
                $userData[$pool['name']]['obtainedUps'][$star] = [];
            }

            if($pool['type'] == 'normal2' && $star == '6' && $userData[$pool['name']]['direct'] > 150) {
                // 吃定向选调
                $operator = $pool['operators']['6']['up'][0];
                $userData[$pool['name']]['direct'] = -1;
            } else if($pool['type'] == 'normal' && $pool['opEndTime'] <= 20230330 && $pool['opEndTime'] >= 20220501 && (
                (
                    $star == '5' && count($userData[$pool['name']]['obtainedUps'][$star]) < count($pool['operators'][$star]['up'])
                    && $userData[$pool['name']]['counter'] > (count($userData[$pool['name']]['obtainedUps'][$star]) * 50 + 51)
                ) || (
                    $star == '6' && count($userData[$pool['name']]['obtainedUps'][$star]) < count($pool['operators'][$star]['up'])
                    && $userData[$pool['name']]['counter'] > (count($userData[$pool['name']]['obtainedUps'][$star]) * 200 + 201)
                )
            )) {
                // 吃中保底
                // b23.tv/av225820687
                $remainOperators = array_diff($pool['operators'][$star]['up'], $userData[$pool['name']]['obtainedUps'][$star]);
                $operator = $remainOperators[array_rand($remainOperators, 1)];
                $userData[$pool['name']]['obtainedUps'][$star][] = $operator;
            } else if($pool['operators'][$star]['up'] && ($r <= $pool['operators'][$star]['percentage'] || ($pool['type'] == 'special' && intval($star) >= 5))) {
                // 没吃中保底，而且没歪
                $operator = $pool['operators'][$star]['up'][array_rand($pool['operators'][$star]['up'], 1)];

                // 中保底记录
                if(!in_array($operator, $userData[$pool['name']]['obtainedUps'][$star])) {
                    $userData[$pool['name']]['obtainedUps'][$star][] = $operator;
                }
            } else {
                // 歪了 / 没UP的
                $operator = $pool['operators'][$star]['normal'][array_rand($pool['operators'][$star]['normal'], 1)];
            }
        }

        // 发消息
        if($times != 10) {
            for($n = 6; $n > 0; $n--) {
                if(intval($star) >= $n) {
                    $reply .= '★';
                } else {
                    $reply .= '　';
                }
            }
            $reply .= ' '.$operator."\n";
        }

        // 生成十连图
        if($times == 10) {
            $operatorBg = new Imagick();
            $operatorBg->readImageBlob(getImg('ark/gacha/'.$star.'.png'));
            $result->compositeImage($operatorBg, Imagick::COMPOSITE_OVER, $resultXPos, 0);

            $operatorPortrait = new Imagick();
            $portraitImage = getCache('ark/portrait/'.$operator);
            if(!$portraitImage) {
                $portraitImage = file_get_contents($operatorData[$operator]['portrait']);
                setCache('ark/portrait/'.$operator, $portraitImage);
            }
            $operatorPortrait->readImageBlob($portraitImage);
            $radio = 252 / $operatorPortrait->getImageGeometry()['height'];
            $height = intval($operatorPortrait->getImageGeometry()['height'] * $radio);
            $operatorPortrait->cropThumbnailImage(82, $height);
            $result->compositeImage($operatorPortrait, Imagick::COMPOSITE_OVER, $resultXPos, 112);

            $operatorProfession = new Imagick();
            $operatorProfession->readImageBlob(getImg('ark/profession/'.$professionData[$operatorData[$operator]['profession']].'.png'));
            $operatorProfession->thumbnailImage(59, 59);
            $result->compositeImage($operatorProfession, Imagick::COMPOSITE_OVER, $resultXPos + 12, 322);
            $resultXPos += 82;
        }

        // 小保底检测
        if($star == '6') {
            if($pool['type'] == 'normal' || $pool['type'] == 'normal2') {
                $userData['normal']['floor6'] = 0;
                $userData['normal']['floor5'] = 0;
            } else {
                $userData[$pool['name']]['floor6'] = 0;
                $userData[$pool['name']]['floor5'] = 0;
            }
        } else if($star == '5') {
            if($pool['type'] == 'normal' || $pool['type'] == 'normal2') {
                $userData['normal']['floor5'] = 0;
            } else {
                $userData[$pool['name']]['floor5'] = 0;
            }
        }

        // 定向选调检测
        if($pool['type'] == 'normal2' && $operator == $pool['operators']['6']['up'][0]) {
            $userData[$pool['name']]['direct'] = 0;
        }

        // 大保底检测
        foreach($pool['bonus'] as $n => $bonus) {
            if($bonus['type'] == 'star' && intval($star) >= intval($bonus['star']) && $userData[$pool['name']]['bonus'][$n] != true) {
                $userData[$pool['name']]['bonus'][$n] = true;
            } else if($bonus['type'] == 'operator' && $operator == $bonus['operator'] && $userData[$pool['name']]['bonus'][$n] != true) {
                $userData[$pool['name']]['bonus'][$n] = true;
            }
        }
    }

    // 十连发图
    if($times == 10) {
        $watermarkLayer = new Imagick();
        $watermarkLayer->newImage(960, 450, 'none');
        $watermarkLayer->setImageFormat('png');
        $watermark = new ImagickDraw();
        $watermark->setTextAlignment(Imagick::ALIGN_LEFT);
        $watermark->setFont(getFontPath('consolab.ttf'));
        $watermark->setFontSize(12);
        $watermark->setFillColor(new ImagickPixel('#FFFFFF'));
        $watermark->annotation(84, 438, $Event['user_id'].' '.date('Y/m/d H:i:s'));
        $watermark->setTextAlignment(Imagick::ALIGN_RIGHT);
        $watermark->annotation(894, 438, 'Generated by BLBot');
        $watermarkLayer->drawImage($watermark);
        $result->compositeImage($watermarkLayer, imagick::COMPOSITE_OVER, 0, 0);

        $result->setImageFormat('jpeg');
        $result->setImageCompression(Imagick::COMPRESSION_JPEG);
        $result->setImageCompressionQuality(80);
        $reply .= sendImg($result->getImageBlob())."\n";
    }

    // 大保底提示
    foreach($pool['bonus'] as $n => $bonus) {
        if($userData[$pool['name']]['counter'] < $bonus['counter'] && $userData[$pool['name']]['bonus'][$n] != true) {
            $reply .= ($bonus['counter'] - $userData[$pool['name']]['counter']).' 次内寻访内必得';
            $reply .= (($bonus['type'] == 'star') ? (' '.$bonus['star'].'★ 及以上干员') : ('干员 '.$bonus['operator']))."\n";
        }
    }

    // 定向选调提示
    if($pool['type'] == 'normal2' && $userData[$pool['name']]['direct'] != -1) {
        if($userData[$pool['name']]['direct'] <= 150) {
            $reply .= '定向选调累计次数 '.$userData[$pool['name']]['direct']."\n";
        } else {
            $reply .= '下次 6★ 干员必定为 '.$pool['operators']['6']['up'][0].' !!'."\n";
        }
    }

    // 次数提示
    $reply .= '“'.$pool['name'].'”中已经寻访了 '.$userData[$pool['name']]['counter'].' 次'."\n";

    // 小保底提示
    $reply .= (($pool['type'] == 'normal' || $pool['type'] == 'normal2') ? ('标准寻访') : ('“'.$pool['name'].'”')).'已连续 '.(($pool['type'] == 'normal' || $pool['type'] == 'normal2') ? $userData['normal']['floor6'] : $userData[$pool['name']]['floor6']).' 次没有招募到 6★ 干员';
    setData('ark/user/'.$Event['user_id'], json_encode($userData));

    return $reply;
}

requireLvl(1, '模拟抽卡');

$poolName = nextArg();
$times = nextArg();

if(is_numeric($poolName) && !$times) {
    $times = $poolName;
    $poolName = '';
} else if(!$times) {
    $times = 1;
}

if(!rand(0, 100)) {
    replyAndLeave('啊呜，你的寻访凭证被小刻吃掉了！');
} else {
    replyAndLeave(gacha($poolName, $times));
}
