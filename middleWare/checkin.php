<?php

global $Message;

$checkin = array("签到","簽到","qd","缺德","确定","驱动","前挡","恰当","前端","期待","全都","取得","去掉","氢弹","桥墩",
"渠道","球队","清单","裙底","强调","青岛","启动","签订","缺点","强度","起点","取代","祈祷","强盗","抢夺","切断","清淡",
"轻度","情调","取缔","情敌","权当","祛痘","倾倒","气垫");

foreach($checkin as $word)
	if(preg_match('/^'.$word.'/', $Message)){
    		loadModule('checkin');leave();
	}

if(preg_match('/^签出/', $Message) || preg_match('/^簽出/', $Message)){
    loadModule('checkout');leave();
}
?>
