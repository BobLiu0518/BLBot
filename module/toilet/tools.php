<?php

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

function getReplyMsg($company, $station, $toilet){
	return $company.(mb_strlen($company.$station, 'UTF-8') >= 14 ? "\n" : ' ').$station.'站'
		.(in_array($company, ['香港鐵路', '臺北捷運']) ? '衛生間' : '卫生间')."：\n".$toilet;
}

function getExactStationData($station){
	$data = json_decode(getData('toilet/data.json'), true);
	$result = [];
	foreach($data as $companyName => $company){
		$stationNames = [$station];
		$toiletInfo = $company[$stationNames[0]];
		if(!$toiletInfo){
			continue;
		}else if(preg_match('/^StationName=(.+)$/', $toiletInfo, $match)){
			$stationNames = [$match[1]];
		}else if(preg_match('/^Redirect=(.+)$/', $toiletInfo, $match)){
			$stationNames = explode('&', $match[1]);
		}

		foreach($stationNames as $stationName){
			$result[] = getReplyMsg($companyName, $stationName, $company[$stationName]);
		}
	}
	return implode("\n\n", $result);
}

function getFuzzyStationNames($station, $unique = true){
	$data = json_decode(getData('toilet/data.json'), true);
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
	usort($similarNames, function($a, $b){
		return $a['distance'] - $b['distance'];
	});

	$result = [];
	foreach($similarNames as $stationName){
		$result[] = $stationName['name'];
	}
	if($unique){
		$result = array_unique($result);
	}

	return $result;
}

?>
