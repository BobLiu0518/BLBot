<?php

function loadPermissionList()
{
    return json_decode(getData("usertype.json"));
}

function savePermissionList($banList)
{
    setData("usertype.json",json_encode($banList));
    exec("git add .;git commit -m \"\";git push");
    return;
}

?>