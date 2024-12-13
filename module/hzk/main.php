<?php

loadModule('hzk.tools');
requireLvl(1);

replyAndLeave(getBrailledChar(nextArg(), nextArg(true)) ?? '生成失败…');
