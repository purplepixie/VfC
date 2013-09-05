<?php
require_once("functions.php");

$id=dbreadystr($_GET['id']);

if($id)
{
	$uname=getraw('user','uname',$id,$fail);
	$email=getraw('user','email',$id,$fail);
	$digest=getraw('user','digest',$id,$fail);
	if(!$fail)
	{
		$mypage=false;
		$pageintro="User profile for ";
		if($id==$uid) {
			$mypage=true;
			$pageintro="User options for ";
		}
		
		starthtmlpage("Visions for Change User: $uname",true);
		echo("<div><h2>$pageintro$uname</h2>");

        $imid=getraw('user','image',$id,$null);
        if($imid)
            echo('<img class="left bordered" src="photo.php?size=inquarter&amp;id='.$imid.'" alt="'.$uname.'" />');
        
        if($mypage)
        {
            // echo('Upload a picture to represent yourself(max 4Mb):');
            // echo('<div class="upload">');
            // echo('<form action="submit.php" enctype="multipart/form-data" method="post">');
            // echo('<input type="hidden" name="MAX_FILE_SIZE" value="4000000" />');
            // echo('<input name="uploadedfile" type="file" /><br />');
            // control('hidden','',$id,'','userid','');
            // control('submit','upload','upload','stdbtn','upload','');
            // echo('</form>');
            echo('</div>');
            
			if($digest=='yes') {
				$dig=true;
			} else {
				$dig=false;
			}
			
			startform();
			chbx('Receive Digest Emails?',$dig,'','digest');
			// chbx('Unsubscribe from all emails?',$noemail
			control('submit','Update','update','stdbtn','update','');
			echo('</form>');
			
			
            echo('<br />');
            // echo('<a href="notifications.php?id='.$id.'">Click here to see your messages.</a>');
            // echo('<br />');
            echo('<br />');
        }
        
        echo('</div>');
        // echo('<br style="clear:both;"/>');
		if($uid!='')
		{
			//does $uid need to leave feedback as reciever of an item offered by $id
			// $res=getres("SELECT item.id,request.id FROM request INNER JOIN item ON request.itemid=item.id WHERE tooftime=0 And item.offerer=$id And request.requester=$uid And request.accepted='yes'");
			// while($arr=mysql_fetch_array($res))
			// {
				// echo('<div class="box" >');
				// echo('<h3>Leave feedback for'.$uname.' regarding '.getraw('item','bdesc',$arr[0],$null).'</h3>');
				// startform();
				// control('hidden','',$arr[1],'','feedbacktoofreqid','');
				
				// echo('<div class="quarter control">');
				// echo('<label for="selctrl1">feedback</label><br /><span>');
				// echo('<select id="selctrl1" class="nonbutton"  name="feedback">');
				// echo('<option default value="positive">positive</option>'."\n");
				// echo('<option value="neutral">neutral</option>'."\n");
				// echo('<option value="negative">negative</option>'."\n");
				// echo('</select></span>');
				// echo('</div>');
				// control('textarea','comment','','half','comment');
				// control('submit','send','','stdbtn','','');
				// echo('</form>');
				// echo('</div>');
			// }

			//does $uid need to leave feedback as offerer of an item recieved by $id
			// $res=getres("SELECT item.id,request.id FROM request INNER JOIN item ON request.itemid=item.id WHERE toreqtime=0 And item.offerer=$uid And request.requester=$id And request.accepted='yes'");
			// while($arr=mysql_fetch_array($res))
			// {
				// echo('<div class="box" >');
				// echo('<h3>Leave feedback for '.$uname.' regarding '.getraw('item','bdesc',$arr[0],$null).'</h3>');
				// startform();
				// control('hidden','',$arr[1],'','feedbacktoreqreqid','');
				
				// echo('<div class="quarter control">');
				// echo('<label for="selctrl2">feedback</label><br /><span>');
				// echo('<select id="selctrl2" class="nonbutton"  name="feedback">');
				// echo('<option default value="positive">positive</option>'."\n");
				// echo('<option value="neutral">neutral</option>'."\n");
				// echo('<option value="negative">negative</option>'."\n");
				// echo('</select></span>');
				// echo('</div>');
				
				// echo('<div class="quarter control">');
				// echo('<label for="selctrl3">item transfered</label><br /><span>');
				// echo('<select id="selctrl3" class="nonbutton"  name="itemgone">');
				// echo('<option default value="yes">yes</option>'."\n");
				// echo('<option value="no">no</option>'."\n");
				// echo('</select></span>');
				// echo('</div>');
				
				// control('textarea','comment','','half','comment');
				// control('submit','send','','stdbtn','','');
				// echo('</form>');
				// echo('</div>');
			// }
		}

		// $now=time();


		// if($_GET['offeredall'])
		// {
			// echo('<h3>Items Offered - All</h3>');
			// echo('<form method="get" action="user.php">');
			// control('submit','hide unavailable','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			
			// echo('</form>');
			// $whereextra='';
		// }
		// else
		// {
			// echo('<h3>Items Offered</h3>');

			// echo('<form method="get" action="user.php">');
			// control('submit','show all','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			// control('hidden','',1,'','offeredall','');
			
			// echo('</form>');
			
			// $whereextra=" And (status='available' Or status='pending collection') And bbd>$now";
		// }
		// echo('<br /><br style="clear:both;"/>');

		// $res=getres("SELECT id,bdesc FROM item WHERE offerer=$id$whereextra");
		// while($arr=mysql_fetch_array($res))
		// {
			// echo('<a href="item.php?id='.$arr[0].'">'.$arr[1].'</a><br />');
		// }

		// echo('<br /><br style="clear:both;"/>');

		// if($_GET['requestedall'])
		// {
			// echo('<h3>Item Requests Made- All</h3>');
			// echo('<form method="get" action="user.php">');
			// control('submit','hide unavailable','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			
			// echo('</form>');
			// $whereextra='';
		// }
		// else
		// {
			// echo('<h3>Pending Item Requests</h3>');
			// echo('<form method="get" action="user.php">');
			// control('submit','show all','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			// control('hidden','',1,'','requestedall','');
			
			// echo('</form>');
			
			// $whereextra=" And (item.status='available' Or item.status='pending collection') And bbd>$now";
		// }

		

		// $res=getres("SELECT item.id,item.bdesc FROM item INNER JOIN request ON item.id=request.itemid WHERE request.requester=$id$whereextra");
		// while($arr=mysql_fetch_array($res))
		// {
			// echo('<a href="item.php?id='.$arr[0].'">'.$arr[1].'</a><br />');
		// }
        // echo('<br /><br style="clear:both;"/>');
        // if($_GET['wantedall'])
		// {
			// echo('<h3>Items Wanted - All</h3>');
			// echo('<form method="get" action="user.php">');
			// control('submit','hide unavailable','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			
			// echo('</form>');
			// $whereextra='';
		// }
		// else
		// {
			// echo('<h3>Items Curently Wanted</h3>');
			// echo('<form method="get" action="user.php">');
			// control('submit','show all','','stdbtn','','');
			// control('hidden','',$id,'','id','');
			// control('hidden','',1,'','wantedall','');
			
			// echo('</form>');
			
			// $whereextra=" And wanted.status='unfulfilled' And bbd>$now";
		// }


		// $res=getres("SELECT wanted.id,wanted.bdesc FROM wanted WHERE requestedby=$id$whereextra");
		// while($arr=mysql_fetch_array($res))
		// {
			// echo('<a href="wanted.php?id='.$arr[0].'">'.$arr[1].'</a><br />');
		// }
        // echo('<br /><br style="clear:both;"/>');
        
        // if($id==$uid)
        // {
            // echo('<a name="watched"> </a>');
            // echo('<h3>Watched Categories</h3>');
            // $res=getres("SELECT id,category,distance,type FROM watch WHERE user=$uid");
            // while($arr=mysql_fetch_array($res))
            // {
                // if($arr[1]==0)
                    // $catname="All";
                // else
                    // $catname=getraw('category','name',$arr[1],$null);
                // echo('<a href="watchview.php?id='.$arr[0].'">'.$catname.'</a><br />');
            // }
            
            
        
            // echo('<br /><br style="clear:both;"/>');

            // echo('<a href="changepassword.php?id='.$uid.'">change your password</a><br /><br />');
        // }
        
        
		// ob_start();
		
		// echo('<div class="box"><div class="box_title">Feedback on items offered</div>');

		// $res=getres("SELECT COUNT(tooffeedback) as num FROM request INNER JOIN item ON request.itemid=item.id WHERE tooftime!=0 And item.offerer=$id");
		// $arr=mysql_fetch_array($res);
		// $totfb=$arr[0];
		// if($totfb)
		// {
			// $res=getres("SELECT tooffeedback, COUNT(tooffeedback) as num FROM request INNER JOIN item ON request.itemid=item.id WHERE tooftime!=0 And item.offerer=$id GROUP BY tooffeedback");
			// while($arr=mysql_fetch_array($res))
			// {
				// echo($arr[0].':'.(100*($arr[1]/$totfb)).'&#37; ('.$arr[1].')<br />');
			// }
			// echo('<a href="userfeedback.php?id='.$id.'">Click here to see detailed feedback for '.$uname.'</a>');
		// }
		// else
			// echo("No feedback yet given on items offered.");
            
        // echo('</div>');
			
		// echo('<div class="box"><div class="box_title">Feedback on items requested</div>');
		// $res=getres("SELECT COUNT(toreqfeedback) as num FROM request WHERE toreqtime!=0 And requester=$id");
		// $arr=mysql_fetch_array($res);
		// $totfb=$arr['num'];
		// if($totfb)
		// {
			// $res=getres("SELECT toreqfeedback, COUNT(toreqfeedback) as num FROM request WHERE toreqtime!=0 And requester=$id GROUP BY toreqfeedback");
			// while($arr=mysql_fetch_array($res))
			// {
				// echo($arr[0].':'.(100*($arr[1]/$totfb)).'&#37; ('.$arr[1].')<br />');
			// }
			// echo('<a href="userfeedback.php?id='.$id.'">Click here to see detailed feedback for '.$uname.'</a>');
		// }
		// else
			// echo("No feedback yet given on items requested.");
        
        // echo('</div>');
        
		// echo('<div class="box"><div class="box_title">Aproximate location</div>');
		
		// location('user',$id,'small');
        // echo('</div>');
        // $ob=ob_get_contents();
        // ob_end_clean();
        
        // $reptable='user';
        // $repid=$id;

		// endhtmlpage($ob);
		endhtmlpage();
	}
	else
	{
		starthtmlpage('Noone');
		echo("Unknown user ".$id);
		endhtmlpage();
	}
}
else
{
	starthtmlpage('Noone');
	echo("Don't know which user to display.");
	endhtmlpage();
}
?>