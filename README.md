# BLBot Lite

[![License](https://img.shields.io/badge/License-MIT%20%26%20AGPL-red.svg)](LICENSE)
[![Based on kjBot](https://img.shields.io/badge/Based%20on-kjBot-brightgreen.svg)](https://github.com/kj415j45/kjBot)
[![Dev group](https://img.shields.io/badge/Dev%20group-789029454-blue.svg)](https://jq.qq.com/?_wv=1027&k=5FBe63r)

BLBot 基于 [kjBot](https://github.com/kj415j45/kjBot) 开发，是一个轻量级多功能的 QQ 机器人。

BLBot Lite 在 [BLBot](https://github.com/BobLiu0518/BLBot/tree/main/) 上，精简了部分功能，并改用 [QQ 开放平台](https://bot.q.qq.com/wiki) 的官方接口。

## 安装依赖

在开始之前，你需要安装 BLBot 依赖的运行环境：

### OneBot 环境

Lite 版开发目标为 Gensokyo。为获得最好的效果，请您使用 [Gensokyo](https://github.com/Hoshinonyaruko/Gensokyo) 运行。

### 数据库

BLBot 使用 [MongoDB](https://www.mongodb.com/) 作为数据库。

为了使用 MongoDB，你需要创建一个 BLBot 使用的账户。可在 `mongosh` 中执行以下指令：

```javascript
db.createUser({
    user: 'appUser', // 用户名
    pwd: 'appPassword', // 密码
    roles: [{ role: 'readWrite', db: 'BLBotLite' }],
});
```

将其中的 `user` 和 `pwd` 字段的内容分别填入 `config.ini` 的 `dbUsername` 和 `dbPassword` 中。如果没有更改过 MongoDB 的端口号，`dbPort` 可以留空。

## 框架结构

```
/
|--SDK/ #kjBot\SDK
|--public/
    |--tools/ #各类开放函数的文件
    |--index.php #入口文件
    |--init.php #初始化用
    |......
|--vendor/ #包目录
|--storage/ #请确保运行 PHP 的用户具有这个文件夹的写权限
    |--data/ #数据文件夹
        |--error.log #如果出现异常未捕获则会在此存放日志
        |......
    |--cache/ #缓存文件夹
|--module/ #在这里开始编写你的模块吧 :)
    |--......
|--config.ini.example #配置文件样例，本地部署时请复制为 config.ini 并根据实际情况调整
|--build.sh #进行环境配置
|--run.sh #一键部署（大概 :v
```

## 感谢

-   [richardchien/coolq-http-api](https://github.com/richardchien/coolq-http-api)
    -   酷 Q 与许多 Bot 之间的桥梁
-   [kilingzhang/coolq-php-sdk](https://github.com/kilingzhang/coolq-php-sdk)
    -   kjBot 的起源
-   [kj415j45/jkBot](https://github.com/kj415j45/jkBot)
    -   kjBot 的零代
-   框架作者
    -   [kj415j45](https://github.com/kj415j45)
-   贡献者
    -   [Cyanoxygen](https://github.com/Cyanoxygen)
    -   [Baka-D](https://github.com/Baka-D)
    -   [lslqtz](https://github.com/lslqtz)
    -   [LovelyA72](https://github.com/LovelyA72)

## LICENSE

BLBot 框架及 SDK 均为 MIT 协议。但是模块与中间件均为 AGPL 协议，如果您希望闭源开发，请不要使用该项目提供的模块和中间件。
