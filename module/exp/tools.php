<?php

if(!function_exists('getLvl')){
    function getRealExp($QQ){
        return (int)getData("exp/{$QQ}");
    }

    function getExp($QQ){
        $exp = getRealExp($QQ);
        return $exp>=9999999?"∞":$exp;
    }

    function setExp($QQ, $Exp, $set = false){
        if($set)setData('exp.history', "* {$QQ} {$Exp}\n", true);
        return setData("exp/{$QQ}", (int)$Exp);
    }

    function addExp($QQ, $income){
        setData('exp.history', "+ {$QQ} {$income}\n", true);
        return setExp($QQ, getRealExp($QQ)+(int)$income, true);
    }

    function getLvl($QQ){
        $exp = getRealExp($QQ);
        if($exp >= 999999999) return 6;
        else if($exp >= 99999999) return 5;
        else if($exp >= 9999999) return 4;
        else if($exp >= 365) return 3;
        else if($exp >= 30) return 2;
        else if($exp >= 7) return 1;
        else if($exp >= 0) return 0;
        else return -1;
    }

}

?>
