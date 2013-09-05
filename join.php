<?php
require_once("functions.php");
starthtmlpage("Register on Visions for Change");
echo('<h2>Register</h2>');
?>
<div>Stage 1 of 3</div>
<?php
startform();
txtbx('username','','newuname','w275');

pswd('Password','newpassword');
pswd('Confirm Password','retypepassword');

txtbx('email','','email','w275');

$publickey = "6Le6rAUAAAAAANUXGNnUFilRHrzkKkfIKDn9Lup9";
echo('<br />');
echo recaptcha_get_html($publickey);
btn('submit','create account');
endform();

endhtmlpage();
?>