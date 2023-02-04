<?php

// https://github.com/SkyDynamic/nonebot_plugin_jrrp/blob/main/nonebot_plugin_jrrp/__init__.py

function rol($num, $k, $bits = 64){
	$b1 = base_convert($num << $k, 10, 2);
	if(strlen($b1) <= $bits){
		return base_convert($b1, 2, 10);
	}else{
		return base_convert(substr($b1, -$bits), 2, 10);
	}
}

function get_hash($str){
	$num = 5381;
	$num2 = strlen($str) - 1;
	for($i = 0; $i <= $num2; $i++){
		$num = rol($num, 5) ^ $num ^ ord($str[$i]);
	}
	return gmp_xor($num, '12218072394304324399');
}

function get_jrrp($str, $d){
	$num = gmp_intval(gmp_abs((gmp_div_q(get_hash('asdfgbn'.(intval(date('z', $d)) + 1).'12#3$45'.date('Y', $d).'IUY'), 3) + get_hash('QWERTY'.$str.'0*8&6'.date('j', $d).'kjhg') / 3) / 527) % 1001 + 0.5);
	if($num >= 970){
		$num2 = 100;
	}else{
		$num2 = round($num / 969 * 99);
	}
	return $num2;
}

?>
