<?php

global $Queue;
requireSeniorAdmin();
loadModule('bst.tools');
$api = "http://61.129.57.72:8181/Ajax/Handler.ashx?Method=station&roadline=";

$rl = nextArg();
if(!$rl)leave('请输入线路！');
if(!isLine($rl))leave('找不到线路 '.$rl.'！');
$isDown = nextArg();
if($isDown == '上行' || $isDown == '上' || $isDown == '0' || $isDown === NULL)$isDown = false;
else if($isDown == '下行' || $isDown == '下' || $isDown == '1')$isDown = true;
$upDown = $isDown?"下行":"上行";

//$result = getData('bst/'.$rl);
//if(!$result){
	$result = json_decode(file_get_contents($api.$rl), true);
	//setData('bst/'.$rl, $result);
//}
//if(!$result['Count'])leave('没有信息！');

$reply = <<<EOT
线路名：$rl
$upDown 设站：
EOT;

foreach($result['data'] as $station){
	if($station['Upstream'] == "true" && !$isDown)continue;
	else if($station['Downstream'] == "true" && $isDown)continue;
	$reply .= <<<EOT

{$station['LevelId']} {$station['LevelName']}
EOT;
}

$reply .= <<<EOT

如果需要切换上下行，
请在命令最后加上“上行”或者“下行”！
EOT;

$Queue[]= sendBack($reply);

?>
