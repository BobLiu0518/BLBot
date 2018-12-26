<?php

global $User_id;
loadModule('credit.tools');

if(trim(getData('insider/'.$User_id))!='true')leave();

if(!cancelInsider($User_id)){
    leave("取消失败，请联系2018962389取消！");
}

if(trim(getData('insider/r'.$User_id))=='true')decCredit($User_id, 114514);
setData('insider/'.$User_id, 'cancel');
setData('insider/r'.$User_id, 'false');

leave('您已取消 BL1040Bot 的内测计划。');

?>