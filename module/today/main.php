<?php
global $Event, $Queue, $User_id, $Message, $CQ;
requireLvl(0);

$date = date('Y-m-d');
$seed = crc32($User_id . $date);
mt_srand($seed);

$activities = [
    "适合" => [
        "抽卡",
        "学习",
        "翘课",
        "打盹",
        "休息",
        "出门",
        "聚会",
        "打游戏",
        "看电影"
    ],
    "不适合" => [
        "打王者排位",
        "熬夜",
        "翘课",
        "宅在家",
        "拖延",
        "不交作业",
        "吃饭",
        "请假",
        "上课睡觉"
    ]
];

$suitable = array_rand($activities["适合"], 3);
$unsuitable = array_rand($activities["不适合"], 3);

$suitableActivities = array_map(function($index) use ($activities) {
    return $activities["适合"][$index];
}, $suitable);

$unsuitableActivities = array_map(function($index) use ($activities) {
    return $activities["不适合"][$index];
}, $unsuitable);

$randomMessages = [
    "今天是个美好的一天！",
    "好运常伴你左右。",
    "今天你会有意想不到的收获。",
    "保持积极的心态，一切都会变好。",
    "今天适合尝试新事物。"
];

$randomMessage = $randomMessages[array_rand($randomMessages)];

$output = "今 日 运 势\n";
$output .= $randomMessage . "\n";
$output .= "--------------------\n";
$output .= "宜  " . implode(" ", $suitableActivities) . "\n";
$output .= "--------------------\n";
$output .= "忌  " . implode(" ", $unsuitableActivities) . "\n";

$Queue[] = replyMessage($output);
?>