<?php
require_once("functions.php");
starthtmlpage("Visions");
echo('<h2>Add Organisation</h2>');
?>
<div class="">

</div>
<div class="">
<?php

global $uid;

if($uid=='')
{
	echo('you must <a href="login.php">login</a> to add a group');
}
else
{
	f_edit_group(); // defined in form_functions.php
}
echo('</div><br style="clear:both"/>');

endhtmlpage();
?>