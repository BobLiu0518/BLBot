# BLBot

[![License](https://img.shields.io/badge/License-MIT%20%26%20AGPL-red.svg)](LICENSE)
[![Based on kjBot](https://img.shields.io/badge/Based%20on-kjBot-brightgreen.svg)](https://github.com/kj415j45/kjBot)
[![Dev group](https://img.shields.io/badge/Dev%20group-789029454-blue.svg)](https://jq.qq.com/?_wv=1027&k=5FBe63r)

BLBot 基于 [kjBot](https://github.com/kj415j45/kjBot) 开发，是一个轻量级多功能的 QQ 机器人。

## 用户手册

用户手册见：[bot.bobliu.tech](https://bot.bobliu.tech/)

## 安装依赖

在开始之前，你需要安装 BLBot 依赖的运行环境：

### OneBot 环境

本项目开发目标为 Lagrange。为获得最好的效果，请您使用 [Lagrange.OneBot](https://github.com/KonataDev/Lagrange.Core) 运行。

使用 [其他 OneBot 11/10 实现](https://onebot.dev/ecosystem.html#onebot-11-10-cqhttp) 也能运行 BLBot，但部分功能可能不可用。

### 数据库

BLBot 使用 [MongoDB](https://www.mongodb.com/) 作为数据库。

为了使用 MongoDB，你需要创建一个 BLBot 使用的账户。可在 `mongosh` 中执行以下指令：

```javascript
db.createUser({
    user: 'appUser', // 用户名
    pwd: 'appPassword', // 密码
    roles: [{ role: 'readWrite', db: 'BLBot' }],
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
|--middleWare/ #中间件，用于处理非命令
    |--Chain.php #中间件链，用于调整中间件顺序以及启用状态
    |--......
|--module/ #在这里开始编写你的模块吧 :)
    |--......
|--config.ini.example #配置文件样例，本地部署时请复制为 config.ini 并根据实际情况调整
|--build.sh #进行环境配置
|--run.sh #一键部署（大概 :v
```

## 上手

### 快速安装

```sh
git clone https://github.com/BobLiu0518/BLBot.git
cd BLBot/
./build.sh
```

仅作为框架使用时请清除 `composer.json` 内的 `require` 以及 `module/`、`middleWare/` 文件夹内的全部内容。

### 入门

`public/init.php` 中存在一个全局变量区供编写模块的程序员使用，约定本框架产生的全局变量均为大写字母开头。

`public/tools/` 下的文件将为框架扩展各类方法，请仔细阅读。

### 编写第一个模块

假定该模块为 `hello/main.php` ，向 bot 发送 `#hello` 即可触发该模块。

```php
<?php

global $Queue; //从全局变量区中导入 $Queue 数组，该数组提供消息队列的功能

if(!fromGroup()){ //如果消息不是来自群聊
    $Queue[]= sendBack('Hello, world!'); //向消息队列尾插入一句 'Hello, world!'，在哪收到就发到哪，此处只会在私聊中发送
}else{
    leave(); //从模块中退出，不再执行下面的语句
}

?>
```

### 编写更多模块

参考 `module/` 文件夹下的其他模块

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
