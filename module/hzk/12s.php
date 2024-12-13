<?php

loadModule('hzk.tools');
requireLvl(1);

replyAndLeave(getBrailledChar(nextArg(true), 'HZK12') ?? '生成失败…');
