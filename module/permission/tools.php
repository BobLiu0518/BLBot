<?php

function loadBanList()
{
    return json_decode(getData("usertype.json"));
}

function saveBanList($banList)
{
    setData("usertype.json",json_encode($banList));
    exec("git add .;git commit -m \"\";git push");
    return;
}

?>