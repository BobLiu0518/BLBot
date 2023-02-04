<?php

requireMaster();

if(!$group = trim(nextArg()))leave("No group id.");
if(!is_numeric($group))leave("Invaild group id ".$group.".");

$whitelist = json_decode(getData("grouplist.json"),true);
$whitelist["groups"][] = $group;
$json = json_encode($whitelist);
setData("grouplist.json", $json);
leave($group." added.");

?>
