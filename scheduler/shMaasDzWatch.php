<?php

global $Schedulers;
$Schedulers[] = new BLBot\Scheduler(
    'shMaasDzWatch',
    true,
    function ($timestamp) {
        return intval(date('s', $timestamp)) < 5 && intval(date('i', $timestamp)) % 5 == 0;
    },
    function ($timestamp) {
        global $Queue;
        $notices = [];
        $data = [];
        $cache = getData('shmaas.json');
        if ($cache) $cache = json_decode($cache, true);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\n", [
                    'Content-Type: application/json',
                    'X-Saic-Platform: h5',
                    'X-Saic-AppId: maas_car',
                ]),
                'content' => json_encode([
                    'pageSize' => 10000,
                    'pageNumber' => 1,
                    'filterConditionPb' => [
                        'lon' => '',
                        'lat' => '',
                        'sourceType' => 1,
                    ],
                ]),
            ],
        ]);
        $lines = json_decode(file_get_contents('https://api.shmaas.net/traffic/cstmbus/line/list', false, $context), true);
        if ($lines['errMsg']) throw $lines['errMsg'];
        foreach ($lines['data']['lines'] as $line) {
            $lineInfo = "{$line['lineName']} {$line['lineNo']}";
            $data[$lineInfo] = $line;
            if (!$cache) continue;
            if (!$cache[$lineInfo]) {
                $lineInfo .= "\n{$line['sceneName']} {$line['labelName']} {$line['merchantName']}";
                $lineInfo .= "\n{$line['startSiteName']}-{$line['endSiteName']} ￥".number_format($line['ticketPrice'] / 100, 2, '.', '');
                $notices[] = "新线：\n{$lineInfo}";
            } else {
                foreach ($line as $key => $value) {
                    if (in_array($key, ['fundLinePb'])) continue;
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    $originValue = json_encode($cache[$lineInfo][$key], JSON_UNESCAPED_UNICODE);
                    if ($value !== $originValue) {
                        $notices[] = "{$lineInfo} {$key} 属性更改：\n——— 更改前 ———\n{$originValue}\n——— 更改后 ———\n{$value}";
                    }
                }
            }
        }
        setData('shmaas.json', json_encode($data));
        if (!$cache) $notices[] = '已初始化 '.count($data).' 条线路信息';

        foreach ($notices as $notice) {
            $notice = "[{$lines['nowTime']}]\n{$notice}";
            setData('shmaas.log', "\n\n".$notice, true);
            $Queue[] = sendMaster($notice);
        }
    }
);
