<?php
require_once("functions.php");
starthtmlpage("Subscription not yet complete");
?>
<h2>Subscription Not Yet Complete</h2>
<div class="">
You have been sent an email, you must follow the link in the email to complete your subscription. In the unlikely event that you do not 
	recieve an email please check that it has not been miscategorised as spam and contact <?php echo($settings['emailfrom']);?><br />
</div>
<?php
echo('<br style="clear:both"/>');

endhtmlpage();
?>