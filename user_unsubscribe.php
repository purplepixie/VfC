<?php
require_once("functions.php");
$tk = md5($_GET['unsubscriber']);
if($_GET['unsubscriber']!="" and $_GET['token']==$tk) {
	$no='no';
	$un = $_GET['unsubscriber'];
	if($_GET['digest']) {
		setraw('user','digest','email=\''.$un."'",$no,$null);	
	} else {
		setraw('user','digest','email=\''.$un."'",$no,$null);
		setraw('user','mail','email=\''.$un."'",$no,$null);	
	}
	$result.="OK - Details changed!";	
	starthtmlpage("Visions for Change User Options",true);
	echo("<h2>Unsubscribe</h2>");	
	echo("<p>You have successfully unsubscribed the email address $un.</p>");
	endhtmlpage();
}

?>