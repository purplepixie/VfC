<?php
require_once("functions.php");

$token=mysql_real_escape_string($_GET['token']);

$userid=getraw('toactivate','uid',"token='$token'",$fail);
if($fail)
{
    starthtmlpage("Register - Activation",true);
    echo('<h2>Register</h2>');
    if($uid=='')
    {
        echo('<h1>No account activated.</h1>');
        echo('You may have already activated your account - try logging-in above.');
        echo('<br />');
        echo('<br />');
        echo('Alternatively you may have miscopied the web address from the email we sent you please check and try again.');
    }
    else
    {
        $uname=getraw('user','uname',$uid,$null);
        echo("You are logged in as $uname.");
    }

}
else
{

    $password=getraw('toactivate','password',"token='$token'",$fail);
    setraw('user','password',$userid,$password,$null);
	$yes='yes';
	setraw('user','activated',$userid,$yes,$null);
    $uname=getraw('user','uname',$userid,$null);
    getres("DELETE FROM toactivate WHERE token='$token'");
    login($uname,$password);
    starthtmlpage("Register - Activation");
    echo('<h2>Register</h2>');
    ?>
    <div>Stage 3 of 3</div>
    <h3>Your account is now active</h3>
	<?php
    //notify($uid,'joined','user.php?id='.$uid,"Welcome to ".$settings['sitename'],"We hope you enjoy using this site. Please start by setting your location on your page, if you haven't done so already.Then click the 'Items and Wanted' link on our left, choose a category and start to offer and request items!");
    echo("<br />You have been logged in as: <b>$uname</b>");

}
endhtmlpage();
?>