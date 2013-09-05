<?php
require_once("functions.php");
starthtmlpage("Visions - Mail");

global $uid;

$mid=1*$_GET['id'];


?>
<h2>Confirm send?</h2>

<p>Subject:

<?php
echo(getraw('emailsent','subject',$mid,$null)."</p>");
echo("<iframe name='email' src='emailbody.php?id=$mid' width='660' height='400'>");

?>
Warning: email text could not be embedded.
</iframe>

<?php
startform();
hidden('confsend',$mid,'confsend');
btn('submit','send');
endform();

endhtmlpage();
?>