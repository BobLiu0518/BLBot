<?php

requireSeniorAdmin();
global $CQ, $Queue;
$groupList = $CQ->getGroupList();
$idList = array();
foreach($groupList as $group){
    $member = json_decode($CQ->getGroupMemberList($group));
    $id = $member->user_id;
    $idList[]= $id;
}
$idList = array_unique($idList);
$Queue[]= sendBack("本 Bot 一共加了 ".count($groupList)." 个群，群成员（去重）一共有".count($idList)."个");

?>