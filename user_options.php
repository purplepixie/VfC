<?php
require_once("functions.php");
global $uid;
if($uid) {
	$uname=getraw('user','uname',$uid,$fail);
	$email=getraw('user','email',$uid,$fail);
	$digest=getraw('user','digest',$uid,$fail);
	$mail=getraw('user','mail',$uid,$fail);

	starthtmlpage("Visions for Change User: $uname",true);
	echo("<h2>User Options: $uname</h2>");
	?>
	<p>Please select your user options or press Unsubscribe to remove yourself from our mailing list.</p>
	<?php

	startform();
	txtbx('Username: ',$uname,'a_uname');
	echo("<p>Email address: $email</p>");
	chbx(' Receive digest emails',$digest,'digest','digest');
	hidden('updateuser','updateuser');
	btn('submit','Update User');
	endform();

	startform();
	hidden('unsubscribe',$uid);
	btn('submit','Unsubscribe',"onclick='confirm(\"Sure?\")'");
	endform();
	
	echo("<h3>Change Password</h3>");
	startform();
	pswd('Old Password','oldpassword');
	pswd('New Password','replacementpassword');
	pswd('Confirm New Password','retypepassword');
	echo('<br style="clear:both"/>');
	btn('submit','Change Password');
	endform();
	
	// delete_form('user',$uid);

	endhtmlpage();
} else {
	starthtmlpage("Visions for Change User Options",true);
	echo("<h2>User Options</h2>");	
	echo("<p>You must be logged in to change your user options.</p>");
	endhtmlpage();
	
}

?>