<?php

$data = json_decode(getData('toilet/data.json'), true);
replyAndLeave("当前支持查询的城市：\n".implode('、', array_keys($data)).'（按接入顺序排序，更多城市接入中…）');

?>
