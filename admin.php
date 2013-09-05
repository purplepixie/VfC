<?php

require_once("functions.php");
$now=time()-60*60;//less an hour for luck
starthtmlpage("Visions - Admin");

if(ismoderator())
{
	echo('<h2>Admin</h2>');
	?>
	<div class=""></div>
	<?php

	echo('<h3>Groups for approval</h3>');
	echo('<div id="main">');
	$res=getres("SELECT id,orgname,description,contactname,publicemail,tel,website,creator FROM org WHERE status='pending'");
	while($group=mysql_fetch_array($res,MYSQL_NUM)) {
		$sdesc=substr(str_replace(array("\r", "\r\n", "\n", "<br />", "<br>"), ' ', $group[2]),0,50);
		if(strlen($sdesc)<strlen(str_replace(array("\r", "\r\n", "\n", "<br />", "<br>"), ' ', $group[2])))
		{
			$sdesc.="...";
		}
		
		echo("<a href='group.php?id=$group[0]' title='Click for more info...'>");
		echo("<div class='divbox gpbox'>");
			echo("<div class='gptitle'>$group[1]</div>");
			echo("<div class='gpdesc'>$sdesc</div>");
		echo("</div>");	
		echo("</a>");
		$creator = getraw("user", "uname", "id=$group[7]", $null);
		$creator_email = getraw("user", "email", "id=$group[7]", $null);
		if($group[7]!=0) {
			echo("This group was created by: <b><a href='mailto:$creator_email' title='$creator_email'>$creator</a></b> (id=$group[7])");
		} else {
			echo("The group creator is not known.");
		}
	}
	echo('</div><br/>');

	echo('<h3>Events for approval</h3>');
	$now=time()-60*60;//less an hour for luck
	$res=getres("SELECT id FROM event WHERE status='pending' And start>$now ORDER BY start ASC");
	while($eid=mysql_fetch_array($res,MYSQL_NUM)) {

		$event=event_data($eid[0]);
		echo("<a href='event.php?eid=$event[id]'>");
		eventbox($event);
		echo("</a>");
		$creator = getraw("user", "uname", "id=$event[creator]", $null);
		$creator_email = getraw("user", "email", "id=$event[creator]", $null);
		if($event[creator]!=0) {
			echo("This event was created by: <b><a href='mailto:$creator_email' title='$creator_email'>$creator</a></b> (id=$event[creator])");
		} else {
			echo("The event creator is not known.");
		}

	}
	echo('<br style="clear:both"/>');

	echo('<h3>Send email</h3>');
	echo("<a href='send.php'>Email</a><br/><br/>");
	
	echo('<h3>Archives</h3>');
	echo('<a href="grouparchive.php">Groups</a><br/>');
	echo('<a href="eventarchive.php">Events</a><br/>');
	
	echo('<h3>Other Admin Functions</h3>');
	echo('<a href="listusers.php">List Subscribers and Users</a><br/>');
	echo('<a href="editinfo.php">Edit Homepage Information</a><br/>');

}
else
{
echo("Please log in  as a moderator to see this page");
}
endhtmlpage();

?>