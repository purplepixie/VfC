<?php
require_once("functions.php");
starthtmlpage("Archives");

if(ismoderator()) {
	echo('<h2>Events List</h2>');
	echo('<h3>Past Events</h3>');
	$now=time()-60*60;//less an hour for luck
	$res=getres("SELECT id,title,start,status FROM event WHERE start<$now ORDER BY start ASC");
	echo('<table>');
	while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
		echo("<tr><td><a href=\"event.php?eid=$arr[0]\">$arr[1]</a></td><td>$arr[3]</td></tr>");
	}
	echo('</table>');
	echo('<h3>Future Events</h3>');
	$res=getres("SELECT id,title,start,status FROM event WHERE start>$now ORDER BY start ASC");
	echo('<table>');
	while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
		echo("<tr><td><a href=\"event.php?eid=$arr[0]\">$arr[1]</a></td><td>$arr[3]</td></tr>");
	}
	echo('</table>');
} else {
	echo('Only admins can view archived events.');
}
endhtmlpage();
?>