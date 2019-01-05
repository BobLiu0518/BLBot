<?php

requireSeniorAdmin();
global $CQ, $Queue;
/*****/$Queue[]= sendBack("Debug: 1");
$groupList = $CQ->getGroupList();
/*****/$Queue[]= sendBack("Debug: 2");
$idList = array();
/*****/$Queue[]= sendBack("Debug: 3");
foreach($groupList as $group){
    $member = json_decode($CQ->getGroupMemberList($group));
    $id = $member->user_id;
    $idList[]= $id;
}
/*****/$Queue[]= sendBack("Debug: 4");
$idList = array_unique($idList);
/*****/$Queue[]= sendBack("Debug: 5");
$Queue[]= sendBack("本 Bot 一共加了 ".count($groupList)." 个群，群成员（去重）一共有".count($idList)."个");

?>