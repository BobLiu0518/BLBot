<?php

requireLvl(6);

// Extract database from /data/data/com.infothinker.gzmetro/databases/gzmetro_YYYYMMDD.sqlite
$db = new SQLite3('../storage/cache/toilet/guangzhou.sqlite');
$data = json_decode(getData('toilet/data.json'), true);
setCache('toilet/'.time().'.bak', json_encode($data));
$data['广州地铁'] = [];

$toiletData = $db->query(<<<EOT
SELECT station_id, location_cn, name_cn
FROM device
WHERE category_id = 6
ORDER BY station_id ASC;
EOT);
$toilets = [];
while($row = $toiletData->fetchArray(SQLITE3_ASSOC)){
    if(!$toilets[$row['station_id']]){
        $toilets[$row['station_id']] = [];
    }
    $toilets[$row['station_id']][] = '【'.$row['name_cn'].'】'.$row['location_cn'];
}

$stationData = $db->query(<<<EOT
SELECT station_id, name_cn
FROM station;
EOT);
while($row = $stationData->fetchArray(SQLITE3_ASSOC)){
    $toilet = $toilets[$row['station_id']];
    $data['广州地铁'][$row['name_cn']] = implode("\n", $toilet ?? ['无数据，该站可能无卫生间']);
}

setData('toilet/data.json', json_encode($data));
replyAndLeave('更新数据成功，共 '.count($data['广州地铁']).' 条数据');

?>
