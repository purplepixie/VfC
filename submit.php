<?php
// This file is called whenever a form is filled in and submitted.  It normally called from within a
// script on the page containinmg the form (ajax) but can be posted to old style with the browser
//visiting this page. The code at the end either instucts the script or the browser to forward to the url
// $gotopage url as defined in
// the code.
//
// There are handlers for each possible form submitted. These are currently
//      Adding a User (newuname)  -  Called from join.php
//      Updating a User (updateuser)  -  Called from user_options.php
//	Unsubscribe  -  Called from user_options.php
//      Deleting a User  -  Not used at the moment
//      Editing Postcode -  Not used at the moment
//      Forgotten email (emailofforgotten)  -  Called from forgot.php
//      Picture Upload (MAX_FILE_SIZE)  -  Not used at the moment
//      Replacement Password (replacementpassword)  -  Called from changepassword.php
//      Activate Subscriber (subemail)  -  Called from session.php
//      Add or edit group (orgname)  -  Called from form_functions.php (used by addgroup.php and group.php)
//      Add or edit event (eventtitle)  -  Called from form_functions.php (used by addevent.php and event.php)
//      Delete Event, Delete Group, Approve Group, Approve Event  -  Called from form_functions.php
//      Email Content (emailcontent)  -  Called from send.php
//      Confirm Send (for emails)  -  Called from confsend.php
//      Section Title (This is for editing information on the homepage)  -  Called from editinfo.php

$formerrors='';
$gotopage=$_GET['fwd']; // This must be before declaring functions.php because it could potentially be edited

require_once("functions.php");

if($_POST['newuname']!="") {
    $privatekey = "6Lcq2aAUAAAAAEAS-tRxpEHLbKMeq3t-fvONkzu-";
    $is_valid = validate_recaptcha_2($privatekey,$_REQUEST['g-recaptcha-response']);
    if ($is_valid) {
      $formerrors.='You must correctly answer the reCAPTCHA question to show that you are not a machine.';
    }

    if ($is_valid)
    {
        $newuname=dbreadystr($_POST['newuname']);
        if($newuname!=$_POST['newuname'])
            $formerrors.='The username must not contain html special characters such as pointy brackets. <br />';
        if($newuname=='')
            $formerrors.='You must enter a username. <br />';

        if(getraw('user','id',"uname='".$newuname."'",$null)!='')
            $formerrors.='The username '.$newuname.' is already in use. <br />';

        if(strlen($_POST['newpassword'])<6)
            $formerrors.='The password must be at least 6 characters long. <br />';
        if($_POST['newpassword']!=$_POST['retypepassword'])
            $formerrors.='The password and retyped password do not match. <br />';
        //if(getraw('user','email',"uname='".mysql_real_escape_string  ($_POST['email'])."'",$null)!='')//************************************************************REENABLE AFTER TESTING****
          //  $formerrors.='An account is already registered using this email address:'.$_POST['email'].'. <br />';
        $password=mysql_real_escape_string(stripslashes($_POST['newpassword']));
        //if($password!=$_POST['newpassword'])
            //$formerrors.='Password must not contain html special characters such as pointy brackets. <br />';
        $email=mysql_real_escape_string($_POST['email']);
        if(getraw('user','id',"email='".$email."'",$null)!='') {
			$uid=getraw('user','id',"email='".$email."'",$null);
			if(getraw('user','uname',"email='".$email."'",$null)!='') {
				$formerrors.='The email '.$email.' already has an account. Go to the login page to login and click <a href="forgot.php">here</a> if you have forgotten your login details.<br />';
			}
		}
        if(!validEmail($_POST['email']))
            $formerrors.='The email address "'.htmlspecialchars($email).'" does not appear to be valid. <br />';
        //if($_POST['conds']=='')
            //$formerrors.='You must accept the conditions of use to use the site. <br />';
    }
    if($formerrors=='')
    {
        $token='';
        for($n=0;$n<9;$n++)
        {
            $token.=chr(mt_rand(65,90));
        }
		if($uid!='') {
			getres("UPDATE user SET uname='$newuname',password='$token',email='$email' WHERE id=$uid");
		} else {
			getres("INSERT INTO user (uname,password,email) VALUES ('$newuname','$token','$email')");
		}
        $nuid=getraw('user','id',"uname='".mysql_real_escape_string  (htmlspecialchars($_POST['newuname']))."'",$null);
        getres("INSERT INTO toactivate (password,token,uid) VALUES ('".$password."','$token',$nuid)");
        $subject="Activate account at ".$settings['sitename'];
        $activurl=$settings['siteURL']."activate.php?token=".$token;
        $plainmessage="To activate your account please follow the link or copy the URL below into the address bar of your browser\n".$activurl."\nIf you did not register for this account please take no action.";
        $htmlmessage="<html><body>To activate your account please follow the link below or copy the URL into the address bar of your browser\n<a href='".$activurl."'>".$activurl."</a>\nIf you did not register for an account please take no action.";
        $htmlmessage.="</body></html>";
        //if(htmlmail( $_POST['email']  ,  $subject  , $plainmessage, $htmlmessage ))
        if(mail($_POST['email'],$subject,$plainmessage,"From: ".$settings['sitename']." <noreply@".$settings['domain'].">"))
        {
            $gotopage='join2.php';
            //$formerrors.='OK.Done.';
            //echo("<h1>An email has been sent to ".$_POST['email']."</h1> Please follow the instructions in that email to activate your account. If you don't see it in a few minutes time check that it hasn't been miscategorised as spam.");
        }
        else
        {
            logerror("Create account email error to ".$_POST['email']." uid:$nuid");
            $formerrors.="Sorry. Something went wrong sending you an email to set up your account!!!???";
        }
    }
}

if($_POST['updateuser']!="") {
	if($uid!='') {
		$new_uname=$_POST['a_uname'];
		if($new_uname=='') {
			$formerrors.="You must enter a username.";
		}
		$digest='no';
		if($_POST['digest']=='on')
			$digest='yes';
		$yes='yes';

		if($formerrors=='')
		{
			setraw('user','digest',$uid,$digest,$null);
			if($digest=='yes')
				setraw('user','mail',$uid,$yes,$null);
			$formerrors.="OK - Details changed!";
		}
	} else {
		$formerrors.="You must be logged in to edit your user options.";
	}
}

if($_POST['unsubscribe']!="") {
	$no='no';
	if($uid!='') {
		setraw('user','digest',$uid,$no,$null);
		setraw('user','mail',$uid,$no,$null);
		$formerrors.="OK - Details changed!";
	} else {
		$formerrors.="You must be logged in to edit your user options.";
	}
}

if($_POST['deleteuser']!="") {
	$formerrors.="Sorry, this function is not available yet.";
}

if($_POST['postcode'] || $_POST['lat'])
{
    $lat=false;
    if($_POST['postcode'] && $_POST['geolocdone']=='no')
    {
        $ret=googleloc(stripslashes($_POST['postcode']));
        $lat=$ret['lat'];
        $long=$ret['lng'];
        $formerrors.=$ret['errors'];
    }
    else
    {
        $lat=parseLatLong(stripslashes($_POST['lat']));
        $long=parseLatLong(stripslashes($_POST['lng']));
    }
    if($lat!==false)
    {
        if($_POST['user'])
        {
            if($_POST['user']==$uid)
            {
                getres("UPDATE user SET lat=$lat , lng=$long WHERE id=$uid");
                $gotopage='user.php?id='.$uid."#location";
            }
            else
            {
                $formerrors.='Error - attempt to change other users location!';
                logerror('Error - attempt to change other users location by '.$uid);
            }
        }
        elseif($_POST['item'])
        {

            $iid=dbreadystr($_POST['item']);
            $offererid=getraw('item','offerer',$iid,$null);
            if($offererid==$uid)
            {
                $distance=getraw('item','delivery',$iid,$null);
                $degperm=0.0000089907844459429085187682625309058;
                $deliverylatd=$distance*$degperm;
                $deliverylngd=$distance*($degperm/cos(deg2rad(abs($lat))));
                $deliverylatd=abs($deliverylatd);
                $deliverylngd=abs($deliverylngd);
                getres("UPDATE item SET lat=$lat , lng=$long, deliverylatd=$deliverylatd, deliverylngd=$deliverylngd WHERE id=$iid");
                $gotopage='item.php?id='.$iid."#location";
            }
            else
                $formerrors.='Error - only the offerer of an item can change its location!';
        }
    }
    else
    {
	$formerrors.='Latitude or longitude not understood.';
    }
}

if($_POST['emailofforgotten']) {
    $privatekey = "6Le6rAUAAAAAAIcPVSzu2HPEdFOBdcwvxdlhdMTZ";
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid)
    {
      $formerrors.='You must correctly answer the reCAPTCHA question to show that you are not a machine. <br />'."(reCAPTCHA said: " . $resp->error . ")";
    }

    if ($resp->is_valid)
    {
        $fuserid=getraw('user','id',"email='".mysql_real_escape_string(stripslashes($_POST['emailofforgotten']))."'",$fail);
        if($fail)
        {
            $formerrors.="Email (".htmlspecialchars ($_POST['emailofforgotten']).") address not registered. Have you joined the mailing list but not craeted an account?";
        }
        else
        {
            $plainmessage="Your log in details are as follows:\nUsername:".getraw('user','uname',$fuserid,$fail)."\nPassword:".getraw('user','password',$fuserid,$fail);
            if(mail($_POST['emailofforgotten'],"Forgotten password - ".$settings['sitename'],$plainmessage))
            {
                $formerrors.="An email has been sent to ".htmlspecialchars($_POST['emailofforgotten'])." Please follow the instructions in that email to activate your account. If you don't see it in a few minutes time check that it hasn't been miscategorised as spam.";
            }
            else
            {
                logerror("Create account email error to ".mysql_real_escape_string(stripslashes($_POST['email']))." uid:$nuid");
                $formerrors.="Sorry. Something went wrong!!??.";
            }
        }
    }
}

if($_POST['MAX_FILE_SIZE']!='')//picture upload
{
    if($uid!='')
    {
        if($_POST['itemid'])
        {
            $iid=dbreadystr($_POST['itemid']);

            if(getraw('item','offerer',$iid,$null)==$uid)
            {
                $res=getres("SELECT COUNT(*) As num FROM image WHERE itemid=".$iid);

                $arr=mysql_fetch_array($res);
                if($arr[0]<$settings['maxphotosperitem'])
                {

                    $uploaded=file_get_contents($_FILES['uploadedfile']['tmp_name']);



                    //now resize and save it
                    $bigim=imagecreatefromstring($uploaded);

                    if($bigim)
                    {

                        getres("INSERT INTO image (type,itemid) VALUES ('jpeg','$iid')");
                        $imid=mysql_insert_id();
                        $biw=imagesx($bigim);
                        $bih=imagesy($bigim);

                        $size=Array('inwhole'=>701,'inhalf'=>328,'inquarter'=>168,'infifth'=>114,'small'=>64,'tiny'=>32);

                        foreach($size as $szname => $siw)
                        {
                            if($siw>$biw)
                                $siw=$biw;
                            $sih=(($bih*$siw)/$biw);
                            $smim=imagecreatetruecolor($siw,$sih);
                            imagecopyresampled($smim,$bigim,0,0,0,0,$siw,$sih,$biw,$bih);
                            ob_start();
                            imagejpeg($smim, NULL, 85);
                            getres("UPDATE image SET $szname=('".mysql_real_escape_string(ob_get_contents())."') WHERE id=$imid");
                            ob_end_clean();
                            imagedestroy($smim);
                        }

                        $gotopage="item.php?id=".$iid;
                    }
                    else
                    {
                        $formerrors.="Error - can't interpret the file you uploaded plaese try another image - jpeg, png and gif should work.";

                    }
                }
                else
                    $formerrors.="Error - Max pictures already uploaded for this item!";
            }
            else
                $formerrors.="Error - can't upload pictures to other peoples items!";
        }
        elseif($_POST['userid'])
        {
            $id=$_POST['userid'];
            if($id==$uid)
            {
                $uploaded=file_get_contents($_FILES['uploadedfile']['tmp_name']);



                //now resize and save it
                $bigim=imagecreatefromstring($uploaded);

                if($bigim)
                {

                    getres("INSERT INTO image (type,itemid) VALUES ('jpeg',0)");
                    $imid=mysql_insert_id();
                    $biw=imagesx($bigim);
                    $bih=imagesy($bigim);

                    $size=Array('inwhole'=>701,'inhalf'=>328,'inquarter'=>168,'infifth'=>114,'small'=>64,'tiny'=>32);

                    foreach($size as $szname => $siw)
                    {
                        if($siw>$biw)
                            $siw=$biw;
                        $sih=(($bih*$siw)/$biw);
                        $smim=imagecreatetruecolor($siw,$sih);
                        imagecopyresampled($smim,$bigim,0,0,0,0,$siw,$sih,$biw,$bih);
                        ob_start();
                        imagejpeg($smim, NULL, 85);
                        getres("UPDATE image SET $szname=('".mysql_real_escape_string(ob_get_contents())."') WHERE id=$imid");
                        ob_end_clean();
                        imagedestroy($smim);
                    }
                    setraw('user','image',$uid,$imid,$null);

                    $gotopage="user.php?id=".$uid."#picture";
                }
                else
                    $formerrors.="Error - can't interpret the file you uploaded plaese try another image - jpeg, png and gif should work.";
            }
            else
                $formerrors.="Error - you can only change your own picture.";
        }
        else
            $formerrors.="Error - don't know what to do with this picture";

    }
    else
        $formerrors.="You must be logged in to upload files.";
}

if($_POST['replacementpassword']) {
    getraw('user','uname',"id='".$uid."' And password='".mysql_real_escape_string(stripslashes($_POST['oldpassword']))."'",$fail);
    if($fail)
        $formerrors.='The old password is incorrect. <br />';


    if(strlen($_POST['replacementpassword'])<6)
        $formerrors.='The password must be at least 6 characters long. <br />';
    if($_POST['replacementpassword']!=$_POST['retypepassword'])
        $formerrors.='The password and retyped password do not match. <br />';
    $password=mysql_real_escape_string(stripslashes($_POST['replacementpassword']));
    //if($password!=$_POST['replacementpassword'])
        //$formerrors.='Password must not contain html special characters such as pointy brackets. <br />';

    if($formerrors=='')
    {
        setraw('user','password',$uid,$password,$null);
        $formerrors.="OK - Password changed!";
    }
}

if($_POST['subemail']) {
	if(!validEmail($_POST['subemail'])) {
            $formerrors.='The email address "'.htmlspecialchars($_POST['subemail']).'" does not appear to be valid. <br />';
	} else {

		$email=dbreadystrhtmlnochange($_POST['subemail']);

		if(getraw('user','id',"email='".$email."'",$null)!='')
			$formerrors.='The email '.$email.' is already registered. <br />';
		else
		{
			if(!getres("INSERT INTO user (email) VALUES ('".$email."')"))
				$formerrors.='Error - failed to add email subscriber. Please contact the webmaster.';
			else
			{
				$nid=getraw('user','id',"email='".$email."'",$null);
				if($nid=='')
					$formerrors.='Error - cant get new email id. Please contact the webmaster.';
				else
				{
					$token='';
					for($n=0;$n<20;$n++)
					{
					    $token.=chr(mt_rand(65,90));
					}
					if(!getres("INSERT INTO substoactivate (sid,token) VALUES ('".$nid."','".$token."')"))
						$formerrors.='Error - cant add subscriber to activate list. Please contact the webmaster.';
					else
					{
						$subject="Activate subscription at ".$settings['sitename'];
						$activurl=$settings['siteURL']."activatesub.php?token=".$token;
						$plainmessage="To activate your subscription please follow the link or copy the URL below into the address bar of your browser\n".$activurl."\nIf you did not register for this subscription please take no action.";
						$htmlmessage="<html><body>To activate your subscription please follow the link below or copy the URL into the address bar of your browser\n<a href='".$activurl."'>".$activurl."</a>\nIf you did not register for a subscription please take no action.";
						$htmlmessage.="</body></html>";
						//if(htmlmail( $_POST['email']  ,  $subject  , $plainmessage, $htmlmessage ))
						if(mail($email,$subject,$plainmessage,"From: ".$settings['sitename']." <noreply@".$settings['domain'].">"))
						{
						    $gotopage='subscribed.php';
						    //$formerrors.='OK.Done.';
						    //echo("<h1>An email has been sent to ".$_POST['email']."</h1> Please follow the instructions in that email to activate your account. If you don't see it in a few minutes time check that it hasn't been miscategorised as spam.");
						}
						else
						{
						    //logerror("Create account email error to ".$_POST['email']." uid:$nuid");
						    $formerrors.="Sorry. Something went wrong setting up your account!!!???";
						}

					}
				}
			}
		}
	}
}

if($_POST['orgname']) {
	if($_POST['publicemail']!='' and !validEmail($_POST['publicemail']))
            $formerrors.='The email address "'.htmlspecialchars($_POST['publicemail']).'" does not appear to be valid.<br />';
	else
	{
		$orgname=dbreadystr($_POST['orgname']);
		$orgdesc=dbreadystr($_POST['orgdesc']);
		$contactname=dbreadystr($_POST['contactname']);
		$publicemail=($_POST['publicemail']);
		$tel=dbreadystr($_POST['tel']);
		$website=dbreadystr(formaturl($_POST['website']));


		if($orgname=='')
			$formerrors.="The organisation name must not be blank (You may use your own name as a individual). <br />";
		// if($contactname=='')
			// $formerrors.="The contact name must not be blank. <br />";
		if($formerrors=='')
		{

			if($_POST['gid']!='')
			{
				$gid=$_POST['gid']*1;

				if( !isgroupadmin($gid) && !ismoderator() )
				{
					$formerrors.="You must be logged in as an admin of this group or a moderator to do that. <br />";
				}
				else
				{
					if(!getres("UPDATE org SET orgname='$orgname',description='$orgdesc',contactname='$contactname',publicemail='$publicemail',tel='$tel',website='$website' WHERE id=".$gid))
					{
						$formerrors.="Failed to add group to database. Please contact the webmaster. <br />";
					}
					else
					{
						//$gotopage='addedorg.php?gid='.getraw('org','id',"orgname='".$orgname."'",$null);
					}
				}
			}
			else
			{
				if(getraw('org','id',"orgname='".$orgname."'",$null)!='')
					$formerrors.='The name '.htmlspecialchars($orgname).' is already in use. <br />';
				else
				{
					if($uid!='')
					{
						if(!getres("INSERT INTO org (orgname,description,contactname,publicemail,tel,website,creator) VALUES ('$orgname','$orgdesc','$contactname','$publicemail','$tel','$website','$uid')"))
						{
							$formerrors.="Failed to add group to database. Please contact the webmaster. <br />";
						}
						else
						{
							$gid=getraw('org','id',"orgname='".$orgname."'",$null);

							if(!getres("INSERT INTO groupadmin (uid,gid) VALUES ($uid,$gid)"))
							{
								$formerrors.="Failed to add group to database. Please contact the webmaster. <br />";
							}
							else
								$gotopage='addedorg.php?gid='.getraw('org','id',"orgname='".$orgname."'",$null);
						}

					}
					else
						$formerrors.="You must be logged in to create a group. <br />";
				}
			}
		}
	}
}

if($_POST['eventtitle']) {
	if($uid!='')
	{

		$eventtitle=dbreadystr($_POST['eventtitle']);
		$description=dbreadystr($_POST['description']);

		$notime=false;
		$noend=false;

		if($_POST['starttimehour']=='--')
			$notime=true;

		$start=gettimestamp($_POST['startdate'],$_POST['starttimehour'],$_POST['starttimemin']);

		if($_POST['endtimehour']=='--') {
			$noend=true;
			$end=0;
		} else {
			$end=gettimestamp($_POST['startdate'],$_POST['endtimehour'],$_POST['endtimemin']);
		}


		if($start<time())
			$formerrors.='The event must start in the future.<br />';

		if($end<$start and $noend==false) {
			$formerrors.='End dates (when given) must be after start date. Select hour: "--" to indicate no end date given.<br />';
			$end=0;
		}

		if( ($_POST['eid']=='') && (getraw('event','id',"title='".$eventtitle."' And start='".$start."'",$null)!='') )
		{
			$formerrors.='An event with the same title and start time already exists.<br />';
		}

		$category=dbreadystr($_POST['category']);
		$type=dbreadystr($_POST['type']);

		if($category=='unselected')
		{
			$formerrors.='Please select a category.<br />';
		}
		if($type=='unselected')
		{
			$formerrors.='Please select an event type.<br />';
		}

		//$formerrors.="TEST:$category,$type<br />";

		$group=$_POST['group']+0;

		$price=dbreadystr($_POST['price']);
		$contactname=dbreadystr($_POST['contactname']);
		$contactemail=dbreadystr($_POST['contactemail']);
		$tel=dbreadystr($_POST['tel']);
		$website=dbreadystr(formaturl($_POST['web']));


		$book=dbreadystr($_POST['book']);
		$buildingroom=dbreadystr($_POST['buildingroom']);
		$address1=dbreadystr($_POST['address1']);
		$address2=dbreadystr($_POST['address2']);
		$towncity=dbreadystr($_POST['towncity']);
		$postcode=dbreadystr($_POST['pcode']);

		if(''!=checkPostcode($postcode) && (!checkPostcode($postcode)) )//allow emty but not invalid postcodes
			$formerrors.='The postcode "'.htmlspecialchars($_POST['postcode']).'" does not appear to be valid.<br />';

		if($_POST['contactemail']!='' and !validEmail($_POST['contactemail']))
			$formerrors.='The email address "'.htmlspecialchars($_POST['contactemail']).'" does not appear to be valid.<br />';
		if($eventtitle=='')
			$formerrors.="The event title must not be blank. <br />";

		if($formerrors=='')
		{
			if($_POST['eid']=='')
			{
				if(!getres("INSERT INTO event (title,description,start,end,org,price,contactname,contactemail,tel,web,book,buildingroom,address1,address2,towncity,postcode,creator,category,type) VALUES ('$eventtitle','$description','$start','$end','$group','$price','$contactname','$contactemail','$tel','$website','$book','$buildingroom','$address1','$address2','$towncity','$postcode','$uid','$category','$type')"))
				{
					$formerrors.="Failed to add group to database. Please contact the webmaster. <br />";
				}
				else
				{
					$eid=mysql_insert_id();
					if(!getres("INSERT INTO eventadmin (uid,eid) VALUES ($uid,$eid)"))
					{
						$formerrors.="Failed to add event to eventadmin table. Please contact the webmaster. <br />";
					}
					else
						$gotopage='event.php?eid='.$eid;
				}
			}
			else
			{
				$eid=$_POST['eid']*1;

				if( !iseventadmin($eid) && !ismoderator() )
				{
					$formerrors.="You must be logged in as an admin of this event or a moderator to do that. <br />";
				}
				else
				{
					if(!getres("UPDATE event SET title='$eventtitle',description='$description',start='$start',end='$end',org='$group',price='$price',contactname='$contactname',contactemail='$contactemail',tel='$tel',web='$website',book='$book',buildingroom='$buildingroom',address1='$address1',address2='$address2',towncity='$towncity',postcode='$postcode',category='$category',type='$type' WHERE id=".$eid))
					{
						$formerrors.="Failed to update event in database. Please contact the webmaster. <br />";
					}
					else
					{//auto refresh
						//$gotopage=
					}
				}
			}
		}
	}
	else
		$formerrors.="You must be logged in to create an event. <br />";
}

if($_POST['deleteevent']) {
	$eid=1*$_POST['deleteevent'];

	if(ismoderator() || iseventadmin($eid))
	{
		if(!getres("UPDATE event SET status='deleted',modby='$uid' WHERE id=".$eid))
		{
			$formerrors.="Failed to delete event in db. <br /> ";
		}
		if(ismoderator())
			$gotopage="admin.php";
		else
			$gotopage="event.php?eid=$eid";
	}
	else
	{
		$formerrors.="Only moderators and event admins may delete events. <br />";
	}
}

if($_POST['deletegroup']) {
	$gid=1*$_POST['deletegroup'];

	if(ismoderator() || isgroupadmin($gid))
	{
		if(!getres("UPDATE org SET status='deleted',modby='$uid' WHERE id=".$gid))
		{
			$formerrors.="Failed to delete group in db. <br /> ";
		}

		if(ismoderator())
			$gotopage="admin.php";
		else
			$gotopage="group.php?id=$gid";

	}
	else
	{
		$formerrors.="Only moderators and group admins may delete groups. <br />";
	}
}

if($_POST['approvegroup']) {
	$gid=1*$_POST['approvegroup'];

	if(ismoderator())
	{
		if(!getres("UPDATE org SET status='approved',modby='$uid' WHERE id=".$gid))
		{
			$formerrors.="Failed to approve group in db. <br /> ";
		}
		if(ismoderator())
			$gotopage="admin.php";
		else
			$gotopage="group.php?id=$gid";
	}
	else
	{
		$formerrors.="Only moderators may approve groups. <br />";
	}
}

if($_POST['approveevent'])
{
	$eid=1*$_POST['approveevent'];

	if(ismoderator())
	{
		if(!getres("UPDATE event SET status='approved',modby='$uid' WHERE id=".$eid))
		{
			$formerrors.="Failed to approve event in db. <br /> ";
		}
		if(ismoderator())
			$gotopage="admin.php";
		else
			$gotopage="event.php?eid=$eid";
	}
	else
	{
		$formerrors.="Only moderators may approve events. <br />";
	}
}

if($_POST['emailcontent']) {
	$formerrors.=emailcontent($gotopage);
}

if($_POST['confsend'])
{
	$formerrors.=confirm_send($gotopage);
}

if($_POST['sectiontitle']) {
	if(ismoderator())
	{
		$id = $_POST['id'];
		$title = mysql_real_escape_string(stripslashes($_POST['sectiontitle']));
		$short = mysql_real_escape_string(stripslashes($_POST['short']));
		$long = mysql_real_escape_string(stripslashes($_POST['long']));
		if($id==0) {
			getres("INSERT INTO about (title,short,long_text) VALUES ('$title','$short','$long')");
			$id=getraw('about','id',"title='".$title."'",$null);
		} else {
			getres("UPDATE about SET title='$title',short='$short',long_text='$long' WHERE id=$id");
		}
		$gotopage='editinfo.php?section='.$id;
	} else {
		$formerrors.="Hey, you're not allowed to edit this stuff (how the hell did you even get here??!!)";
	}
}

function formaturl($str)
{
	if(substr($str,0,7)=="http://") {
		return(substr($str,7));
	} elseif(substr($str,0,8)=="https://")
		return(substr($str,8));
	return($str);
}

//$formerrors.=$gotopage;
$formerrors.=$sessionerror;
//If this is an ajax req then return any info required otherwise need to output a propper page
if($_GET['ajax']=='y')
{//ajax req - this page is being requested from a script within another page and the response will be
//interpreted by that script.

    if($gotopage!='')
    {
        echo("Goto:$gotopage");
    }
    else
    {
        echo($formerrors);
    }

}
else
{//Support for non javascript enabled browsers - Return either html to forward the broser to $goto or an error meassage

    if($gotopage!='')
    {
	preg_match('/([^\?]*)\?*([^#]*)/',$gotopage,$m);
        //clear$_GET first?? and $_POST
        //$_POST=array();
        $_GET=array();
        parse_str($m[2],$_GET);
        require($m[1]);
    }
    else
    {
        ?><!DOCTYPE html>
        <html>
        <head>
        <title>Submit</title>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />

        </head>
        <body>
        <?php
        if($formerrors)
        {
            echo('<h1>There are some problems with the data you entered</h1>');
            echo($formerrors);
            echo("<br />Please go back and reenter the information.</body></html>");
        }

    }
}
//NEW HANDLERS SOULD GO ABOVE THIS SECTION - NOT HERE
?>
