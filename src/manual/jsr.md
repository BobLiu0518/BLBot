# 金山铁路车次

可以查询金山铁路车次的信息。数据来自 12306 官网。

## 金山铁路车站查询

**指令**：`#jsr <车站> [时间]`

查询指定车站在指定时间的车次信息。

-   如果不指定时间，默认为当前时间的 5 分钟前。

## 金山铁路车次查询

**指令**：`#jsr <车次>`

查询指定车次的信息。

## 金山铁路出行指南

**指令**：`#jsr.route [车站] [时间]`

查询指定车站（春申-金山卫）往返市区（莘庄/上海南）的最近班次。

-   如果不填写车站，则使用设置的默认车站（见下文）。
-   如果不指定时间，则为当前时间。
-   正午前优先展示去程（往市区），正午后优先展示返程（往金山卫）。

## 默认车站设置

**指令**：`#jsr.default [车站]`

设置指定车站（春申-金山卫）为出行指南默认查询的车站。

-   如果不填写车站，则为清除之前的设置。