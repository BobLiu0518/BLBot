<?php

requireLvl(6);
loadModule('blocker.tools');

replyAndLeave(blockBannedWords(nextArg(true)));