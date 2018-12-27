<?php

global $Queue;

$msg=<<<EOT
说明：这是一份傻×都能看懂的 #help，原来的版本请使用 #man 代替。
这个版本不包括高级命令和测试命令，需要查看请使用 #man -advanced
请注意：不要把括号输进去。尖括号的内容，比如 <a>，请替换为自己要的内容。
<@a> 在群聊中可以使用@，在所有地方都可以直接输入Q号。
标注 <参数> 的内容，请使用下方标识的“参数：”内的内容填充。
标注 <换行> 的内容，请一定要换行，要不然会报错。
请注意空格，不加的地方别加，要加的地方必须加。

#checkin
    签到
#credit.check <@a>
    查看金币数量，a 默认为自己
#credit.transfer <@a> <b>
    给 a b 个金币（手续费5%）
#help
    展示本帮助列表
#issue <换行> 标题 <换行> 内容
    报告bug
#man
    查看详细命令解释
#man.<a>
    查看 a 命令的详细解释
#osu.bind
    绑定 osu! 账号
    （参见网址osu.ppy.sh）
#osu.bp <参数>
    查看你的 osu! bp
    参数：
    -<a>
        要查看的 bp，1-100的整数
    -user <b>
        要查看的用户，默认自己
    -<c>
        要查看的模式，std，taiko，mania，ctb 中的一个
#osu.listen <a>
    听 osu! 谱面为 a 的歌（全损音质）
#osu.profile <@a>
    查看 a 的 osu! 资料，默认自己
#osu.recent <参数>
    查看你上一次 osu! 的成绩
    参数：
    -user <a>
        要查看的用户，默认自己
    -<b>
        要查看的模式，std，taiko，mania，ctb 中的一个
#osu.setMode <a>
    设置你 osu! 的默认格式，a 为模式，std，taiko，mania，ctb 中的一个
#pixiv.IID <a>
    搜索 pixiv 查看 ID 为 a 的图片
    a 可以有多个，空格分隔
#pixiv.search <参数> <关键字>
    在 pixiv 上面搜索
    参数：
    -mode <a>
        可选，safe 或者 r18，safe 模式屏蔽 18禁 图片
    -page <b>
        可选，第 b 页的内容
    - <c>
        可选，第 c 张图片
#recordStat
    阅读用户记录使用情况的用户协议
#recordStat.verify
    加入匿名用户使用情况记录并获得奖励金币
#recordStat.cancel
    退出匿名用户使用情况记录并交还金币
#recordStat.me
    查看自己是否加入了匿名用户使用情况记录
#roll <参数>
    生成随机数
    参数：
    <a>
        可选，设置最大值为 a
    <a> <b>
        设置最小值为 a，最大值为 b
    请注意，这两组参数只能选一组。
#search.baidu <a>
    返回百度搜索 a 的链接
#search.google <a>
    返回 Google 搜索 a 的链接
#song <a>
    点歌，a 为搜索关键字，如歌名/歌手
#sleep <a>
    使自己被禁言（如果有权限的话）
    <a> 可以使用英文的时间，比如：
        7am
        15minutes
        30days
#time
    让舰娘报时
    可以在群里设置自动报时，具体联系 2018962389
#trans <a> <b> <换行> <c>
    将 c 从 a 语言翻译到 b 语言
    a 不填的话是自动检测源语言
    a 和 b 可以填语言代号，比如：
        zh 中文
        en 英文
        eo 世界语
    查看更多语言代号请使用 #man.trans 查看
#unsleep <a>
    在 a 群将你解除禁言（如果有权限的话）
    每天只能用一次
#version
    查看 BL1040Bot 的版本
#voice <a> <换行> <b>
    让机器人发语音，a 是语言代号，比如：
        zh 中文
        en 英文
        eo 世界语
    查看更多语言代号请使用 #man.voice 查看
    b 是要说的内容。
EOT;

$msg2=<<<EOT
说明：这是一份傻×都能看懂的 #help，原来的版本请使用 #man 代替。
这个版本不包括高级命令和测试命令，需要查看请使用 #man -advanced

具体内容见私聊。
EOT;

if(fromGroup()){
    $Queue[]= sendBack($msg2);
    $Queue[]= sendPM($msg);
}else{
    $Queue[]= sendBack($msg);
}

?>