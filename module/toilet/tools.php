<?php

// Reference: https://gist.github.com/simkimsia/2971092/b2c128d643d8333f1ebe4ece1f49566ec2b69d5d
function wordWrapAnnotation(&$image, &$draw, $text, $maxWidth) {
    $words = explode(" ", $text);
    $lines = [];
    $i = 0;
    $lineHeight = 0;
    while($i < count($words)) {
        $currentLine = $words[$i];
        $metrics = $image->queryFontMetrics($draw, $currentLine);

        // Split word if width out of limit
        if($metrics['textWidth'] > $maxWidth && mb_strlen($currentLine) > 1) {
            for($j = mb_strlen($words[$i]) - 2; $j > 1; $j--) {
                $metrics = $image->queryFontMetrics($draw, mb_substr($currentLine, 0, $j));
                if($metrics['textWidth'] <= $maxWidth) {
                    if(preg_match('/[。？！，、；：”’』」）】〕］》〉]/u', mb_substr($words[$i], $j, 1))) {
                        $j++;
                    }
                    array_splice($words, $i, 1, [mb_substr($currentLine, 0, $j), mb_substr($currentLine, $j)]);
                    break;
                }
            }
            $currentLine = $words[$i];
        }

        if($i + 1 >= count($words)) {
            $lines[] = $currentLine;
            break;
        }

        // Check to see if we can add another word to this line
        $metrics = $image->queryFontMetrics($draw, $currentLine.' '.$words[$i + 1]);
        while($metrics['textWidth'] <= $maxWidth) {
            // If so, do it and keep doing it!
            $currentLine .= ' '.$words[++$i];
            if($i + 1 >= count($words)) {
                break;
            }
            $metrics = $image->queryFontMetrics($draw, $currentLine.' '.$words[$i + 1]);
        }

        // We can't add the next word to this line, so loop to the next line
        $lines[] = $currentLine;
        $i++;
    }
    return $lines;
}

function utf8_to_extended_ascii($str, &$map) {
    $matches = array();
    if(!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)) {
        return $str;
    }
    foreach($matches[0] as $mbc) {
        if(!isset($map[$mbc])) {
            $map[$mbc] = chr(128 + count($map));
        }
    }

    return strtr($str, $map);
}
function levenshtein_utf8($s1, $s2) {
    $charMap = array();
    $s1 = utf8_to_extended_ascii($s1, $charMap);
    $s2 = utf8_to_extended_ascii($s2, $charMap);
    return levenshtein($s1, $s2);
}