<?php

global $CQ, $Event;
requireLvl(1);
loadModule('schedule.tools');
loadModule('nickname.tools');
loadModule('poem.tools');
loadModule('motto.tools');

if(fromGroup()) {
    $CQ->setGroupReaction($Event['group_id'], $Event['message_id'], '351');
    $targets = $CQ->getGroupMemberList($Event['group_id']);
} else {
    $targets = json_decode("[{\"user_id\":{$Event['user_id']}}]");
}
$weekday = date('N');
$time = date('H:i');

$results = [];
$status = ['进行中', '翘课中', '下一节', '已结束', '无课程'];
foreach($targets as $target) {
    $todayCourses = getCourses($target->user_id, time());
    if($todayCourses === false) {
        continue;
    }

    if(!count($todayCourses)) {
        $results[] = [
            'user_id' => $target->user_id,
            'type' => 4,
            'mainDesc' => '今日无课程',
            'subDesc' => getMotto($target->user_id) ?? getVerse(),
        ];
        continue;
    }

    // 匹配当前/下节课程
    $timezone = getTimezoneGMTOffset(getTimezone($target->user_id));
    $timezoneHint = $timezone == 'GMT+8' ? '' : "({$timezone})";
    foreach($todayCourses as $course) {
        if($time < $course['startTime']) {
            $remain = ceil((strtotime($course['startTime']) - time()) / 60);
            $remain = $remain > 60 ? (floor($remain / 60).' 小时') : ($remain.' 分钟');
            $results[] = [
                'user_id' => $target->user_id,
                'type' => 2,
                'mainDesc' => blockBannedWords($course['name']),
                'subDesc' => "{$course['startTime']}-{$course['endTime']}{$timezoneHint} ({$remain}后)",
            ];
            continue 2;
        } else if($time >= $course['startTime'] && $time < $course['endTime']) {
            $remain = ceil((strtotime($course['endTime']) - time()) / 60).' 分钟';
            $results[] = [
                'user_id' => $target->user_id,
                'type' => isAbandoned($target->user_id) ? 1 : 0,
                'mainDesc' => blockBannedWords($course['name']),
                'subDesc' => "{$course['startTime']}-{$course['endTime']}{$timezoneHint} (剩余 {$remain})",
            ];
            continue 2;
        }
    }
    $total = 0;
    foreach($todayCourses as $course) {
        $total += strtotime($course['endTime']) - strtotime($course['startTime']);
    }
    $total = number_format($total / 60 / 60, 1);
    $results[] = [
        'user_id' => $target->user_id,
        'type' => 3,
        'mainDesc' => '今日课程已上完',
        'subDesc' => "共计 {$total} 小时",
    ];
}

if(!count($results)) {
    replyAndLeave((fromGroup() ? '暂无群友配置了课程表哦…' : '暂未配置课程表哦…')."\n使用 #schedule.set 指令即可设置～");
}
if(!fromGroup()) {
    foreach($results as $user_id => $content) {
        replyAndLeave("[{$status[$content['type']]}]\n{$content['mainDesc']}\n{$content['subDesc']}");
    }
}
usort($results, function ($a, $b) {
    return ($a['type'] <=> $b['type']) * 2 + ($a['subDesc'] <=> $b['subDesc']);
});

// 准备图片
$image = new Imagick();
$image->newImage(1, 1, '#FFFFFF');
$image->setImageFormat('png');
$draw = new ImagickDraw();
$draw->setTextEncoding('UTF-8');
$draw->setFont(getFontPath('unifont.otf'));
$maxContentX = 0;
$currentX = $currentY = 20;
$colors = ['#B13333', '#DB6F2D', '#3949AB', '#379151', '#7F7F7F', '#00897B'];

// 画左上角框框
$draw->setFillColor($colors[5]);
$draw->rectangle($currentX, $currentY, $currentX + 80, $currentY + 40);
$draw->rectangle($currentX, $currentY, $currentX + 40, $currentY + 80);
$currentY += 200;

// 准备素材
$circle = new Imagick();
$circle->newImage(100, 100, 'none');
$circle->setimageformat('png');
$circle->setimagematte(true);
$circleDraw = new ImagickDraw();
$circleDraw->setfillcolor('#FFFFFF');
$circleDraw->circle(50, 50, 50, 100);
$circle->drawimage($circleDraw);
$icon = new Imagick();
$icon->setBackgroundColor(new ImagickPixel('transparent'));
$arrow = getImg('svg_icon/arrow.svg');

foreach($results as $result) {
    $avatar = getAvatar($result['user_id']);
    $nickname = getNickname($result['user_id']);

    // 画箭头
    $currentX = 200;
    $icon->readImageBlob(str_replace('#FFFFFF', $colors[$result['type']].'66', $arrow));
    $draw->composite(Imagick::COMPOSITE_MULTIPLY, $currentX, $currentY + 12, 60, 75, $icon);
    $currentX += 54;
    $icon->readImageBlob(str_replace('#FFFFFF', $colors[$result['type']].'59', $arrow));
    $draw->composite(Imagick::COMPOSITE_MULTIPLY, $currentX, $currentY + 12, 60, 75, $icon);

    // 画头像
    $currentX = 100;
    $icon->readImageBlob($avatar);
    $icon->compositeimage($circle, Imagick::COMPOSITE_DSTIN, 0, 0);
    $draw->composite(Imagick::COMPOSITE_DEFAULT, $currentX, $currentY, 100, 100, $icon);

    // 画昵称
    $currentX += 164;
    $draw->setFillColor('#000000');
    $draw->setFontSize(44);
    $draw->setGravity(Imagick::GRAVITY_NORTHWEST);
    $draw->annotation($currentX, $currentY - 8, '‣ '.$nickname);
    $contentX = $currentX + $image->queryFontMetrics($draw, '‣ '.$nickname)['textWidth'];
    if($contentX > $maxContentX) $maxContentX = $contentX;

    // 画状态
    $currentY += 48;
    $draw->setFillColor($colors[$result['type']]);
    $draw->rectangle($currentX + 4, $currentY, $currentX + 128, $currentY + 48);
    $draw->setFillColor('#FFFFFF');
    $draw->setFontSize(36);
    $draw->annotation($currentX + 12, $currentY + 6, $status[$result['type']]);
    $currentX += 140;

    // 画描述
    $currentY -= 4;
    $draw->setFillColor($colors[$result['type']]);
    $draw->setFontSize(32);
    $draw->annotation($currentX, $currentY, $result['mainDesc']);
    $contentX = $currentX + $image->queryFontMetrics($draw, $result['mainDesc'])['textWidth'];
    if($contentX > $maxContentX) $maxContentX = $contentX;
    $draw->setFontSize(24);
    $draw->annotation($currentX, $currentY + 32, $result['subDesc']);
    $contentX = $currentX + $image->queryFontMetrics($draw, $result['subDesc'])['textWidth'];
    if($contentX > $maxContentX) $maxContentX = $contentX;
    $currentY += 100;
}

$imageWidth = max(800, $maxContentX + 100);

// 画底栏
$signature = 'BLBot - '.date('Y/m/d H:i:s').' - '.$Event['group_id'];
$draw->setFillColor('#000000');
$draw->setFontSize(24);
$currentX = ($imageWidth - $image->queryFontMetrics($draw, $signature)['textWidth']) / 2;
$draw->annotation($currentX, $currentY + 8, $signature);
$prompt = '使用 #schedule.set 指令设置课程表';
$currentX = ($imageWidth - $image->queryFontMetrics($draw, $prompt)['textWidth']) / 2;
$draw->annotation($currentX, $currentY + 32, $prompt);

// 画右下角框框
$draw->setFillColor($colors[5]);
$draw->rectangle($imageWidth - 60, $currentY, $imageWidth - 20, $currentY + 80);
$draw->rectangle($imageWidth - 100, $currentY + 40, $imageWidth - 20, $currentY + 80);

// 画标题
$title = '“群友在上什么课?”';
$draw->setFontSize(72);
$titleWidth = $image->queryFontMetrics($draw, $title)['textWidth'];
$draw->setFillColor($colors[5].'59');
$draw->rectangle(($imageWidth - $titleWidth) / 2 - 20, 136, ($imageWidth + $titleWidth) / 2 + 20, 160);
$draw->setFillColor('#000000');
$draw->annotation(($imageWidth - $titleWidth) / 2, 80, $title);

// 生成图片
$image->extentImage($imageWidth, $currentY + 100, 0, 0);
$path = realpath(getCachePath('schedule'))."/{$Event['group_id']}.png";
$image->drawImage($draw);
$image->writeImage($path);

$CQ->setGroupReaction($Event['group_id'], $Event['message_id'], '351', false);
replyAndLeave("[CQ:image,file=file://{$path}]");