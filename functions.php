<?php
// This is the core functions file

header('Content-type: text/html; charset=utf-8');

date_default_timezone_set('Europe/London');

require_once("dblink.php"); // This defines functions getres(), getraw() and setraw(), which are functions used to communicate with the database
require_once("settings.php"); // This file defines the $settings array
require_once("session.php"); // This file checks whether users are logged in and defines functions associated with logging in etc.
//require_once('recaptcha/recapatchalib.php'); // Recaptcha library for security - removed for reCAPTCHA v2 integration
require_once('recaptcha/recaptcha2.php'); // DC updated captcha for v2
require_once('form_functions.php'); // This file defines functions associated specifically with displaying forms, such as startform(), control() and others
require_once('display_functions.php'); // This file defines functions groupbox(), eventbox() for displaying information about groups and events
require_once('emailing.php'); // This file defines functions associated with emailing

function starthtmlpage($title, $desc="A directory of radical groups and events in Norwich; managed by Visions for Change - A resource for individuals and groups working for a just and sustainable future.", $opensidebar=false, $maps=false, $recaptcha=false)
{
    global $uid;
    global $settings;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?php echo $desc; ?>"/>
<meta name="keywords" content="visions for change just and sustainable world future events green equality" />
<meta name="author" content="<?php echo $settings['sitename']; ?>" />
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.0.min.js"></script>
<!-- errors as missing jQuery date pickers (see below)
<script type="text/javascript" src="pretty_fields.js"></script>
-->
<script type="text/javascript" src="rq.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/cupertino/jquery-ui.css" />
<!-- Not hosted on site or jQuery - giving 404s
  <script type="text/javascript" src="/jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
  <script type="text/javascript" src="/jquery-ui-1.10.3/ui/jquery.ui.datepicker.js"></script>
-->
<script type="text/javascript" src="collapses.js"></script>
<title>
<?php
echo($title);
?>
</title>
<?php
if($maps)
    echo('<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$settings['googlemapskey'].'&amp;sensor=false" type="text/javascript"></script>');

if ($recaptcha)
{
    echo "<script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>\n";
}
?>
<script type="text/javascript" src="fuscripts.js"></script>
</head>

<body onload="collapse_load()">
<div id="everything">
	<a href='index.php'><div id="banner">
		<div class="contact"><a href='mailto:mail@visionsforchangenorwich.org.uk'>contact</a></div>
		<h1><span><?php
			echo($settings['sitename']);
		?></span></h1>
		<span id="strap">A resource for individuals and groups working for a just and sustainable world</span>
		<img src="world.png" alt="world"/>
	</div></a>

	<div id="navigation">


	<?php
		nav("index.php","Home &amp; About");
		nav("events.php","Events");
		nav("groups.php","Groups");
		if($uid!='') {
			if(ismoderator()) {
				nav("admin.php","Admin", true);
			}
		} else {
			nav("join.php","Register", true);
			nav("login.php","Login");
		}
	?>
	</div>
	<div id="errordiv">
	</div>
	<div id="layout_body">
		<div id="sidebar">
			<div class="side">
				<?php subscribedisp(); // described in session.php ?>
			</div>

	<?php
	if(!$opensidebar) {
		echo("</div>");
	}

}

function nav($link,$lname, $last=false)
{
	$add="";
	if($last==true) {
		$add=" navend";
	}
	echo("<a class='navbox$add' href='$link'>");
	//echo("<div class='navbox'>");
	echo($lname);
	//echo("</div>");
	echo("</a>");
}

function endhtmlpage($content='')
{
    global $reptable;
    global $repid;
    global $uid;
        ?>
                    </div>
                        <?php
                        echo('<div class="box">');
                        echo($content);
                        echo('</div>');
    ?>

                <div id="errordiv2">
                </div>
                <div class="clearer">&nbsp;</div>

<div id="footer">

	<div class="credits">
	Design: Ian Barker, Chris Brock, Simeon Jackson
	</div>


	<div id="tags">
	<a href="http://validator.w3.org/check?uri=referer">
		<img src="http://www.w3.org/Icons/valid-xhtml10-blue" alt="Valid XHTML 1.0 Transitional" height="31" width="88" />
	</a>
	<a href="http://jigsaw.w3.org/css-validator/check/referer">
		<img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" lt="Valid CSS!" />
	</a>
	</div>
</div>

</div>
</body>
</html>
    <?php
}



function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
        // domain not found in DNS
        // $isValid = false;
      }
   }
   return $isValid;
}

function logerror($err)
{
    $time=time();

    getres("INSERT INTO error (message,time) VALUES ('".mysql_real_escape_string($err)."','$time')");
}

function checkPostcode (&$toCheck) {

  // Permitted letters depend upon their position in the postcode.
  $alpha1 = "[abcdefghijklmnoprstuwyz]";                          // Character 1
  $alpha2 = "[abcdefghklmnopqrstuvwxy]";                          // Character 2
  $alpha3 = "[abcdefghjkstuw]";                                   // Character 3
  $alpha4 = "[abehmnprvwxy]";                                     // Character 4
  $alpha5 = "[abdefghjlnpqrstuwxyz]";                             // Character 5

  // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
  $pcexp[0] = '^('.$alpha1.'{1}'.$alpha2.'{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$^';

  // Expression for postcodes: ANA NAA
  $pcexp[1] =  '^('.$alpha1.'{1}[0-9]{1}'.$alpha3.'{1})([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$^';

  // Expression for postcodes: AANA NAA
  $pcexp[2] =  '^('.$alpha1.'{1}'.$alpha2.'[0-9]{1}'.$alpha4.')([[:space:]]{0,})([0-9]{1}'.$alpha5.'{2})$^';

  // Exception for the special postcode GIR 0AA
  $pcexp[3] =  '^(gir)(0aa)$^';

  // Standard BFPO numbers
  $pcexp[4] = '^(bfpo)([0-9]{1,4})$^';

  // c/o BFPO numbers
  $pcexp[5] = '^(bfpo)(c\/o[0-9]{1,3})$^';

  // Overseas Territories
  $pcexp[6] = '^([a-z]{4})(1zz)$/i^';

  // Load up the string to check, converting into lowercase
  $postcode = strtolower($toCheck);

  // Assume we are not going to find a valid postcode
  $valid = false;

  // Check the string against the six types of postcodes
  foreach ($pcexp as $regexp) {

    if (preg_match ($regexp,$postcode, $matches)) {

      // Load new postcode back into the form element
		  $postcode = strtoupper ($matches[1] . ' ' . $matches [3]);

      // Take account of the special BFPO c/o format
      $postcode = preg_replace ('^C\/O^', 'c/o ', $postcode);

      // Remember that we have found that the code is valid and break from loop
      $valid = true;
      break;
    }
  }

  // Return with the reformatted valid postcode in uppercase if the postcode was
  // valid
  if ($valid){
	  $toCheck = $postcode;
		return true;
	}
	else return false;
}

function parseLatLong($latLong)
{
    $str=trim($latLong);
    if(0==preg_match('/^([0-9\.\-]+)(�*)(.*)/',$str,$matches))
        return(false);

    $degs=$matches[1];
    $rems=trim($matches[3]);

    if($matches[2]=='�')
    {
        preg_match('/^([0-9\.\-]+)(\\\'*)(.*)/',$rems,$matches);
        $minutes=$matches[1];
        $rems=trim($matches[3]);
        if($matches[2]=="'")
        {
            preg_match('/^([0-9\.\-]+)(\"*)(.*)/',$rems,$matches);
            $secs=$matches[1];
            $rems=trim($matches[3]);
        }
    }

    if($degs<0)
    {
        $sign=-1;
        $degs=-$degs;
    }
    else
        $sign=1;

    if(preg_match("/S|W|s|w/",$rems))
    {
        $sign=-1;
    }

    if(preg_match("/N|E|n|e/",$rems))
    {
        $sign=1;
    }

    $tot=$sign*($degs+$minutes/60+$secs/3600);

    return($tot);

}

function location($ofwhat,$wid,$size='full')
{
	global $uid;
	if($ofwhat=='user')
	{
		$lat=getraw('user','lat',$wid,$null);
		$lng=getraw('user','lng',$wid,$null);
	}
	elseif($ofwhat=='item')
	{
		$lat=getraw('item','lat',$wid,$null);
		$lng=getraw('item','lng',$wid,$null);
		$offererid=getraw('item','offerer',$wid,$null);
	}
    elseif($ofwhat=='wanted')
	{
		$lat=getraw('wanted','lat',$wid,$null);
		$lng=getraw('wanted','lng',$wid,$null);
		//$offererid=getraw('item','offerer',$wid,$null);
	}
    $imsize='400x300';
    if($size=='small')
        $imsize='250x200';
	echo('<div class="location box">');

	echo('<img class="left bordered" alt="google map of location" src="http://maps.google.com/staticmap?center='.$lat.','.$lng.'&amp;zoom=13&amp;size='.$imsize.'&amp;key='.$settings['googlemapskey'].'&amp;sensor=false&amp;markers='.$lat.','.$lng.',green" />');
	//echo('<br style="clear:both"/>');
	if(  ( ($ofwhat=='user')&&($wid==$uid) )  ||  ( ($ofwhat=='item')&&($offererid==$uid) )  )
	{
		?>
		<form method="post" action="submit.php" accept-charset="iso-8859-1" onsubmit="googleloc(true);return false;">
		<?php

		hidden($ofwhat,$wid);
		echo('<br style="clear:both"/>');

		txtbx('postcode or address','','postcode','w275');

		//echo('<br style="clear:both"/>');
		echo('OR');
		//echo('<br style="clear:both"/>');
		txtbx('latitude',$lat,'lat');
		txtbx('longitude',$lng,'lng');
		hidden('geolocdone','no');
		btn('submit','set location');
		endform();
        ?>
		To change the location enter something like &ldquo;My Street,My Town&rdquo; or &ldquo;My Street,Postcode&rdquo; then click set location.
		The maker should be within a few hundred meters of where you are are but it doesnt need to point to your individual house,
		in fact, it is probably best if it doesnt quite do so.
		We don't store your address, just the latitude and longitude that indicate your aproximate location.
		<?php
	}
	echo('<br style="clear:both"/>');
	echo('</div>');
}

function googleloc($address)
{
	$ret=Array();
	$ret['errors']='';

    $dbaddress=dbreadystr($address);
    $now=time();
    $res=getres("SELECT lat,lng FROM geocache WHERE address='$dbaddress' And ctime>".($now-2592000));//within last month
    if($arr=mysql_fetch_array($res))
    {
        $ret['lat']=$arr[0];
        $ret['lng']=$arr[1];
    }
    else
    {
	    $postcode=urlencode($address);
        //$str= exec('wget  --output-document=- maps.google.com/maps/geo?q='.$postcode.'&output=csv&oe=iso-8859-1&sensor=false&gl=uk&key='.$settings['googlemapskey'].'',$out);
        $str=fetch_page('maps.google.com','/maps/geo?q='.$postcode.'&output=csv&oe=iso-8859-1&sensor=false&gl=uk&key='.$settings['googlemapskey'].'');
        $out=explode("\n",$str);
        //get lat and long from postcode
        $understood=false;
        foreach($out as $o)
        {
            if(preg_match('/([0-9]+),([0-9]+),([0-9\-.]+),([0-9\-.]+)/',$o,$matches))
            {
                $understood=true;
                if($matches[1]==200)
                {
                    $ret['lat']=$matches[3];
                    $ret['lng']=$matches[4];
                    getres("INSERT INTO geocache (address,lat,lng,ctime) VALUES ('$dbaddress',".$ret['lat'].",".$ret['lng'].",$now)");
                }
                else
                {
                    if(602==$matches[1])
                        $ret['errors'].="Couldnt understand your address please set it out differently separating parts of the address with commas or enter just your postcode.";
                    else
                        $ret['errors'].="We asked google for your latitude and longitude but there was an error.<br />Google said error ".$matches[1];
                }
                break;
            }
        }
        if(!$understood)
        {
            $ret['errors'].="We asked google for your latitude and longitude but we didn't understand the response<br />Google said ";
            foreach($out as $o)
            {
                $ret['errors'].=$o;
            }
        }
	}
	return($ret);
}

function gettimefromraw($y)
{
    $y=stripslashes($y);
    if (preg_match('/^\s*(\d\d?)[^\w](\d\d?)[^\w](\d{1,4}\s*$)/', $y, $match))
    {
        $y = $match[2] . '/' . $match[1] . '/' . $match[3];
        $t=strtotime($y)+date("Z",$t);

    }
    else
    {
        $t=false;
    }
    return($t);
}

function gettimestamp($date,$hour,$minutes)
{
	$daypart=gettimefromraw($date);
	$daypart=((int)($daypart/(60*60*24)))*(60*60*24);
	return($daypart+60*60*$hour+60*$minutes);
}

function getdatefromtimestamp($ts)
{
	return(date('d/m/Y',$ts));
}

function gethourfromtimestamp($ts)
{
	$hour=(int)(($ts%(60*60*24))/(60*60));
	return($hour);
}

function getminfromtimestamp($ts)
{
	$min=(int)($ts%(60*60))/60;
	return($min);
}

function fetch_page($host,$path) {

	/* get hostname and path */

	//$path = $host['path'];


	if (empty($path)) {
		$path = "/";
	}

	/* Build HTTP 1.0 request header. Defined in RFC 1945 */
	$headers = "GET $path HTTP/1.0\r\n"
	. "User-Agent: myHttpTool/1.0\r\n\r\n";

	/* open socket connection to remote host on port 80 */
	$fp = fsockopen($host, 80, $errno, $errmsg, 30);

	if (!$fp) {
		/* ...some error handling... */
		return false;
	}

	/* send request headers */
	fwrite($fp, $headers);

	/* read response */
	while(!feof($fp)) {
		$resp .= fgets($fp, 4096);
	}

	fclose($fp);

	/* separate header and body */
	$neck = strpos($resp, "\r\n\r\n");
	$head = substr($resp, 0, $neck);
	$body = substr($resp, $neck+4);

	/* omit parsing response headers */

	/* return page contents */

	return($body);
}

function formatdate($timestamp)
{
    $now=time();
    if($timestamp<$now)
    {
        $str=date('d F Y',$timestamp);
    }
    else
    {
        if(($now+604800)>$timestamp)//1 week
            $str=date('l jS F Y',$timestamp);
        else
            $str=date('l jS F Y',$timestamp);
    }
    return($str);
}

function housekeeping()
{//once a day or similar

	$now=time();
	$last=getraw('site','lasthousekeeping ',1,$null);

	if(($last+86400)<$now)//1 day
	{
		setraw('site','lasthousekeeping',1,$next,$null);
	}

}


function truncateString($intLength = 0, $strText = "") {
    if ($intLength == 0) {
        return $strText;
    }

    if(strlen($strText) > $intLength) {
        $strNewText=substr($strText,0,$intLength);
        return ($strNewText . "...");
    }
    else {
        return $strText;
    }
}

// function formatdistance($distinm)
// {
    // if($distinm<804)
        // return($distinm.' meters');
    // elseif($distinm<1000)
        // return('about half a mile');
    // elseif($distinm<(11263))//7miles
        // return('about '.round($distinm/1609).' miles');
    // else
        // return(round($distinm/1609).' miles');
// }

// function microtime_float()
// {
    // list($utime, $time) = explode(" ", microtime());
    // return ((float)$utime + (float)$time);
// }

function ismoderator()
{
	global $uid;
	if($uid!='')
	{
		if('yes'==getraw('user','moderator',"id=$uid",$fail))
		{
			return(true);
		}
	}
	return(false);
}

function isgroupadmin($gid)
{
	global $uid;
	if($uid!='')
	{
		return( ($gid!='') && ($gid==getraw("groupadmin","gid","uid=$uid And gid=".$gid,$null)) );
	}

	return(false);
}

function iseventadmin($eid)
{
	global $uid;
	if($uid!='')
	{
		return( ($eid!='') && ($eid==getraw("eventadmin","eid","uid=$uid And eid=".$eid,$null)) );
	}

	return(false);
}

function br2nl($text)
{
    return  preg_replace('/<br\\s*?\/??>/i', chr(10), $text);
}

function br2sp($text)
{
    return  preg_replace('/<br\\s*?\/??>/i', " ", $text);
}
?>
