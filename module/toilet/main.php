<?php

global $Message, $Command;
loadModule('toilet.tools');

$fromMiddleware = $Command[0] == 'middleWare/toilet';
if($fromMiddleware) {
    $stationName = trim($Message);
    if(!$stationName) leave();
} else {
    $stationName = implode(' ', array_splice($Command, 1));
    if(!$stationName) replyAndLeave('要查询什么车站呢？');
}

$toiletInfo = json_decode(getData('toilet/toiletInfo.json'), true);
$citiesMeta = json_decode(getData('toilet/citiesMeta.json'), true);

$results = [];
$citiesNotSupported = [];
$similarStations = [];

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

// Check toilets
foreach($toiletInfo as $city => $stations) {
    $checkedStations = [];
    $checkingStations = array_unique([$stationName, $stationName.'站', preg_replace('/站$/', '', $stationName)]);
    while(count($checkingStations)) {
        $checkingStation = array_shift($checkingStations);
        $checkedStations[] = $checkingStation;

        if(array_key_exists($checkingStation, $stations)) {
            // Handle not supported
            if($citiesMeta[$city]['support'] === false) {
                $citiesNotSupported[] = $city;
                break;
            }

            // Handle toilets
            if(array_key_exists('toilets', $stations[$checkingStation])) {
                if(!array_key_exists($city, $results)) {
                    $results[$city] = [];
                }
                $results[$city][$checkingStation] = $stations[$checkingStation];

                // Get station name width
                $stationNameWidth = 40;
                $draw->setFont(getFontPath($fonts[$citiesMeta[$city]['font']]));
                $draw->setFontSize(60);
                $stationNameWidth += $image->queryFontMetrics($draw, $citiesMeta[$city]['name'].'站')['textWidth'];
                $draw->setFontSize(80);
                $stationNameWidth += $image->queryFontMetrics($draw, $checkingStation)['textWidth'];
                $maxStationNameWidth = max($maxStationNameWidth, $stationNameWidth);
            }

            // Handle redirect
            if(array_key_exists('redirect', $stations[$checkingStation])) {
                foreach($stations[$checkingStation]['redirect'] as $target) {
                    if(!in_array($target, $checkedStations) && !in_array($target, $checkingStations)) {
                        $checkingStations[] = $target;
                    }
                }
            }
        }
    }
}

// Get similar stations
if(!count($results) && !count($citiesNotSupported) && mb_strlen($stationName) >= 2) {
    foreach($toiletInfo as $city => $stations) {
        foreach(array_keys($stations) as $station) {
            if(mb_strpos($station, $stationName) !== false) {
                $strDistance = 0;
            } else {
                $strDistance = levenshtein_utf8($station, $stationName);
            }

            if($strDistance <= min(4, mb_strlen($station) / 2)) {
                $similarStations[$strDistance][$station][] = $city;
            }
        }
    }
    ksort($similarStations);
}

if($fromMiddleware && !count($results) && !count($citiesNotSupported) && !count($similarStations)) {
    leave();
}

// Draw init
$imageWidth = max(1000, 100 + 80 + 100 + $maxStationNameWidth + 120);
$currentX = $currentY = 0;

// Draw header
$draw->setFillColor('#1A3DA3');
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
$titleWidth = $image->queryFontMetrics($draw, '洗手间查询')['textWidth'] + 88;
$currentX = ($imageWidth - $titleWidth) / 2;
@$icon->setImageOpacity(1);
$draw->composite(Imagick::COMPOSITE_PLUS, $currentX, $currentY + 28, 72, 72, $icon);
$currentX += 88;
$draw->setGravity(Imagick::GRAVITY_NORTHWEST);
$draw->annotation($currentX, $currentY + 8, '洗手间查询');
$icon->clear();
$icon->destroy();

$currentY += 150;
$draw->setFillColor('#1A3DA3');
$draw->rectangle(0, $currentY, $imageWidth, $currentY + 8);
$currentY += 84;

// Draw body
foreach($results as $city => $stations) {
    $colors = $citiesMeta[$city]['color'];
    foreach($stations as $stationName => $station) {
        $currentX = 100;
        $draw->setGravity(0);

        // Draw logo
        $logo = new Imagick();
        $logo->readImageBlob(getImg('metro_logo_svg/'.$citiesMeta[$city]['logo']));
        $draw->composite(Imagick::COMPOSITE_OVER, $currentX, $currentY, 80, 80, $logo);
        $currentX += 100;
        $currentY += 64;
        $logo->clear();
        $logo->destroy();

        // Draw station name
        $draw->setFont(getFontPath($fonts[$citiesMeta[$city]['font']]));
        $draw->setFillColor($citiesMeta[$city]['color'][array_key_exists('secondary', $citiesMeta[$city]['color']) ? 'secondary' : 'main']);
        $draw->setFontSize(60);
        $draw->annotation($currentX, $currentY, $citiesMeta[$city]['name']);
        $currentX += $image->queryFontMetrics($draw, $citiesMeta[$city]['name'])['textWidth'] + 40;
        $draw->setFillColor($citiesMeta[$city]['color']['main']);
        $draw->setFontSize(80);
        $draw->annotation($currentX, $currentY, $stationName);
        $currentX += $image->queryFontMetrics($draw, $stationName)['textWidth'];
        $draw->setFontSize(60);
        $draw->annotation($currentX, $currentY, '站');
        $currentX = 100;
        $currentY += 32;

        // Draw line
        $draw->rectangle($currentX, $currentY, $imageWidth - $currentX, $currentY + 8);
        $currentY += 36;

        // Draw data
        $draw->setGravity(Imagick::GRAVITY_NORTHWEST);
        if(count($station['toilets']) == 0) {
            $draw->setFontSize(40);
            $draw->setFillColor('#000000');
            $draw->annotation($currentX, $currentY, [
                'CN' => '* 无数据 该站可能无洗手间',
                'HK' => '* 無數據 該站可能無洗手間',
                'TW' => '* 無數據 該站可能無洗手間',
            ][$citiesMeta[$city]['font']]);
            $currentY += 80;
        } else {
            foreach($station['toilets'] as $toilet) {
                $currentX = 100;

                // Draw title
                $draw->setFontSize(32);
                $titleWidth = $image->queryFontMetrics($draw, $toilet['title'])['textWidth'];
                $titleColor = $colors[array_key_exists($toilet['title'], $colors) ? $toilet['title'] : 'main'];
                $draw->setFillColor($titleColor);
                $titleColor = new ColorCompare\Color($titleColor);
                $draw->roundRectangle($currentX, $currentY, $currentX + $titleWidth + 40, $currentY + 52, 26, 26);
                $draw->setFillColor($titleColor->getLab()['L'] > 72 ? '#000000' : '#FFFFFF');
                $draw->annotation($currentX + 20, $currentY, $toilet['title']);

                // Draw content
                $currentX += $titleWidth + 56;
                $currentY -= 6;
                $draw->setFillColor('#000000');
                $draw->setFontSize(40);
                $lines = wordWrapAnnotation($image, $draw, $toilet['content'], $imageWidth - $titleWidth - 256);
                for($i = 0; $i < count($lines); $i++) {
                    $draw->annotation($currentX, $currentY, $lines[$i]);
                    $currentY += 48;
                }
                $currentY += 32;
            }
        }
        $currentY += 64;
    }
}

// Draw cities not supported
if(count($citiesNotSupported)) {
    $currentX = 100;
    $cityNames = [];
    $logo = new Imagick();
    foreach($citiesNotSupported as $city) {
        $cityNames[] = $citiesMeta[$city]['name'];

        // Draw logo
        $logo->readImageBlob(getImg('metro_logo_svg/'.$citiesMeta[$city]['logo']));
        $draw->composite(Imagick::COMPOSITE_OVER, $currentX, $currentY, 50, 50, $logo);
        $logo->clear();
        $currentX += 64;
    }
    $logo->destroy();

    $currentX = 100;
    $currentY += 50;
    $draw->setFont(getFontPath($fonts['CN']));
    $draw->setFillColor('#000000');
    $draw->setFontSize(32);
    $draw->setGravity(Imagick::GRAVITY_NORTHWEST);

    $lines = wordWrapAnnotation($image, $draw, implode('、', $cityNames).'不支持查询', $imageWidth - 256);
    for($i = 0; $i < count($lines); $i++) {
        $draw->annotation($currentX, $currentY, $lines[$i]);
        $currentY += 40;
    }
    $currentY += 80;
}

// Draw similar stations
if(!count($results) && !count($citiesNotSupported)) {
    $currentX = 100;
    $draw->setFont(getFontPath($fonts['CN']));
    $draw->setFillColor('#000000');
    $draw->setFontSize(48);
    $draw->setGravity(Imagick::GRAVITY_NORTHWEST);

    $lines = wordWrapAnnotation($image, $draw, '未找到名为 '.$stationName.' 的车站…', $imageWidth - 256);
    for($i = 0; $i < count($lines); $i++) {
        $draw->annotation($currentX, $currentY, $lines[$i]);
        $currentY += 56;
    }
    $currentY += 20;
    if(count($similarStations)) {
        $draw->setFontSize(40);
        $draw->annotation($currentX, $currentY, '你可能想找： ');
        $currentY += 56;
        foreach($similarStations as $distance => $stations) {
            foreach($stations as $station => $cities) {
                $stationNameWidth = $image->queryFontMetrics($draw, $station)['textWidth'];
                if($currentX + $stationNameWidth + count($cities) * 40 > $imageWidth - 100) {
                    $currentY += 56;
                    $currentX = 100;
                }
                $logo = new Imagick();
                foreach($cities as $city) {
                    // Draw logo
                    $logo->readImageBlob(getImg('metro_logo_svg/'.$citiesMeta[$city]['logo']));
                    $draw->composite(Imagick::COMPOSITE_OVER, $currentX, $currentY + 16, 32, 32, $logo);
                    $logo->clear();
                    $currentX += 40;
                }
                $logo->destroy();
                $draw->annotation($currentX, $currentY, $station);
                $currentX += $stationNameWidth + 40;
            }
        }
        $currentY += 56;
    }
    $currentY += 80;
}

// Draw footer
$draw->setGravity(0);
$currentX = 0;
$draw->setFillColor('#1A3DA3');
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
使用 /toilet.cities 指令查看支持情况及数据来源
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

// Send message
$image->extentImage($imageWidth, $currentY, 0, 0);
$image->drawImage($draw);
replyAndLeave(sendImg($image->getImageBlob()));
