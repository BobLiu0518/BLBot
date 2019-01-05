<?php

requireSeniorAdmin();
global $CQ, $Queue;
$groupList = $CQ->getGroupList();
$idList = array();
foreach($groupList as $group){
    $memberList = json_decode($CQ->getGroupMemberList($group->group_id));
    foreach($memberList as $member)
        $idList[]= $member->user_id;
}
/*****/$Queue[]= sendBack("Debug: 4");
$idList = array_unique($idList);
/*****/$Queue[]= sendBack("Debug: 5");
$Queue[]= sendBack("本 Bot 一共加了 ".count($groupList)." 个群，群成员（去重）一共有".count($idList)."个");

?>