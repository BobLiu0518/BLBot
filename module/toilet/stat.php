<?php

$data = json_decode(getData('toilet/data.json'), true);
$stations = [];
foreach($data as $company){
	foreach($company as $station => $toilet){
		$stations[] = $station;
	}
}
$count = array_count_values($stations);
$count = array_filter($count, function($value){ return $value > 2; });
arsort($count);
replyAndLeave(var_export($count, true));

?>
