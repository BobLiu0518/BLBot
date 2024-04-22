<?php

$data = json_decode(getData('toilet/data.json'), true);
replyAndLeave("当前支持查询的城市：\n".implode("\n", array_keys($data))."\n(更多城市接入中…)");

?>
