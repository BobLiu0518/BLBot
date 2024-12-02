<?php

if(!config("alias", false)) leave('功能不开放');

function getAliasDb() {
    static $db;
    if(!$db) $db = new BLBot\Database('alias');
    return $db;
}

function getAlias($user_id) {
    $db = getAliasDb();
    $data = $db->get(intval($user_id));
    return $data ? ($data['aliases'] ?? []) : [];
}

function setAlias($user_id, $alias, $origin) {
    $db = getAliasDb();
    $aliases = getAlias($user_id);
    $aliasLimits = [
        ['level' => 2, 'limit' => 3],
        ['level' => 3, 'limit' => 5],
        ['level' => 4, 'limit' => 8],
        ['level' => 5, 'limit' => 12],
    ];
    foreach($aliasLimits as $limit) {
        if(count($aliases) >= $limit['limit']) {
            requireLvl($limit['level'] + 1, '设置更多别名', '使用 #alias.del 删掉一些');
        }
    }
    return $db->set($user_id, ['aliases.'.$alias => $origin]);
}

function clearAlias($user_id) {
    $db = getAliasDb();
    return $db->delete(intval($user_id));
}

function delAlias($user_id, $alias) {
    $db = getAliasDb();
    return $db->remove(intval($user_id), 'aliases.'.$alias);
}

function parseCommandName($name) {
    global $Config;
    if($Config['enablePrefix2']) {
        $pattern = '/^('.$Config['prefix'].'|'.$Config['prefix2'].')/u';
    } else {
        $pattern = '/^'.$Config['prefix'].'/u';
    }
    return strtolower(preg_replace($pattern, '', $name));
}
