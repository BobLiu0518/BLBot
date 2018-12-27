<?php

leave(<<<EOT
使用谷歌翻译
每 10 字节收费 1 金币
未满 10 字节按 10 字节计算
整十字节多收 1 金币
用法：
#voice [源语言] {目标语言}
{
    文本
}

常用语言代号：
zh   汉语
ja   日语
en   英语
fr   法语
ru   俄语
es   西班牙语
ar   阿拉伯语
eo   世界语
auto 自动检测

不填写源语言时默认自动检测。

示例：
#trans zh en
我爱你

#trans zh
I love you
EOT
);

?>
