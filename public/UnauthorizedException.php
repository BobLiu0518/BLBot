<?php
namespace kjBot\Frame;


class UnauthorizedException extends \Exception{
    function __construct(){
        $img = sendImg(getData("dt/pd".rand(1,23).".jpg"));
        switch(rand(1,3))
        {
            case 1:
            $this->message = $img."\n权限不足！停止你的瞎玩行为！";break;
            case 2:
            $this->message = $img."\nPermission denied...";break;
            case 3:
            $this->message = $img."\n没有权限…一定是哪里不对头";break;
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