<?php

switch($Event['request_type']){
    case 'friend':
//        if(config('allowFriends') != 'ignore')
              $CQ->setFriendAddRequest($Event['flag'], config('allowFriends')); //交给master二次审核？
//        $Queue[]= sendMaster('Being friends with '.$Event['user_id']); //通知master
//        $Queue[]= sendDevGroup('Being friends with '.$Event['user_id']);
//        $Queue[]= sendBack(config('addFriendMsg')); //发送欢迎消息
        break;
    case 'group':
        switch($Event['sub_type']){
            case 'add':
                //TODO 新人加群的情况可能需要中间件来处理
                break;
            case 'invite':
//                if(config('allowGroups') != 'ignore')
//                    $CQ->setGroupAddRequest($Event['flag'], $Event['sub_type'], config('allowGroups'));
//                $Queue[]= sendMaster('Join Group '.$Event['group_id'].' by '.$Event['user_id'].', flag: '.$Event['flag']); //通知master
//                $Queue[]= sendDevGroup('Join Group '.$Event['group_id'].' by '.$Event['user_id'].', flag: '.$Event['flag']);
                break;
            default:
        }
        break;
    default:

}

?>
