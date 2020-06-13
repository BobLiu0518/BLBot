<?php
namespace kjBot\Frame;

class LvlLowException extends \Exception{
    function __construct($required, $user){
        $this->message = '等级不足！使用本功能需要 Lv'.$required.'，您当前为 Lv'.$user.'！';
        $this->code = 401;
    }
}

?>
