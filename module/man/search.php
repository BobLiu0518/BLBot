<?php

$msg=<<<EOT
搜索
用法：
#search.{pixiv|baidu|google}

返回指定的搜索引擎链接
其中 #search.pixiv 可以使用参数，
参数列表见 #help.pixiv.search
搜索 Google 需要手动 番羽 土啬

更多搜索（如bilibili）
即将到来™

示例：
#search.pixiv hentai（等价于 #pixiv.search hentai）
#search.baidu 百度是垃圾
#search.google 百度是垃圾
EOT;

leave($msg);

?>
