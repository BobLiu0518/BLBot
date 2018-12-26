<?php

global $User_id;
loadModule('credit.tools');

switch(trim(getData('recordStat/'.$User_id))){
    case 'cancel':
        setData('recordStat/'.$User_id, 'true');
        leave('感谢您的支持');
    case 'read':
        addCredit($User_id, 114514);
        setData('recordStat/'.$User_id, 'true');
        setData('recordStat/r'.$User_id, 'true');
        leave("感谢您的支持，奖励 114514 金币\n请注意，如果您在将来要求取消记录，需要将这 114514 金币交还。若取消后再同意，不会再有 114514 金币奖励。");
    case 'true':
        leave("您已经同意 BL1040Bot 记录您的使用情况，如需取消请输入\n#recordStat.cancel");
    default:
        leave('请先阅读协议！');
}


?>