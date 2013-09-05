<?php

function htmlmail( $to  ,  $subject  ,  $plainmessage,   $htmlmessage  ,  $additional_headers='',  $additional_parameters ='')
{

    global $settings;
	$from = $settings['sitename'].' <'.$settings['emailfrom'].'>';
	$headers = $additional_headers;
	$headers .= "From: " . $from  . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    // $headers .= "boundary=" . $mime_boundary_header;"
    
    // $semi_rand = md5(time());
    // $mime_boundary = "==MULTIPART_BOUNDARY_$semi_rand";
    // $mime_boundary_header = chr(34) . $mime_boundary . chr(34);

   

    // $body = "
// --$mime_boundary
// Content-Type: text/plain; charset=us-ascii
// Content-Transfer-Encoding: 7bit

// $plainmessage

// --$mime_boundary
// Content-Type: text/html; charset=us-ascii
// Content-Transfer-Encoding: 7bit

// $htmlmessage

// --$mime_boundary--";
	
	if ($sending = @mail($to, $subject, $htmlmessage,$headers,$additional_headers))
		return $sending;
	else
		return false;
}

function emailcontent(&$gotopage) { // this is called from submit.php
	global $uid;
	global $settings;
	
	if(ismoderator()) {
		if($_POST['emailsubject']=='') {
			return "You must enter a subject. <br />";
		}
		else
		{
			$content=full_email($_POST[emailsubject],$_POST['emailcontent']);
			
			$content=str_replace("\n","\n<br />",$content);
			$content=str_replace("'","^qu^",$content);
			$content=str_replace('"',"^dqu^",$content);
			
			$subject=mysql_real_escape_string($_POST['emailsubject']);
			$jm=$_POST['justmods'];
			if($jm!='') {
				if(!getres("INSERT INTO emailsent (sender,subject,body,senttime,justmods) VALUES ('$uid','$subject','$content',0,'$jm')"))
				{
					return "Failed to add email to database. Please contact the webmaster. <br />";
				}
				else
				{
					$mid=mysql_insert_id();
					
					$gotopage='confsend.php?id='.$mid;
					
					return;
				}
			} else {
				if(!getres("INSERT INTO emailsent (sender,subject,body,senttime) VALUES ('$uid','$subject','$content',0)"))
				{
					return "Failed to add email to database. Please contact the webmaster. <br />";
				}
				else
				{
					$mid=mysql_insert_id();
					
					$gotopage='confsend.php?id='.$mid;
					
					return;
				}			
			}
		}
	}
	else
		return "You must be logged in as a moderator to do that. <br />";
		
}

function confirm_send(&$gotopage) {
	if(ismoderator()) {
		$mid=mysql_real_escape_string($_POST['confsend']);
		$mid=1*$mid;
		
		$res=getres("SELECT  sender,subject,body,senttime,justmods FROM emailsent WHERE id=$mid");
		
		if(!$res)
		{
			return "Failed to find email. Please contact the webmaster. <br />";
		}
		else
		{
			
			 $arr=mysql_fetch_array($res,MYSQL_NUM);
			 if(!$arr)
			 {
				return "Failed to fetch email. Please contact the webmaster. <br />";
			 }
			 else
			 {
			 
				$senttime=$arr[3];
				$subject=$arr[1];
				$content=$arr[2];
				$content=str_replace("^qu^","'",$content);
				$content=str_replace('^dqu^','"',$content);
				if($arr[4]=='on')
					$wheremod=" AND moderator='yes'";
				
				if(substr($content,0,6)!='<html>')
					$content=nl2br($content);
				
				if($senttime!=0)
				{
					return "This email has already been sent. <br />";
				}
				else
				{
					$csent=0;
					$merrors='';
					
					$res2=getres("SELECT email FROM user WHERE activated='yes' AND mail='yes'$wheremod;");

					while($arr=mysql_fetch_array($res2,MYSQL_NUM))
					{
						$ccontent = str_replace("%email%", $arr[0], $content);
						$ccontent = str_replace("%token%", md5($arr[0]), $ccontent);
						$result=htmlmail($arr[0],$subject,strip_tags($ccontent),$ccontent);
						if($result)
						{
							$csent++;
							$ctime=time();
							getres("UPDATE emailsent SET senttime=$ctime,nosent='$csent',mailerrors='$merrors' WHERE id=$mid");
						}
						else
						{
							$merrors.="Failed to send email to ". $arr[0] . "<br />\n";
							
						}
					}
					
					
					if($merrors!='') {
						return "Errors occured sending email: <br/>$merrors";
						// mail("ian@aircurrents.co.uk","Visions mail error","mailsent $mid");
					}
						
					$gotopage='sent.php?id='.$mid;
				}
			}
		}
	}
	else
		return "You must be logged in as a moderator to do that. <br />";
}

function generate_digest($where) {
	global $settings;
	$now=time();

	// getres("INSERT INTO log (type,event,variable) VALUES ('LOG','startingemailsend','$now')");
	$res=getres("SELECT id FROM event $where ORDER BY start ASC");
	
	while($arr=mysql_fetch_array($res,MYSQL_NUM))
	{
		$arr=event_data($arr[0]);
		$cont.=reventbox($arr);
	}
	
	return $cont;
}

function full_email($subj, $cont, $dig=false) {
	global $settings;
	
	$content="<html><head><title>$subj</title></head><body>\n\r";
	if($dig) {
		$digest_uns='Visions digest emails <a href="'.$settings['siteURL'].'user_unsubscribe.php?unsubscriber=%email%&token=%token%&digest=true">here</a> or from ';
		$content.="<h2 style='font-size:1.6em;  	color:black;  	margin: 10px 0px 20px 0px;'>Visions for Change - Events Digest</h2>\n\r<p><i>Here's your regular update of events in the next 14 days</i></p>\n\r";	
	}
	$content.=$cont;
	$content.='<p>You can unsubscribe from '.$digest_uns.'all Visions for Change emails by clicking <a href="'.$settings['siteURL'].'user_unsubscribe.php?unsubscriber=%email%&token=%token%">here</a>.</p>'."\n\r</body>\n\r</html>";
	

	
	return $content;
}

?>