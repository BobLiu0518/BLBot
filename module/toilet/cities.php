<?php

global $Message, $Command;
loadModule('toilet.tools');

$cacheTime = filemtime(getCachePath('toilet/cities.png'));
if(time() <= $cacheTime + 86400) {
    $result = getCache('toilet/cities.png');
} else {
    $citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);

    // Image init
    $image = new Imagick();
    $image->newImage(1, 1, '#FFFFFF');
    $image->setImageFormat('png');
    $draw = new ImagickDraw();
    $draw->setTextEncoding('UTF-8');
    $fonts = [
        'CN' => 'SourceHanSansSC-Bold.otf',
        'HK' => 'SourceHanSansHC-Bold.otf',
        'TW' => 'SourceHanSansTC-Bold.otf',
    ];
    $maxStationNameWidth = 0;

    // Draw init
    $imageWidth = max(1000, 100 + 80 + 100 + $maxStationNameWidth + 120);
    $currentX = $currentY = 0;

    // Draw header
    $draw->setFillColor('#00695C');
    $draw->rectangle(0, $currentY, $imageWidth, $currentY + 160);
    $currentY += 16;

    $icon = new Imagick();
    $icon->setBackgroundColor(new ImagickPixel('transparent'));
    $icon->readImageBlob(getImg('svg_icon/toilet.svg'));
    @$icon->setImageOpacity(0.15);
    $draw->composite(Imagick::COMPOSITE_PLUS, $currentX + 64, $currentY + 12, 156, 156, $icon);

    $draw->setFillColor('#FFFFFF');
    $draw->setFont(getFontPath($fonts['CN']));
    $draw->setFontSize(72);
    $titleWidth = $image->queryFontMetrics($draw, '洗手间数据')['textWidth'] + 88;
    $currentX = ($imageWidth - $titleWidth) / 2;
    @$icon->setImageOpacity(1);
    $draw->composite(Imagick::COMPOSITE_PLUS, $currentX, $currentY + 28, 72, 72, $icon);
    $currentX += 88;
    $draw->setGravity(Imagick::GRAVITY_NORTHWEST);
    $draw->annotation($currentX, $currentY + 8, '洗手间数据');
    $icon->clear();
    $icon->destroy();

    $currentY += 150;
    $draw->setFillColor('#00695C');
    $draw->rectangle(0, $currentY, $imageWidth, $currentY + 8);
    $currentY += 84;

    // Draw content
    $logo = new Imagick();
    foreach($citiesMeta as $city) {
        $currentX = 100;

        // Draw logo
        $logo->readImageBlob(getImg('metro_logo_svg/'.$city['logo']));
        $draw->composite(Imagick::COMPOSITE_OVER, $currentX, $currentY + 12, 50, 50, $logo);
        $currentX += 56;
        $logo->clear();

        // Draw station name
        $draw->setFont(getFontPath($fonts[$city['font']]));
        $draw->setFillColor($city['color']['main']);
        $draw->setFontSize(48);
        $draw->annotation($currentX, $currentY, $city['name']);
        $currentY += 56;

        // Draw support
        $currentX = 100;
        $draw->setFontSize(40);
        $supportPrompt = [
            'CN' => ['× 不支持', ' ✓  部分支持', ' ✓  完全支持'],
            'HK' => ['× 不支援', ' ✓  部分支援', ' ✓  完全支援'],
            'TW' => ['× 不支援', ' ✓  部分支援', ' ✓  完全支援'],
        ];
        if($city['support'] === true) {
            $draw->setFillColor('#4CAF50');
            $draw->annotation($currentX, $currentY, $supportPrompt[$city['font']][2]);
        } else if($city['support'] === false) {
            $draw->setFillColor('#F44336');
            $draw->annotation($currentX, $currentY, $supportPrompt[$city['font']][0]);
        } else {
            $draw->setFillColor('#F9A825');
            $draw->annotation($currentX, $currentY, $supportPrompt[$city['font']][1]);
        }
        $currentX += 240;

        // Draw update time
        $draw->setFontSize(32);
        $draw->setFillColor('#000000');
        if($city['support']) {
            $timePrompt = ['CN' => '更新于 ', 'HK' => '更新於 ', 'TW' => '更新於 '];
            $draw->annotation($currentX, $currentY + 10, $timePrompt[$city['font']].$city['time']);
        }
        $currentY += 48;
        if(!is_bool($city['support'])) {
            $currentX = 100;
            $draw->setFillColor('#F9A825');
            $lines = wordWrapAnnotation($image, $draw, $city['support'], 800);
            for($i = 0; $i < count($lines); $i++) {
                $draw->annotation($currentX, $currentY, $lines[$i]);
                $currentY += 36;
            }
        }

        // Draw source
        if($city['support']) {
            $sourcePrompt = ['CN' => '数据来源：', 'HK' => '數據來源：', 'TW' => '數據來源：'];
            $currentX = 100;
            $draw->setFillColor('#000000');
            $lines = wordWrapAnnotation($image, $draw, $sourcePrompt[$city['font']].$city['source'], 800);
            for($i = 0; $i < count($lines); $i++) {
                $draw->annotation($currentX, $currentY, $lines[$i]);
                $currentY += 36;
            }
        }

        // Draw remark
        if(array_key_exists('remark', $city)) {
            $currentX = 100;
            $lines = wordWrapAnnotation($image, $draw, $city['remark'], 800);
            for($i = 0; $i < count($lines); $i++) {
                $draw->annotation($currentX, $currentY, $lines[$i]);
                $currentY += 36;
            }
        }
        $currentY += 40;
    }
    $logo->destroy();
    $currentY += 40;

    // Draw footer
    $draw->setGravity(0);
    $currentX = 0;
    $draw->setFillColor('#00695C');
    $draw->rectangle(0, $currentY, $imageWidth, $currentY + 8);
    $currentY += 14;
    $draw->rectangle(0, $currentY, $imageWidth, $currentY + 160);

    $icon = new Imagick();
    $icon->setBackgroundColor(new ImagickPixel('transparent'));
    $icon->readImageBlob(getImg('svg_icon/arrow.svg'));
    @$icon->setImageOpacity(0.15);
    for($i = 0; $i < 3; $i++) {
        $draw->composite(Imagick::COMPOSITE_PLUS, $imageWidth - 88 - $i * 64, $currentY + 56, 60, 75, $icon);
    }
    $icon->clear();
    $icon->setBackgroundColor(new ImagickPixel('transparent'));
    $icon->readImageBlob(getImg('svg_icon/metro.svg'));
    @$icon->setImageOpacity(0.15);
    $draw->composite(Imagick::COMPOSITE_PLUS, $imageWidth - 380, $currentY + 24, 160, 160, $icon);
    $icon->clear();
    $icon->destroy();

    $date = date('Y年m月d日 H:i');
    $hint = <<<EOT
本图由 BLBot 生成于 {$date}
地铁标志由 -Lyt- 绘制，版权由各地轨交公司所有
使用“××洗手间”查询轨交站点洗手间位置信息
Bot 不为数据的准确性和实时性负责
EOT;
    $draw->setGravity(Imagick::GRAVITY_NORTHWEST);
    $draw->setFillColor('#FFFFFF');
    $draw->setFillOpacity(0.8);
    $draw->setFont(getFontPath($fonts['CN']));
    $draw->setFontSize(22);
    $draw->setTextInterlineSpacing(-4);
    $draw->annotation($currentX + 100, $currentY + 16, $hint);
    $currentY += 160;

    // Get image
    $image->extentImage($imageWidth, $currentY, 0, 0);
    $image->drawImage($draw);
    $result = $image->getImageBlob();

    setCache('toilet/cities.png', $result);
}

replyAndLeave(sendImg($result));
