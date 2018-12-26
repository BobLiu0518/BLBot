<?php
namespace kjBot\Frame;

class UnauthorizedException extends \Exception{
    function __construct(){
        $this->message='权！限！不！足！停止你的瞎玩行为！';
        $this->code=401;
    }
}

class InsiderRequiredException extends \Exception{
    function __construct(){
        $this->message='权！限！不！足！请先注册成为内测人员！';
        $this->code=401;
    }
}

?>