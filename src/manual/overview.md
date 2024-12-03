<script setup lang="ts">
import MasterBadge from '../../components/MasterBadge.vue';
</script>

# 指令一览

这里列出了 BLBot 的所有指令（按字母顺序排序）。

如果想要查看 Bot 有哪些功能，建议改为查看目录栏中各功能的详细说明。

::: tip
标记 <MasterBadge /> 的指令供 Bot 主人使用，不对普通用户开放。
:::

::: warning
此处列出的功能不保证可用性。
:::

## `#a2b` {#a2b}

同 [`#bv.encode`](#bv-encode) 。

## `#alias` {#alias}

详见 [别名系统](./alias)。

## `#alias.check` {#alias-check}

详见 [别名系统](./alias)。

## `#alias.clear` {#alias-clear}

详见 [别名系统](./alias)。

## `#alias.delete` {#alias-delete}

详见 [别名系统](./alias)。

## `#alias.del` {#alias-del}

同 [`#alias.delete`](#alias-delete)。

## `#alias.set` {#alias-set}

详见 [别名系统](./alias)。

## `#ark` {#ark}

详见 [明日方舟相关](./arknights)。

## `#ark.branch` {#ark-branch}

详见 [明日方舟相关](./arknights)。

## `#ark.clear` {#ark-clear}

详见 [明日方舟相关](./arknights)。

## `#ark.gacha` {#ark-gacha}

详见 [明日方舟相关](./arknights)。

## `#ark.operator` {#ark-operator}

详见 [明日方舟相关](./arknights)。

## `#ark.random` {#ark-random}

详见 [明日方舟相关](./arknights)。

## `#ark.time` {#ark-time}

详见 [明日方舟相关](./arknights)。

## `#ark.update` <MasterBadge /> {#ark.update}

更新明日方舟数据。

## `#ark.voice` {#ark-voice}

详见 [明日方舟相关](./arknights)。

## `#ark.waifu` {#ark-waifu}

详见 [明日方舟相关](./arknights)。

## `#atk` {#atk}

同 [`#attack`](#attack)。

## `#atk.rand` {#atk-rand}

同 [`#attack.rand`](#attack-rand)。

## `#attack` {#attack}

见 [打劫群友](./attack)。

## `#attack.config` {#attack-config}

见 [打劫群友](./attack)。

## `#attack.escape` {#attack-escape}

见 [打劫群友](./attack)。

## `#attack.inmate` {#attack-inmate}

见 [打劫群友](./attack)。

## `#attack.rand` {#attack-rand}

见 [打劫群友](./attack)。

## `#attack.suicide` {#attack-suicide}

见 [打劫群友](./attack)。

## `#b2a` {#b2a}

同 [`#bv.decode`](#bv-decode) 。

## `#base64` {#base64}

同 [`#base64.encode`](#base64-encode)。

## `#base64.decode <Base64>` {#base64-decode}

解码 Base64 字符串。

## `#base64.encode <字符串>` {#base64-encode}

编码 Base64 字符串。

## `#bili` {#bili}

见 [哔哩哔哩相关](./bili)。

## `#bili.av` {#bili-av}

同 [`#bv.decode`](#bv-decode) 。

## `#bili.bind` {#bili-bind}

见 [哔哩哔哩相关](./bili)。

## `#bili.bv` {#bili-bv}

同 [`#bv.encode`](#bv-encode) 。

## `#bili.sessdata` <MasterBadge /> {#bili-sessdata}

设置哔哩哔哩 `sessdata` Cookie。

## `#bili.subscribe` {#bili-subscribe}

见 [哔哩哔哩相关](./bili)。

## `#bili.unbind` {#bili-unbind}

见 [哔哩哔哩相关](./bili)。

## `#bili.unsubscribe` {#bili-unsubscribe}

见 [哔哩哔哩相关](./bili)。

## `#bili.user` {#bili-user}

见 [哔哩哔哩相关](./bili)。

## `#bili.video` {#bili-video}

见 [哔哩哔哩相关](./bili)。

## `#bv` {#bv}

同 [`#bv.decode`](#bv-decode) 。

## `#bv.decode <BV号>` {#bv-decode}

将 BV 号解析为 av 号。

## `#bv.encode <av号>` {#bv-encode}

将 av 号解析为 BV 号。

## `#calc` {#calc}

见 [计算器](./calc)。

## `#callme` {#callme}

同 [`#nickname`](#nickname)。

## `#checkin` {#checkin}

见 [金币系统](./credit)。

## `#checkout` {#checkout}

随机损失一定金币。

## `#choose` {#choose}

见 [随机选择](./choose)。

## `#color [色号]` {#color}

生成一张指定色号的纯色图片。如果不指定色号，会随机生成一种颜色。

支持的常见色号包括：

-   十六进制 RGB（如 `#1E88E5` 或 `1E88E5`）;
-   RGB（如 `rgb(11,45,14)`）;
-   HSL（如 `hsl(11,45,14)`）;
-   部分颜色的英文单词（如 `red`）。

## `#config` {#config}

见 [功能管理](./config)。

## `#config.add` {#config-add}

见 [功能管理](./config)。

## `#config.mode` {#config-mode}

见 [功能管理](./config)。

## `#config.remove` {#config-remove}

见 [功能管理](./config)。

## `#config.silence` {#config-silence}

见 [功能管理](./config)。

## `#cr` {#cr}

见 [国铁车次](./cr)。

## `#credit` {#credit}

见 [金币系统](./credit)。

## `#credit.check` {#credit-check}

同 [`#me`](#me)。

## `#credit.give` {#credit-give}

同 [`#credit.transfer`](#credit-transfer)。

## `#credit.rank` {#credit-rank}

见 [金币系统](./credit)。

## `#credit.set <@QQ> <金额>` <MasterBadge /> {#credit-set}

设置 @QQ 的金币余额为指定余额。

## `#credit.transfer` {#credit-transfer}

见 [金币系统](./credit)。

## `#ddl` {#ddl}

同 [#ddl.check](#ddl-check)。

## `#ddl <名称> <时间>` {#ddl-2}

同 [#ddl.add](#ddl-add)。

## `#ddl.add <名称> <时间>` {#ddl-add}

添加待办事项。

## `#ddl.check` {#ddl-check}

查看设置的待办事项。

## `#ddl.finish <名称1> [名称2...]` {#ddl-finish}

完成待办事项。

## `#ddl.notify [时间]` <LvBadge lv=4 /> {#ddl-notify}

设置每日指定时间提醒待办事项。如果不指定时间，默认为 08:00 进行提醒。

## `#escape` {#escape}

同 [`#attack.escape`](#attack-escape)。

## `#exp` {#exp}

同 [`#me`](#me)。

## `#exp.set <@QQ> <经验>` <MasterBadge /> {#exp-set}

设置 @QQ 的经验为指定值。

## `#feedback <内容>` {#feedback}

向 Bot 主人反馈信息。

## `#feedback.invite` {#feedback-invite}

申请邀请 Bot 加入群聊。

## `#gsw <古诗文名>` {#gsw}

查询古诗文。

## `#help` {#help}

获取帮助页面。

## `#inmate` {#inmate}

同 [`attack.inmate`](#attack-inmate)。

## `#jjt <线路名>` {#jjt}

查询嘉定公交公交线路信息。

## `#jrrp` {#jrrp}

见 [今日人品](./jrrp)。

## `#jsr` {#jsr}

见 [金山铁路车次](./jsr)。

## `#jst <线路名>` {#jst}

查询久事公交公交线路信息。

## `#leave` <MasterBadge /> {#leave}

让 Bot 退群。

## `#me` {#me}

见 [金币系统](./credit)。

## `#mkt <线路名>` {#mkt}

查询闵行客运公交线路信息。

## `#morse` {#morse}

同 [`#morse.encode`](#morse-encode)。

## `#morse.decode <摩斯电码>` {#morse-decode}

将摩斯电码解编码为文本。

## `#morse.encode <文本>` {#morse-encode}

将文本编码为摩斯电码。

## `#motto` {#motto}

见 [个性签名](./motto)。

## `#motto.check` {#motto-check}

见 [个性签名](./motto)。

## `#motto.del` {#motto-del}

见 [个性签名](./motto)。

## `#motto.set` {#motto-set}

见 [个性签名](./motto)。

## `#mrrp` {#mrrp}

见 [今日人品](./jrrp)。

## `#music <歌曲名>` {#music}

点歌。如果没有设置过默认音乐平台，使用网易云音乐发送歌曲。

## `#music.163 <歌曲名>` {#music-163}

使用网易云音乐点歌。

## `#music.default [平台]` {#music-default}

设置默认的音乐平台。如果没有填写平台，则清除默认音乐平台记录。

## `#music.qq <歌曲名>` {#music-qq}

使用 QQ 音乐点歌。

## `#nametag <头衔>` {#nametag}

设置你在群中的专属头衔。此指令需要 Bot 是群主。

## `#nickname` {#nickname}

见 [个性签名](./nickname)。

## `#nickname.check` {#nickname-check}

见 [昵称](./nickname)。

## `#nickname.del` {#nickname-del}

见 [昵称](./nickname)。

## `#nickname.set` {#nickname-set}

见 [昵称](./nickname)。

## `#pd` {#pd}

触发一次“权限不足”报错。

## `#pd.lv` {#pd-lv}

触发一次“等级不足”报错。

## `#permission <@QQ>` {#permission}

同 [`#permission.check`](#permission-check)。

## `#permission <@QQ> <权限>` <MasterBadge /> {#permission-1}

同 [`#permission.set`](#permission-set)。

## `#permission.check <@QQ>` {#permission-check}

查看 @QQ 的权限。

## `#permission.set <@QQ> <权限>` <MasterBadge /> {#permission-set}

设置 @QQ 的权限。

## `#pinyin` {#pinyin}

见 [拼音查询](./pinyin)。

## `#poke` {#poke}

让 Bot 戳你一下。

## `#qrcode <文本>` {#qrcode}

通过文本生成二维码。

## `#randomban` {#randomban}

让 Bot 随机禁言你一段时间（最少 1 秒，最多 10 分钟）。此指令需要 Bot 在群内的权限较高，即 Bot 为群主，或者 Bot 为管理员且调用者为普通用户。

## `#rank` {#rank}

同 [`#credit.rank`](#credit-rank)。

## `#redpack` {#redpack}

见 [金币系统](./credit)。

## `#restart` <MasterBadge /> {#restart}

重启 OneBot 实现。

## `#rh` {#rh}

见 [赛马](./rh)。

## `#rh.ban` {#rh-ban}

见 [赛马](./rh)。

## `#rh.clean` <MasterBadge /> {#rh-clean}

在赛马没有正常退出时，强制清理赛马场。

## `#rh.nickname` {#rh-nickname}

见 [赛马](./rh)。

## `#roll` {#roll}

见 [随机数](./roll)。

## `#roll.dice` {#roll-dice}

让 Bot 发个骰子表情。

## `#roll.rps` {#roll-rps}

让 Bot 发个石头剪刀布表情。

## `#sanlian` {#sanlian}

见 [金币系统](./credit)。

## `#say [参数...]` <MasterBadge /> {#say}

指挥 Bot 发送消息。

可选参数：

-   `-escape`：自动转码
-   `-async`：异步发送
-   `-toGroup <群号>`：发送到群
-   `-toPerson <QQ号>`：发送到人

不指定发送目标时，发送到当前聊天窗口。

## `#say.delete <消息ID>` <MasterBadge /> {#say-delete}

撤回消息。

## `#schedule` {#schedule}

见 [课程表](./schedule)。

## `#schedule.abandon` {#schedule-abandon}

见 [课程表](./schedule)。

## `#schedule.check` {#schedule-check}

见 [课程表](./schedule)。

## `#schedule.del` {#schedule-del}

见 [课程表](./schedule)。

## `#schedule.everyone` {#schedule-everyone}

见 [课程表](./schedule)。

## `#schedule.note` {#schedule-note}

见 [课程表](./schedule)。

## `#schedule.notify` {#schedule-notify}

见 [课程表](./schedule)。

## `#schedule.rank` {#schedule-rank}

见 [课程表](./schedule)。

## `#schedule.set` {#schedule-set}

查看支持导入的课程表应用。

## `#schedule.wakeup` {#schedule-wakeup}

见 [课程表](./schedule)。

## `#schedule.xiaoai` {#schedule-xiaoai}

见 [课程表](./schedule)。

## `#scheduler.run <计划任务名>` <MasterBadge /> {#scheduler-run}

无视启动条件，强行启动计划任务。

## `#scheduler.status` <MasterBadge /> {#scheduler-status}

查看计划任务状态。

## `#shjt <线路名>` {#shjt}

查询上海交通公交线路信息。

## `#shname <姓名>` {#shname}

在上海市户籍人口中查询同名人数。

## `#sjwgj <线路名>` {#sjwgj}

查询松江公交公交线路信息。

## `#song` {#song}

同 [`#music`](#music)。

## `#status` <MasterBadge /> {#status}

看看 Bot 的状态。

## `#sudo` {#sudo}

一个没什么实际作用的小彩蛋。

## `#suicide` {#suicide}

同 [`#attack.suicide`](#attack-suicide)。

## `#sun` {#sun}

随机抽取一名群友。

## `#test` {#test}

测试 Bot 是否正常。

## `#time [时间戳]` {#time}

看看时间戳对应的 UTC+8 时间。如果不填写时间戳，则为当前时间。

## `#toilet` {#toilet}

见 [洗手间位置](./toilet)。

## `#toilet.cities` {#toilet-cities}

见 [洗手间位置](./toilet)。

## `#toilet.sort` <MasterBadge /> {#toilet-sort}

给洗手间数据排个序。

## `#toilet.update.<城市名>` <MasterBadge /> {#toilet-update}

更新该城市的洗手间数据。

## `#trash` {#trash}

见 [垃圾分类](./trash)。

## `#ttk` {#ttk}

同 [`#attack`](#attack)。

## `#ttk.rand` {#ttk-rand}

同 [`#attack.rand`](#attack-rand)。

## `#unicode <字符>` {#unicode}

查询字符的 Unicode 码位信息。

## `#v50` {#v50}

在疯狂星期四，Bot 给你 v50 请你吃 KFC。当然，也有可能是你 v Bot 50。

## `#welcome [欢迎语]` {#welcome}

设置群聊的新人入群欢迎消息。如果没有填写参数，则为查看群聊的当前欢迎消息。

## `#welcome.default` {#welcome-default}

重置群聊的新人入群欢迎消息为默认。

## `#yxh` <LvBadge lv=3 /> {#yxh}

生成一段营销号风格消息。

## `#_#` {#QwQ}

一个没什么实际作用的小彩蛋。
