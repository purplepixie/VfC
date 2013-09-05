<?php
require_once("functions.php");
starthtmlpage("Visions");
echo('<h2>Add Event</h2>');


global $uid;

	if($uid=='')
	{
		echo('you must <a href="login.php">login</a> to create an event');
	}
	else
	{
		f_edit_event();
	}
echo('<br style="clear:both"/>');

endhtmlpage();
?>

