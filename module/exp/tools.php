<?php

if(!function_exists('getLvl')) {
    function getRealExp($QQ) {
        return (int)getData("exp/{$QQ}");
    }

    function getExp($QQ) {
        $exp = getRealExp($QQ);
        return $exp >= 999999999 ? "∞" : $exp;
    }

    function setExp($QQ, $Exp, $set = false) {
        if($set) setData('exp.history', "* {$QQ} {$Exp}\n", true);
        return setData("exp/{$QQ}", (int)$Exp);
    }

    function addExp($QQ, $income) {
        setData('exp.history', "+ {$QQ} {$income}\n", true);
        return setExp($QQ, getRealExp($QQ) + (int)$income, true);
    }

    function getLvlMap() {
        return [
            ['lvl' => 6, 'exp' => 999999999],
            ['lvl' => 5, 'exp' => 365],
            ['lvl' => 4, 'exp' => 120],
            ['lvl' => 3, 'exp' => 15],
            ['lvl' => 2, 'exp' => 7],
            ['lvl' => 1, 'exp' => 1],
            ['lvl' => 0, 'exp' => 0],
            ['lvl' => -1, 'exp' => -INF],
        ];
    }

    function getLvl($QQ) {
        $exp = getRealExp($QQ);
        $lvlMap = getLvlMap();
        foreach($lvlMap as $lvl) {
            if($exp >= $lvl['exp']) {
                return $lvl['lvl'];
            }
        }
    }

}
