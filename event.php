<?php
require_once("functions.php");

global $uid;
$eid=1*$_GET['eid'];
$res=getres("SELECT title FROM event WHERE id=".$eid);

if($res!=false) {
	
	$event = event_data($eid);
	
	$desc = "$event[startdate] at $event[hour]:$event[min] at $event[buildingroom]: " . substr(br2sp($event[description]), 0, 100);
	starthtmlpage($event[title] . " - Event", $desc);
	
	$status = $event[status];
	
	$dodisp=false;
	$isadmin=false;
	
	switch ($status) {
	case 'approved':
		$dodisp=true;
		break;

	case 'pending':
		if($uid!='')
		{//logged in
			if(ismoderator())
			{
				$dodisp=true;
				$isadmin=true;
			}

			if( iseventadmin($eid) )
			{
				$dodisp=true;
				$isadmin=true;
			}
		}
		break;
	case 'deleted':
		if(ismoderator())
			{
				$dodisp=true;
				$isadmin=true;
			}
		break;	
	}

	if($dodisp) {
	
		if($status=='pending') {
			echo("<div class='unapproved'>This event is awaiting moderation. It will not be publicly viewable until then. 
			In the unlikely event that that does not happen in the next day or two please contact us.</div>");
		} elseif($status=="deleted") {
			echo("<div class='unapproved'>This event has been deleted. The event is only visible by moderators.</div>");
		} 
		
		eventbox($event, true);
		
		if(ismoderator()) {
			$creator = getraw("user", "uname", "id=$event[creator]", $null);
			$creator_email = getraw("user", "email", "id=$event[creator]", $null);
			if($event[creator]!=0) {
				echo("Created by: <b><a href='mailto:$creator_email' title='$creator_email'>$creator</a></b> (id=$event[creator])<br/>");
			} else {
				echo("The event creator has not been recorded.<br/>");
			}
			
			if($status=='approved' and $event[modby]!=0) {
				$mod = getraw("user", "uname", "id=$event[modby]", $null);
				echo("Approved by: <b>$mod</b><br/>");
			} elseif($status=='deleted' and $event[modby]!=0) {
				$mod = getraw("user", "uname", "id=$event[modby]", $null);
				echo("Deleted by: <b>$mod</b><br/>");
			}
		}
		
		if( ismoderator() && $status!='approved')
		{
			startform();
				hidden('approveevent',$eid);
				btn('submit','Approve');
			endform();
		}
		
		if( ismoderator() || iseventadmin($eid) )
		{
			
			delete_form('event', $eid);
			
			$now=time()-24*60*60;//less a day for luck
			if($event[start]>$now) {
				
				echo("As an admin of this event or a moderator you may edit the details:");
				
				f_edit_event($event);

			} else {
				echo('You may not edit events in the past');
			}
		}
	} else {
		if($status=='pending') {
			echo("This event listing has not yet been moderated. You must be logged in as a moderator or an admin of this event to see it.");
		} elseif($status=='deleted') {
			echo("This event listing has been deleted.");
		}
	}
}
else
{
	starthtmlpage("Visions - Event Not Recognised");
	echo("Event $eid not recognised.");
}
endhtmlpage();
?>