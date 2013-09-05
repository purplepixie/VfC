<?php
require_once("functions.php");
starthtmlpage("Visions - Send Email");
global $uid;



if($uid && ismoderator())
{

	if($_GET['gendigest']=='yes')
	{
		$subj='Visions for Change: Upcoming Events Digest';
		$now=time();
		$fornight=$now+(14*24*60*60);
		// $cont='<h2>Visions for Change - Events Digest</h2><p>Events in the next 14 days</p>';
		$cont.=generate_digest("WHERE status='approved' And start>$now And start<$fornight"); // Defined in emailing.php
		$glink='';
	}
	else
	{
		$cont='';
		$subj='Visions For Change';
		$glink="<a href='send.php?gendigest=yes'>Auto generate the event digest.</a>";
	}

	?>
	<h2>Send an email to the whole mailing list</h2>
	<div>
	Enter the text of the email below.

	<?php

	startform();
	txtbx('Email Subject',$subj,'emailsubject');
	txta('Email Content',$cont,'emailcontent');
	btn('submit','submit');
	chbx(' Send email to mods only','','stdin','justmods');
	endform();
	echo("</div>");

	echo($glink);

}
else
	echo("You must be logged in as a moderator to access this page");


endhtmlpage();
?>