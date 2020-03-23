<?php

global $Queue;

function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
	$return = '';
	if (function_exists('mb_get_info')) {
		for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
			$str = mb_substr ( $string, $x, 1, $in_encoding );
			if (strlen ( $str ) > 1) {
				$return .= '%u'.strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
			} else {
				$return .= $str;
			}
		}
	}
	return $return;
}

$apiA = "http://mbst.shdzyb.com:36115/interface/getBase.ashx?sign=&name=";
$apiB = "http://mbst.shdzyb.com:36115/interface/getStopList.ashx?name=";

$route = nextArg();
$upDown = nextArg();
if($route && $upDown == '上行' || $upDown == '上' || $upDown == '0' || $upDown === NULL)$upDown = '0';
else if($route && $upDown == '下行' || $upDown == '下' || $upDown == '1')$upDown = '1';
else leave('参数错误！');

$dataA = json_decode(getData('jst/'.$route.'a.json'),true);
$dataB = json_decode(getData('jst/'.$route.'b-'.$upDown.'.json'),true);
if(!$dataA){

	//久事封堵API期间禁止查询
	//leave('由于久事集团封堵API，暂无法提供未缓存线路的站级信息，可尝试使用 #shjt 命令替代。');

	$dataA = json_decode(json_encode(simplexml_load_string(iconv('GB2312', 'UTF-8', file_get_contents($apiA.escape($route))))), true);
	setData('jst/'.$route.'a.json', json_encode($dataA));
}
if($dataA['error'])leave("查询A失败：".$dataA['error']);
if(!$dataB){
	$dataB = json_decode(iconv('GB2312', 'UTF-8', file_get_contents($apiB.escape($route)."&lineid=".trim($dataA['line_id'])."&dir=".$upDown)), true);
	setData('jst/'.$route.'b-'.$upDown.'.json', json_encode($dataB));
}
if($dataB['code'] <= 0)leave('查询B失败['.$dataB['code'].']：'.$dataB['msg']);

// 线路元信息
$lineName = trim($dataA['line_name']);
$lineId = trim($dataA['line_id']);

$reply = <<<EOT
线路名：{$lineName}
线路编码：{$lineId}

EOT;
$reply .= '运营时间：'.trim($dataA[($upDown?'end':'start').'_earlytime']).'-'.trim($dataA[($upDown?'end':'start').'_latetime'])."\n\n";
$reply .= $upDown?'下行 设站：':'上行 设站：';

// 线路设站
foreach($dataB['data'] as $station)
		$reply .= "\n".$station['id'].' '.$station['name'];

$reply .= <<<EOT


如果需要切换上下行，
请在命令最后加上上行或者下行！
如单环线不显示中途站，请尝试查询下行！

相关命令：
上海交通 #shjt  松江公交 #sjwgj
浦东公交 #pjt  嘉定公交 #jjt
EOT;

$Queue[]= sendBack($reply);

?>
