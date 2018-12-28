<?php
namespace kjBot\Frame;

global $Queue;
class UnauthorizedException extends \Exception{
    function __construct(){
        $Queue[]= sendBack(sendImg(getData("dt/pd".rand(1,20).".jpg")));
        switch(rand(1,3))
        {
            case 1:
            $this->message = "权限不足！停止你的瞎玩行为！";break;
            case 2:
            $this->message = "Permission denied...";break;
            case 3:
            $this->message = "没有权限…一定是哪里不对头";break;
        }
        $this->code = 401;
    }
}

class InsiderRequiredException extends \Exception{
    function __construct(){
        $this->message = '权！限！不！足！请先注册成为内测人员！';
        $this->code = 401;
    }
}

?>