<?php
require_once("functions.php");
starthtmlpage("Visions - Event");

global $uid;

$mid=1*$_GET['id'];


?>
<h1>Email Sent</h1>

<p>Subject:

<?php
echo(getraw('emailsent','subject',$mid,$null)."</p>");
echo("<iframe src='emailbody.php?id=$mid' width='660'>");

?>
Warning: email text could not be embedded.
</iframe>

<?php

if(getraw('emailsent','senttime',$mid,$null)==0)
{
	echo("<p>The above message does NOT apppear to have been sent.</p>");
}
else
{
	echo("<p>The above message was sent to ".getraw('emailsent','nosent',$mid,$null)." recipients at ". date(DATE_RFC822,getraw('emailsent','senttime',$mid,$null)).".</p>");
	$errs=getraw('emailsent','mailerrors',$mid,$null);
}

if($errs!='')
{
	echo ($errs);
	//echo("<p>There were errors sending the message to some recipients. An email has been sent to ian about it. Please contact him about the problem or wait to hear.</p>");
}
else
{
	echo ("<p>No sending errors were recorded.</p>");
}

endhtmlpage();
?>