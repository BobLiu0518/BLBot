<?php

global $Event, $Queue;
requireLvl(1);

$searchApi = 'https://app.gushiwen.cn/api/search11.aspx?page=1&token=gswapi&value=';
$articleApi = 'https://app.gushiwen.cn/api/shiwen/shiwenv11.aspx?token=gswapi&id=';

if(!($name = nextArg()))leave('没有古诗文名！');
$searchResult = json_decode(file_get_contents($searchApi.urlencode($name)), true);
if(!$searchResult['sumCount'])leave('找不到结果！');
$article = $searchResult['gushiwens'][0];
$id = $article['idnew'];
$nameStr = $article['nameStr'];
$author = $article['author'];
$chaodai = rtrim($article['chaodai'], '代');
$content = str_replace('<p>', '', str_replace('</p>', '', $article['cont']));

$reply =<<<EOT
《{$nameStr}》 {$chaodai} · {$author}
{$content}
EOT;
$Queue[]= sendBack($reply);

?>
