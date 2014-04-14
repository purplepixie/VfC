<?php

function event_data($eid) {
	$res=getres("SELECT id,title,description,start,end,org,price,contactname,contactemail,tel,web,book,buildingroom,address1,address2,towncity,postcode,status,creator,modby,category,type FROM event WHERE id=".$eid);
	$arr=mysql_fetch_array($res,MYSQL_NUM);

	$data[id]=$arr[0];
	$data[title]=$arr[1];
	$data[description]=$arr[2];
	
	$data[start]=$arr[3];
	$data[startdate]=formatdate($arr[3]);
	$data[startdatecal]=getdatefromtimestamp($arr[3]);
	$data[hour]=gethourfromtimestamp($arr[3]);
	$data[min]=getminfromtimestamp($arr[3]);
	if($data[min]==0)
		$data[min]="00";
	elseif(strlen($data[min]<2))
		$data[min]="0".$data[min];

	$data[end]=$arr[4];
	$data[enddate]=formatdate($arr[4]);
	$data[enddatecal]=getdatefromtimestamp($arr[4]);
	$data[endhour]=gethourfromtimestamp($arr[4]);
	$data[endmin]=getminfromtimestamp($arr[4]);
	if($data[endmin]==0)
		$data[endmin]="00";
	elseif(strlen($data[endmin]<2))
		$data[endmin]="0".$data[endmin];
	
	$data[groupid]=$arr[5];
	if($data[groupid]!=0) {
		$data[group]=getraw('org','orgname',$data[groupid],$null);
		$data[glink]="<a href='group.php?id=$data[groupid]'>$data[group]</a>";
	} else {
		$data[group]="";
		$data[glink]="";
	}
	
	$data[price]=$arr[6];
	if($data[price]=='')
		$data[price]='Free';
	$data[contactname]=$arr[7];
	$data[contactemail]=$arr[8];
	$data[tel]=$arr[9];
	
	$data[website]=$arr[10];
	
	
	$data[book]=$arr[11];
	
	if($data[book]=='yes')
		$data[bookstring]='Please book in advance';
	if($data[book]=='no')
		$data[bookstring]='Booking not needed';
	if($data[book]=='optional')
		$data[bookstring]='Booking optional';
	$data[buildingroom]=$arr[12];
	$data[address1]=$arr[13];
	$data[address2]=$arr[14];
	$data[towncity]=$arr[15];
	$data[postcode]=$arr[16];
	$data[status]=$arr[17];
	$data[creator]=$arr[18];
	$data[modby]=$arr[19];
	$data[location]=$arr[20];
	$data[category]=$arr[20];
	$data[type]=$arr[21];
	
	return $data;
	
}

function eventbox($event, $full=false)
{
	// £event is an array with keys id,title,description,startdate,startdatecal,hour,min,end,groupid,group,glink,price,contactname,contactemail,tel,website,book,bookstring,buildingroom,address1,address2,towncity,postcode,status
	$sdesc=substr(str_replace(array("\r", "\r\n", "\n", "<br />", "<br>"), ' ', $event[description]),0,215);
	if(strlen($sdesc)<strlen(str_replace(array("\r", "\r\n", "\n", "<br />"), ' ', $event[description])))
	{
		$sdesc.="...";
	}
	
	echo("<div class='ev'>");
		// echo("<a href=\"event.php?eid=$arr[0]\">");
		// echo("<div class='evimg'>");
			
			// echo("");
			
		// echo("</div>");
		// echo("</a>");
		if($full) {
			echo("<h3>$event[title]</h3>");
			
			$end_string = '';
			$from = '';
			if($event[end]!=0) {
				if($event[startdate] == $event[enddate])
					$end_string = " till $event[endhour]:$event[endmin]";
				else {
					$from = "From: ";
					$end_string = "<br/>Until: $event[enddate] - $event[endhour]:$event[endmin]";
				}
			}
			echo("<div class='dtime'>$from $event[startdate] - $event[hour]:$event[min]</div>");
			
			
			
			if($event[group]!='') 
				echo("<p class='group'>Event organised by $event[glink]</p>");
				
			$description=str_replace("\n","<br />",$event[description]);	
			echo("<p class='description'>$description</p>");
			
			if($event[website]!='') 		
				echo("<div class='evweb'>Event/organisation website: <a href='http://".$event[website]."'>$event[website]</a></div>");	
			echo("<div class='price'>$event[price] - $event[bookstring]</div>");
			
			if($event[postcode]!='')
				echo("<a href='http://maps.google.co.uk?q=$event[buildingroom], $event[postcode]' class='map'>Click here to view venue in Google Maps</a>");
			echo("<div class='address'><div>$event[buildingroom]</div>");
			echo("<div>$event[address1]</div>");
			echo("<div>$event[address2]</div>");
			echo("<div>$event[towncity]</div>");
			echo("<div>$event[postcode]</div>");
			echo("</div>");
			
			echo("<div class='typecat'><span class='typecattitle'>Category:</span>$event[category]<br /><span class='typecattitle'>Type:</span>$event[type] </div>");
			
			echo("Contact: <span class='contactname'>$event[contactname]</span>");
			if($event[tel]!='')
				echo("Tel:<div class='tel'>$event[tel]</div>");
			if($event[contactemail]!='')			
				echo("<div class='evemail'>Email: <a href='mailto:".$event[contactemail]."'>$event[contactemail]</a></div>");
	
		} else {
			echo("<div class='evtitle' title='Click for more details'>");
			//echo("<a href=\"event.php?eid=$event[id]\">");
			echo("$event[title]");
			//echo("</a>");
			echo("</div>");
			echo("<div class='evdesc'>$sdesc</div>");
			echo("<div class='evdate'>");
			echo("$event[startdate] at $event[hour]:$event[min]");
			echo("</div>");	
			echo("<div class='evgroup'>");
			echo($event[group]);
			if($event[group]!='' or $event[buildingroom]!='')
				echo(' - ');
			echo("<span class='evloc'>$event[buildingroom]</span>");
			echo("</div>");			
		}
	echo("</div>");
}

function reventbox($event)
{
	global $settings;
	
	// $event is an array with keys id,title,description,startdate,startdatecal,hour,min,end,groupid,group,glink,price,contactname,contactemail,tel,website,book,bookstring,buildingroom,address1,address2,towncity,postcode,status
	$sdesc=substr(str_replace(array("\r", "\r\n", "\n", "<br />", "<br>"), ' ', $event[description]),0,215);
	if(strlen($sdesc)<strlen(str_replace(array("\r", "\r\n", "\n", "<br />"), ' ', $event[description])))
	{
		$sdesc.="...";
	}
	
	$cont="<div style='margin: 8px 0px 8px 0px;border:1px solid #6d90ae;padding: 10px;overflow:auto;width: 650px;background-color: #FBFDFF;'>\n\r";
		$cont.="<div style='margin:5px 5px 5px 0px;font-weight:bold;color:black;' title='Click for more details'>\n\r";
		$cont.="<a style='text-decoration:none;color: #055cf7; color:black;' href=\"$settings[siteURL]event.php?eid=$event[id]\">";
		$cont.="$event[title]";
		$cont.="</a>\n\r";
		$cont.="</div>\n\r";
		$cont.="<div style='font-size: 10pt;'>$sdesc</div>\n\r";
		$cont.="<div style='margin:2px 2px 2px 0px;font-size:0.9em;font-weight:bold;color:#055cf7;'>";
		if ($event[hour]=="00" and $event[min]=="00") {
			$cont.="$event[startdate] (all day event)";
		} else {
			$cont.="$event[startdate] at $event[hour]:$event[min]";
		}
		$cont.="</div>\n\r";	
		$cont.="<div style='margin:2px 2px 2px 0px;font-size:0.8em;font-style:italic;color:#055cf7;'>";
		$cont.=$event[group];
		if($event[group]!='')
			$cont.=' - ';
		$cont.="<span style='font-style:normal;'>$event[buildingroom]</span>";
		$cont.="</div>\n\r";			
	$cont.="</div>\n\r";
	return $cont;
}

function groupbox($group, $full=false) {
	$sdesc=substr($group[2],0,50);
	$email='';
	$web='';
	if($group[4]!='')
		$email="<a href='mailto:$group[4]'>email</a>";
	if($group[6]!='') {
		if(substr($str,0,7)=="http://") {
			$web="<a href='$group[6]'>website</a>";
		} else {
			$web="<a href='http://$group[6]'>website</a>";
		}
	}
	if(strlen($sdesc)<strlen($group[2]))
	{
		$sdesc.="...";
	}
	
	echo("<div class='gpbox'>");

		echo("<div class='gptitle'>$group[1]</div>");
		echo("<div class='gpdesc'>$sdesc</div>");
	echo("</div>");

}

?>