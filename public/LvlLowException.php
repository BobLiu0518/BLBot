<?php
namespace kjBot\Frame;

class LvlLowException extends \Exception{
    function __construct($required, $user, $message = '本指令', $resolve = null){
        global $Event;
        $this->message = '[CQ:reply,id='.$Event['message_id'].']'.$message.'需要 Lv'.$required.'，您当前为 Lv'.$user.' '.(rand(0, 10) ? '哦～' : '喵～');
	if($required <= 3){
		$this->message .= ' 多多签到领取经验升级'.($resolve ? '，或'.$resolve : '').'吧～';
	}else if($resolve){
		$this->message .= $resolve.'～';
	}
        $this->code = 401;
    }
}

?>
