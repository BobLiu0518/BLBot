<?php

global $User_id;
loadModule('credit.tools');

switch(trim(getData('insider/'.$User_id))){
    case 'cancel':
        if(!setInsider($User_id)){
          leave("加入失败！");
        }
        setData('insider/'.$User_id, 'true');
        leave('感谢您的支持');
    case 'read':
        if(!setInsider($User_id)){
            leave("加入失败！");
        }
        addCredit($User_id, 114514);
        setData('insider/'.$User_id, 'true');
        setData('insider/r'.$User_id, 'true');
        leave("感谢您的支持，奖励 114514 金币\n请注意，如果您在将来要求退出内测计划，需要将这 114514 金币交还。若取消后再同意，不会再有 114514 金币奖励。");
    case 'true':
        leave("您已经加入 BL1040Bot 内测计划，如需取消请输入\n#insider.cancel");
    default:
        leave('请先阅读协议！');
}


?>