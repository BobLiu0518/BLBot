<?php

function getCredit($QQ) {
    return (int)getData("credit/{$QQ}");
}

function setCredit($QQ, $credit, $set = false){
    if($set)setData('credit.history', "* {$QQ} {$credit}\n", true);
    return setData("credit/{$QQ}", (int)$credit);
}

function addCredit($QQ, $income) {
    setData('credit.history', "+ {$QQ} {$income}\n", true);
    return setCredit($QQ, getCredit($QQ)+(int)$income, true);
}

function decCredit($QQ, $pay, $force = false) {
    $balance = getCredit($QQ);
    if($balance >= $pay || $force) {
        setData('credit.history', "- {$QQ} {$pay}\n");
        return setCredit($QQ, (int)($balance - $pay), true);
    } else {
        if($balance == 0) replyAndLeave('余额不足，签到即可获取金币哦！');
        replyAndLeave('余额不足，还需要 '.($pay - $balance).' 个金币哦，多多签到获取金币吧！');
    }
}

function transferCredit($from, $to, $transfer, $fee = 1.01) {
    decCredit($from, ceil($transfer * $fee));
    addCredit($to, $transfer);
}
