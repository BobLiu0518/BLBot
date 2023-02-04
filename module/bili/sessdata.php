<?php

requireMaster();

$data = nextArg();
if(!$data){
	replyAndLeave('未填写 SESSDATA');
}
setData('bili/api/sessdata', $data);
replyAndLeave('设置成功');

?>
