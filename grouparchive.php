<?php
require_once("functions.php");
starthtmlpage("Archives");

if(ismoderator()) {
	echo('<h2>Groups List</h2>');
	$res=getres("SELECT id,orgname,description,contactname,publicemail,tel,website,status FROM org");
	echo('<table>');
	while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
		echo("<tr><td><a href=\"group.php?id=$arr[0]\">$arr[1]</a></td><td>$arr[7]</td></tr>");
	}
	echo('</table>');
} else {
	echo('Only admins can view archived groups.');
}
endhtmlpage();
?>