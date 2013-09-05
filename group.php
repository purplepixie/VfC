<?php
require_once("functions.php");
$now=time()-60*60;//less an hour for luck
?>

<?php

global $uid;

$gid=1*$_GET['id'];

$res=getres("SELECT id,orgname,description,contactname,publicemail,tel,website,status,creator,modby FROM org WHERE id=".$gid);

if($res!=false)
{
	$arr = mysql_fetch_array($res,MYSQL_NUM);
	$desc = $arr[1] . "'s profile at Visions for Change Norwich, a directory of radical groups and events in Norwich.";
	starthtmlpage($arr[1] . " - Group", $desc);

	$dodisp=false;
	$isadmin=false;
	if($arr[7]=='pending')
	{
		if($uid!='')
		{//logged in
			if(ismoderator())
			{
				$dodisp=true;
				$isadmin=true;
			}

			if( isgroupadmin($gid) )
			{
				$dodisp=true;
				$isadmin=true;
			}
		}

	} elseif($arr[7]=='deleted') {
		if($uid!='')
		{//logged in
			if(ismoderator())
			{
				$dodisp=true;
				$isadmin=true;
			}
		}
	}
	else
		$dodisp=true;


	if($dodisp)
	{
		if($arr[7]=='pending') {
			echo("<div class='unapproved'>This group is awaiting moderation. It will not be publicly viewable until then. 
			In the unlikely event that that does not happen in the next day or two please contact us.</div>");
		} elseif($arr[7]=="deleted") {
			echo("<div class='unapproved'>This group has been deleted and is therefore only visible by moderators.</div>");
		}
		echo("<div class='divbox gpbox2'>");
			//echo("<div class='boxtype'>Group Profile</div>");
			echo("<h2>$arr[1]</h2>");
			$stro=str_replace("\n","<br />",$arr[2]);
			echo("<div>$stro</div>");
			echo("<div class='evemail'>Email: <a href='mailto:".$arr[4]."'>$arr[4]</a></div>");
			echo("<div class='contactname'>Contact: $arr[3]</div>");
			echo("<div class='tel'>$arr[5]</div>");
			echo("<div class='gpweb'><a href='http://".$arr[6]."'>$arr[6]</a></div>");
			$res2=getres("SELECT id FROM event WHERE status = 'approved' AND start>$now AND org=$arr[0]");
			if (mysql_num_rows($res2) != 0) 
				echo("<a href='events.php?group=$arr[0]'><div class='eventsby'>Click here for $arr[1] events listings</div></a>");
		echo("</div>");	

		if(ismoderator()) {
			$creator = getraw("user", "uname", "id=$arr[8]", $null);
			$creator_email = getraw("user", "email", "id=$arr[8]", $null);
			if($arr[8]!=0) {
				echo("This group was created by: <b><a href='mailto:$creator_email' title='$creator_email'>$creator</a></b> (id=$arr[8])<br/>");
			} else {
				echo("The group creator is not known.<br/>");
			}
			if($arr[7]=='approved' and $arr[9]!=0) {
				$mod = getraw("user", "uname", "id=$arr[9]", $null);
				echo("Approved by: <b>$mod</b><br/>");
			} elseif($arr[7]=='deleted' and $arr[9]!=0) {
				$mod = getraw("user", "uname", "id=$arr[9]", $null);
				echo("Deleted by: <b>$mod</b><br/>");
			}
		}
		
		if(ismoderator() && $arr[7]!='approved')
		{
			startform();
			hidden('approvegroup',$gid);
			btn('submit','Approve');
			endform();
		}
		
		if( ismoderator() || isgroupadmin($gid) )
		{
			delete_form('group', $gid);
			
			echo("As an admin of this group or a moderator you may edit the details:");
			f_edit_group($arr);
		}
	}
	else
	{
		if($arr[7]=='pending') {
			echo("This group listing has not yet been moderated. You must be logged in as a moderator or an admin of this group to see it.");
		} elseif($arr[7]=='deleted') {
			echo("This group listing has been deleted.");
		}
	}
}
else
{
	echo("Group $gid not recognised.");
}
endhtmlpage();
?>