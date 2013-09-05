<?php
require_once("functions.php");

$token=mysql_real_escape_string($_GET['token']);

$userid=getraw('substoactivate','sid',"token='$token'",$fail);
if($fail)
{
	starthtmlpage("Activate",true);
	echo('<h2>No subscription activated!</h2>');
	echo('You may have miscopied the web address from the email we sent you please check and try again.');

}
else
{
    getres("UPDATE user SET  activated='yes' WHERE id='$userid'");
	getres("DELETE FROM $database.substoactivate WHERE substoactivate.sid='$userid'");
	

    starthtmlpage("Activate");
    ?>
    <h2>Your subscription is now active.</h2>
    <p> Once a week you will recieve a digest of events booked for the coming 2 weeks. We hope this service is useful to you but should you wish to unsubscribe at any time simply follow the link at the bottom of any email we send to you.</p>
    <p> If you would like to contribute events or publicise a group on this site you will also need to <a href='join.php'>create an account</a>.</p>
    <?php
    
}
endhtmlpage();
?>