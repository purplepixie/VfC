<?php
require_once("functions.php");
starthtmlpage("Archives");

if(ismoderator()) {
	echo('<h2>List of Users and Subscribers</h2>');
	echo('<p>This is a list of subscribers and users on the system.  If the username field is empty, they are just subscribers.  If they have a username, they are registered as a user.</p>');
	$res=getres("SELECT uname,email,activated,digest,mail FROM user");
	echo('<table>');
	echo ("<tr><th>Username</th><th>Email Address</th><th>Activated</th><th>Digest</th><th>Subscribed</th></tr>");
	while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
		echo("<tr><td>$arr[0]</a></td><td>$arr[1]</td><td>$arr[2]</td><td>$arr[3]</td><td>$arr[4]</td></tr>");
	}
	echo('</table>');
} else {
	echo('Only admins can view subscriber details.');
}
endhtmlpage();
?>