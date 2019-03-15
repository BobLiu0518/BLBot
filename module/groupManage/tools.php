global $Event;

function getQ(){
	$QQ = nextArg();
	if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
		$QQ = parseQQ($QQ);
	}
	return $QQ??$Event['user_id'];
}

function getQN(){
	$QQ = nextArg();
	if(!(preg_match('/\d+/', $QQ, $match) && $match[0] == $QQ)){
	        $QQ = parseQQ($QQ);
	}
	return $QQ;
}

function getTime(){
	$time='';
	while(true){
		$x=nextArg();
		if($x !== NULL){
			$time.=$x.' ';
		}else{
			break;
		}
	}
	return $time;
}

function requireGroupHigherPermission(){
	requireAdmin();
}

function requireBotGroupAdminPermission(){

}
