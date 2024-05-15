<?php

$data = json_decode(getData('toilet/data.json'), true);
$stations = [];
$threshold = max(nextArg() ?? 5, 3);
foreach($data as $company){
	foreach($company as $station => $toilet){
		$stations[] = $station;
	}
}
$count = array_count_values($stations);
arsort($count);

$reply = [];
foreach($count as $station => $times){
	if($times >= $threshold) $reply[] = '['.$times.'] '.$station;
	else break;
}
replyAndLeave(implode("\n", count($reply) ? $reply : ['无']));

?>
