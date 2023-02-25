<?php

function getAttackData($user_id){
	global $Queue;
	$file = getData('attack/user/'.$user_id);
	$data = json_decode($file ? $file : '{"status":"free","end":"0","count":{"date":"0","times":0}}', true);

	if($data['status'] != 'free' && intval($data['end']) <= intval(date('Ymd'))){
		switch($data['status']){
			case 'imprisoned':
			case 'confined':
				$message = '恭喜出狱啦～';
				break;
			case 'hospitalized':
				$message = '恭喜出院啦～';
				break;
			case 'arknights':
				$message = '睁开眼，你发现自己回到了熟悉的世界。';
				break;
			case 'genshin':
				$message = '你推开门回到了原来的世界。';
				break;
		}
		$Queue[]= replyMessage($message);
		$data['status'] = 'free';
		$data['end'] = '0';
		setAttackData($user_id, $data);
	}

	if($data['count']['date'] < date('Ymd')){
		$data['count']['date'] = date('Ymd');
		$data['count']['times'] = 0;
		setAttackData($user_id, $data);
	}

	return $data;
}

function setAttackData($user_id, $data){
	setData('attack/user/'.$user_id, json_encode($data));
}

function getStatus($user_id){
	// free / imprisoned / confined / hospitalized / arknights / genshin
	return getAttackData($user_id)['status'];
}

function getStatusEndTime($user_id){
	$time = getAttackData($user_id)['end'];
	return substr_replace(substr_replace($time, '/', 6, 0), '/', 4, 0);
}

?>
