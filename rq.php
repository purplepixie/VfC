<?php

include("functions.php");
$it=$_GET['it'];

$res=getres("SELECT buildingroom,address1,address2,towncity,postcode FROM event WHERE id='$it'");
while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
	foreach($arr as $o) {
		echo($o."|-|");
	}
}


?>