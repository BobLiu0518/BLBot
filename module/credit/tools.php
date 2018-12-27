<?php

//leave('服务器迁移中，金币功能关闭，想加速迁移服务器可以看我空间给我打钱');

function getCredit($QQ){
    return (int)getData("credit/{$QQ}");
}

function setCredit($QQ, $credit, $set = false){
    if($set)setData('credit.history', "* {$QQ} {$credit}\n", true);
    return setData("credit/{$QQ}", (int)$credit);
}

function addCredit($QQ, $income){
    if($QQ==config('master')){
        return setCredit($QQ, getCredit($QQ), true);
    }
    setData('credit.history', "+ {$QQ} {$income}\n", true);
    return setCredit($QQ, getCredit($QQ)+(int)$income, true);
}

function decCredit($QQ, $pay){
    $balance = getCredit($QQ);
    if($QQ==config('master')){
        return setCredit($QQ, (int)($balance), true);
    }else if($balance >= $pay){
        setData('credit.history', "- {$QQ} {$pay}\n");
        return setCredit($QQ, (int)($balance-$pay), true);
    }else{
        if($balance == 0)throw new \Exception('余额不足，可使用 #checkin 命令获得金币！');
        throw new \Exception('余额不足，还需要 '.($pay-$balance).' 个金币！');
    }
}

function transferCredit($from, $to, $transfer, $fee = 1.05){
    decCredit($from, intval($transfer*$fee));
    addCredit($to, $transfer);
}

?>
