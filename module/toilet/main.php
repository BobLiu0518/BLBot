<?php

global $Message, $Command;

function utf8_to_extended_ascii($str, &$map){
	$matches = array();
	if (!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)){
		return $str;
	}
	foreach ($matches[0] as $mbc){
		if (!isset($map[$mbc])){
			$map[$mbc] = chr(128 + count($map));
		}
	}

	return strtr($str, $map);
}
function levenshtein_utf8($s1, $s2){
	$charMap = array();
	$s1 = utf8_to_extended_ascii($s1, $charMap);
	$s2 = utf8_to_extended_ascii($s2, $charMap);
	return levenshtein($s1, $s2);
}

if($Command[0] == 'middleWare/toilet'){
	$station = trim($Message);
}else{
	$station = implode(' ', array_splice($Command, 1));
}
if(!$station){
	if($Command[0] == 'middleWare/toilet') leave();
	else replyAndLeave('要查询什么车站呢？');
}
$data = json_decode(getData('toilet/data.json'), true);
$reply = '';
$companies = [];
foreach($data as $companyName => $company){
	if($company[$station]) $reply .= "\n\n".$companyName.' '.$station."站卫生间：\n".$company[$station];
	$companies[] = $companyName;
}
if(!strlen($reply)){
	$similarNames = [];
	foreach($data as $companyName => $company){
		foreach($company as $stationName => $stationInfo){
			$strDistance = levenshtein_utf8($station, $stationName);
			if(mb_strlen($station) >= 2 && mb_strpos($stationName, $station, 0, 'UTF-8') !== false){
				$similarNames[] = [
					'name' => $stationName,
					'distance' => 0,
				];
			}else if($strDistance <= min(4, mb_strlen($station, 'UTF-8') / 2)){
				$similarNames[] = [
					'name' => $stationName,
					'distance' => $strDistance,
				];
			}
		}
	}
	$reply = '没有查询到名为 '.$station." 的车站哦…";
	if(count($similarNames)){
		usort($similarNames, function($a, $b){
			return $a['distance'] - $b['distance'];
		});
		$reply .= "\n你可能想找：";
		foreach($similarNames as $name){
			$reply .= $name['name'].' ';
		}
	}else if($Command[0] == 'middleWare/toilet'){
		leave();
	}
	$reply .= "\n使用指令 #toilet.cities 可以查询支持的城市～";
}

replyAndLeave(trim($reply));

?>
