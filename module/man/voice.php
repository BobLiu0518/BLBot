<?php

global $Queue;

$msg=<<<EOT
让度娘/谷歌娘说话
每字节收费 1 金币
用法：
#voice [声音代号] {语言代号}
{
    文本
}

语言代号为 zh 时才可使用声音代号
可用声音代号：
0 死板女声
1 死板男声
3 矫情男声
4 矫情女声
不指定声音代号时默认0。

常用语言代号：
zh 汉语
ja 日语
en 英语
fr 法语
ru 俄语
es 西班牙语
ar 阿拉伯语
eo 世界语
更多请百度/Google一下

示例：

#voice 4 zh
小白兔♂白又白
两根辫子翘起来
（指大辫子车）

#voice ja
ハハハハハ
EOT;

$Queue[]= sendBack($msg);

?>