# 功能管理

通过功能管理，群主/管理员可以设置部分 BLBot 功能在群内禁用。

功能管理有两种匹配模式：黑名单模式和白名单模式。在黑名单模式下，无法调用匹配列表中的指令；白名单模式下，无法调用匹配列表外的指令。但是，无论如何设置，`#config` 指令都始终可用。

指令匹配只能设置一级指令（即不带点号的指令）；设置后，对应的所有一级或多级指令均会被匹配。例如，设置 `#attack` 为黑名单后，`#attack.rand` 也无法调用。

## 查看当前群功能管理

**指令**：`#config`

## 设置匹配模式

**指令**：`#config.mode <模式>`

模式可选 `黑名单` 或 `白名单`。

## 将指令加入匹配列表

**指令**：`#config.add <指令>`

## 将指令移出匹配列表

**指令**：`#config.remove <指令>`

## 启用或禁用静默模式

**指令**：`#config.silence`

静默模式下，调用被禁用的指令不会有**任何**响应。