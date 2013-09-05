<?php
require_once("functions.php");
starthtmlpage("Visions - Groups");
$now=time()-60*60;//less an hour for luck
?>
<div id="main">
<h2>Groups</h2>
<?php

if($uid!='')
{
?>
	<div><a href="addorg.php">Add a group</a></div>
<?php
}
else
{
	echo("<div><a href='login.php?fwd=addorg.php'>login</a> to add an group</div>");
}


//echo('<br style="clear:both"/>');

$res=getres("SELECT id,orgname,description,contactname,publicemail,tel,website FROM org WHERE status='approved' ORDER BY orgname");

while($group=mysql_fetch_array($res,MYSQL_NUM))
{
	$sdesc=substr(str_replace(array("\r", "\r\n", "\n", "<br />", "<br>"), ' ', $group[2]),0,125);
	if(strlen($sdesc)<strlen(str_replace(array("\r", "\r\n", "\n", "<br />"), ' ', $group[2])))
	{
		$sdesc.="...";
	}
	$email='<div class="dumbtag" title="No email address for this group."></div>';
	$web='<div class="dumbtag" title="No website for this group."></div>';
	$res2=getres("SELECT id FROM event WHERE status = 'approved' AND start>$now AND org=$group[0]");
	if (mysql_num_rows($res2) != 0) 
		$events="<a href='events.php?group=$group[0]'><div class='gptag' title='List of $group[1] events'>events</div></a>";
	else
		$events='<div class="dumbtag" title="No current events organised by this group."></div>';
	if($group[4]!='')
		$email="<a href='mailto:$group[4]'><div class='gptag' title='$group[3]: $group[4]'>email</div></a>";
	if($group[6]!='') {
		if(substr($str,0,7)=="http://") {
			$web="<a href='$group[6]'><div class='gptag' title='$group[6]'>web</div></a>";
		} else {
			$web="<a href='http://$group[6]'><div class='gptag' title='http://$group[6]'>web</div></a>";
		}
	}
	
	echo("$events");
	echo("$web");
	echo("$email");	
	echo("<a href='group.php?id=$group[0]' title='Click for more info...'>");
	echo("<div class='divbox gpbox'>");
		echo("<div class='gptitle'>$group[1]</div>");
		echo("<div class='gpdesc'>$sdesc</div>");
	echo("</div>");	
	echo("</a>");
}

// echo('<table>');
// while($arr=mysql_fetch_array($res,MYSQL_NUM))
// {
	// $sdesc=substr($arr[2],0,45);
	// $email='';
	// $web='';
	// if($arr[4]!='')
		// $email="<a href='mailto:$arr[4]'>email</a>";
	// if($arr[6]!='') {
		// if(substr($str,0,7)=="http://") {
			// $web="<a href='$arr[6]'>web</a>";
		// } else {
			// $web="<a href='http://$arr[6]'>web</a>";
		// }
	// }
	// if(strlen($sdesc)<strlen($arr[2]))
	// {
		// $sdesc.="...";
	// }
	// echo("<tr><td class='gtdname'><a href=\"group.php?id=$arr[0]\">$arr[1]</a></td><td class='gtddesc'><a href=\"group.php?id=$arr[0]\">$sdesc</a></td><td class='gtd'>$email</td><td class='gtd'>$web</td><td class='gtd'><a href='events.php?group=$arr[0]'>events</a></td></tr>");
// }

// echo('</table>');

echo("</div>");
endhtmlpage();
?>