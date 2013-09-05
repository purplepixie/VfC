<?php
//header('Content-type:text/plain');
require_once("functions.php");

global $uid;

$mid=1*$_GET['id'];

$cont=getraw('emailsent','body',"id=$mid",$null);

if(substr($cont,0,6)!='<html>')
	$cont=nl2br($cont);

$cont=str_replace("^qu^","'",$cont);
$cont=str_replace('^dqu^','"',$cont);


echo($cont);

?>