<?php

replyAndLeave('本功能已停止服务。');

global $Event;

delData('ark/user/'.$Event['user_id']);

replyAndLeave('已清除保底数据～');

