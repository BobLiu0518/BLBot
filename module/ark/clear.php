<?php

global $Event;

$files = getDataFolderContents('ark/floor/');
foreach($files as $file){
	if(preg_match('/^'.$Event['user_id'].'.*$/', $file)){
		delData('ark/floor/'.$file);
	}
}

replyAndLeave('已清除保底数据～');

?>
