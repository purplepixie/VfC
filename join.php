<?php
require_once("functions.php");
starthtmlpage("Register on Visions for Change","Join Visions for Change",false,false,true);
echo('<h2>Register</h2>');
?>
<div>Stage 1 of 3</div>
<?php
startform();
txtbx('username','','newuname','w275');

pswd('Password','newpassword');
pswd('Confirm Password','retypepassword');

txtbx('email','','email','w275');

$publickey = "6Lcq2aAUAAAAAAQItwf-RoKmuSexATqF-2M77X43";
echo('<br />');
echo "<div class=\"g-recaptcha\" data-sitekey=\"".$publickey."\"></div>\n";
btn('submit','create account');
endform();

endhtmlpage();
?>
