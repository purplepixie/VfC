<?php
require_once("functions.php");

starthtmlpage("Change Password");

echo("<h1>Change Password</h1>");

if($uid)
{
    startform();
	pswd('Old Password','oldpassword');
	pswd('New Password','replacementpassword');
	pswd('Confirm New Password','retypepassword');
    echo('<br style="clear:both"/>');
    btn('submit','Change Password');
    echo("</form>");
}
else
    echo("You must be logged-in to change your password.");
endhtmlpage();

?>