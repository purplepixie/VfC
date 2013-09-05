<?php
require_once("functions.php");
starthtmlpage("Forgot");

?>
<h1>Forgotten Log-in Details</h1>
Enter the email address you registered with to have your login details emailed to you.
<div class="forgotbox">
<?php
	startform();
		txtbx('email','','emailofforgotten');
        echo('<br style="clear:both"/>');
		$publickey = "6Le6rAUAAAAAANUXGNnUFilRHrzkKkfIKDn9Lup9";
        echo recaptcha_get_html($publickey);
		btn('submit','email login details');
	endform();
?>


</div>

<?php
endhtmlpage();

?>