<?php

function initConfig($group_id)
{
	$config = array(
		seniorAdmin => array(
			
		),
		admin => array(
			
		)
	);
	setConfig($group_id, $config);
	return $config;
}

function loadConfig($group_id)
{
	if($config = json_decode(getData('gm/config/'.$group_id.'.json'), TRUE))
		return $config;
	else
	{
		return initConfig($group_id);
	}
}

function setConfig($group_id, $config)
{
	setData('gm/config/'.$group_id.'.json',$config);
}

function rmConfig($group_id)
{
	delData('gm/config/'.$group_id.'.json');
}

function isGroupSeniorAdmin($group_id, $user_id)
{
	$config = loadConfig($group_id);
	foreach($config['seniorAdmin'] as $seniorAdmin)
		if($seniorAdmin == $user_id)return true;
	return false;
}

function isGroupAdmin($group_id, $user_id)
{
        $config = loadConfig($group_id);
        foreach($config['admin'] as $admin)
                if($admin == $user_id)return true;
        return false;
}

?>
