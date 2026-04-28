<?php

function searchPoi(string $place) {
    $data = getData('weather/poi/'.$place);
    if($data) return json_decode($data, true);

    $params = urlencode(json_encode(['keyWord' => $place, 'queryType' => 7, 'start' => 0, 'count' => 1, 'level' => 18, 'mapBound' => '-180,-90,180,90', 'show' => 2]));
    $tk = Config('TIANDITU_TK');
    $poiRes = json_decode(file_get_contents("https://api.tianditu.gov.cn/v2/search?postStr={$params}&tk={$tk}"), true);

    $poi = $poiRes['pois'][0] ?? null;
    if (!$poi) replyAndLeave("查询地点 {$place} 失败…");
    $fullName = ($poi['city'] ?? $poi['province']) . $poi['county'] . $poi['name'];

    $data = ['name' => $poi['name'], 'fullName' => $fullName, 'lonlat' => $poi['lonlat'], 'address' => $poi['address'], 'typeName' => $poi['typeName']];
    setData('weather/poi/'.$place, json_encode($data));
    return $data;
}
