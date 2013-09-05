<?php
//This file should be included in every page.
require_once("dblink.php");


//might be posting uname and pword to get authed
//or just checkingthe sid to get uid
$sessionerror='';

if($_POST['logout'])
{
    getres("DELETE FROM session WHERE sid='".mysql_real_escape_string($_COOKIE['sid'])."'");
    setcookie('sid','',0,'/');
    $uid='';
}
else
{

    if($_POST['uname']!='')
    { //login attempt
        login($_POST['uname'],$_POST['password']);
    }
    else
    {
        if($_COOKIE['sid'])//has a cookie need to get uid from sid
        {
        getres("SELECT 1");//ensure there's a connection
        $expires=getraw('session','expires',"sid='".mysql_real_escape_string($_COOKIE['sid'])."'",$fail);
        if($fail)
        {//non-existant session;
            $sessionerror='nonexistant session'.mysql_real_escape_string($_COOKIE['sid']);
            $uid='';
        }
        if(time()>$expires)
        {//session timed-out
            $sessionerror='session timed-out';
            $uid='';
        }
        else
        {
            $sessionerror='';
            $uid=getraw('session','uid',"sid='".mysql_real_escape_string($_COOKIE['sid'])."'",$null);
            //refresh session
            $expires=time()+3600;//1 hour session.
            setraw('session','expires',"sid='".mysql_real_escape_string($_COOKIE['sid'])."'",$expires,$null);
        }
        
        }
    }
}

//$uid=21;

function login($uname,$password)
{
    global $uid;
    global $sessionerror;
	global $gotopage;
	
    //if they have a sid cookie end that session
    if($_COOKIE['sid'])
    {
        getres("DELETE FROM session WHERE sid='".mysql_real_escape_string($_COOKIE['sid'])."'");
        setcookie('sid','',0,'/');
    }
    $uid=getraw('user','id',"blocked='no' And uname='".mysql_real_escape_string(stripslashes($uname))."' And password='".mysql_real_escape_string(stripslashes($password))."'",$fail);
    if(!$fail)
    {
        $sid='';
        for($n=0;$n<20;$n++)
        {
            $sid.=chr(mt_rand(65,90));
        }
        $tnow=time();
        $expires=$tnow+3600;//1 hour session.
        getres("INSERT INTO session (sid,expires,uid) VALUES ('$sid','$expires','$uid')");
        setcookie  ( 'sid', $sid, 0,'/');
		$gotopage=$_GET['fwd'];
		
        //do some housekeeping
        getres("DELETE FROM session WHERE expires < $tnow");
    }
    else
    {
        $blocked=getraw('user','blocked',"uname='".mysql_real_escape_string(stripslashes($uname))."' And password='".mysql_real_escape_string(stripslashes($password))."'",$fail);
        if('yes'===$blocked)
            $sessionerror="Account blocked.";
        else
            $sessionerror='Invalid username password combination.';
    }
}

function logindisp()
{
    global $uid;
    global $sessionerror;
    $arrStr = explode("/", $_SERVER['SCRIPT_FILENAME']);
    $arrStr = array_reverse($arrStr );

    if($arrStr[0]!='join.php')
    {
        if($uid=='')
        {
            ?>
            
            <?php
            echo($sessionerror);
            startform();
			txtbx('Username','','uname','w275');
            pswd('Password','password');
			btn('submit','Log In');
			endform();
            ?>
            <a href="join.php">register as new user</a> | <a href="forgot.php">forgotten login</a>
            <div class="content_separator"/></div>
            <?php
        }
        else
        {
            $uname=getraw('user','uname',$uid,$null);
			startform();
			echo('<h3>User</h3>');
            echo('<div>You are logged in as <b>'.$uname.'</b>.</div>');
			echo('<div><a href="user_options.php">User Options</a></div>');
            hidden('logout','logout');
            btn('submit','log out');
			endform();
        }
    }
}

function subscribedisp()
{
global $uid;
?>
<div class="subs">
	<?php
            //echo($sessionerror);
	    
	    if($uid!='')
	    {
			logindisp();
	    }
	    else
	    {
			echo("<h3>Subscribe</h3>");
		    startform();
		    txtbx('Email Address','','subemail','w100pc');
		    btn('submit','subscribe');
		    ?>
		    </form>
		    <?php
	    }
	    ?>
	    
</div>
<?php
/*
<ul>
	    <li>We'll never pass your details to anyone else</li>
	    <li>Unsubcribe whenever you like</li>
	    <li>One email per week by default</li>
	    </ul>
*/

}

?>