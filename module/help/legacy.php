<?php

if(!fromGroup() || nextArg()){
	replyAndLeave("常用指令：
#checkin 签到
#me 查看自身信息
#rank 查看群内财富榜
#welcome (内容) 设置新人欢迎,限管理员使用
#welcome.default 恢复新人欢迎为默认
#feedback <内容> 向Bot反馈

别名指令：
#alias 查看当前设置的别名
#alias.set <别名> <原名> 设置别名
#alias.del <别名> 删除别名

娱乐指令：
#rh.join 赛马,发起赛马需要Lv3
#rh.ban 禁止本群赛马,限管理员使用
#attack <@群友> 打劫群友的金币
#attack.config <倍率> 设置抢劫倍率,限管理员使用
#choose <选项...> 在选项中随机选择
#roll (最小值) (最大值) 生成随机数
#jrrp 查看今日人品,仅供娱乐
#randomBan 随机禁言自己,需要Bot是管理员
#yxh <名词> (动词) 生成营销号文章

实用指令：
#qrcode <内容> 生成二维码
#voice <内容> 让Bot开口说话
#trash <垃圾名> 垃圾分类查询

公交查询：
#shjt <线路名> (方向) “上海交通”查询
#sjwgj <线路名> (方向) “松江微公交”查询
#jst <线路名> (方向) “久事公交”查询
#jjt <线路名> (方向) “嘉定公交”查询
#mkt <线路名> (方向) “闵行客运”查询
注：上述(方向)填“上行”或“下行”
　　<线路名>包含“路”等字样

哔哩哔哩相关指令：
#bili.bind <uid> 绑定账号
#bili (uid) 查看账号信息
#bili.video <av/bv> 查看视频信息
#bili.subscribe 查看群订阅的UP
#bili.subscribe <uid> 订阅UP主
#bili.unsubscribe <uid> 取消订阅UP主
#bili.av <bv> BV号转av号
#bili.bv <av> av号转BV号

明日方舟相关指令：
#ark.gacha (卡池) (次数) 模拟抽卡
#ark.clear 清除抽卡保底数据

<尖括号>参数必填，(圆括号)参数选填
尖括号和圆括号仅做说明使用
使用指令时请不要加括号
也不要漏掉空格或点号哦");
}else{
	replyAndLeave("为避免群聊中刷屏，请查看 Bot QQ空间阅读帮助。如群内不介意刷屏，可发送 #help full 查看完整帮助。");
}

?>
