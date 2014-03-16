<?php

require_once("functions.php");
starthtmlpage("Visions - Events", "A directory of radical groups and events in Norwich; managed by Visions for Change - Individuals and groups working for a just and sustainable world.", true);

$filterstr="events.php?t=1";

$now=time()-60*60;//less an hour for luck

$fgroup=1*$_GET['group'];
$fwhen=$_GET['when'];
$fwhere=$_GET['where'];
$fcat=$_GET['cat'];
$ftype=$_GET['type'];


$simplewhere="WHERE status='approved' And start>$now";
$where=$simplewhere;

$title="";

if($fgroup!=0)
{
	$gname=getraw('org','orgname',$fgroup,$null);
	if($gname=='')
	{
		
	}
	else
	{
		$where.= " And org=$fgroup";
		$title.= " organised by ".$gname;
	}
}

$endtoday=((24*60*60)*(int)(time()/(24*60*60))+(24*60*60));
if($fwhen=='today')
{
	
	$where.= " And start<$endtoday";
	$title.= " today";
}

if($fwhen=='tomorrow')
{
	$where.= " And start<$endtoday And start<";
	$where.=$endtoday+(24*60*60);
	$title.= " tomorrow";
}

if($fwhen=='next7')
{
	$where.= " And start<";
	$where.=$endtoday+(7*24*60*60);
	$title.= " in the next 7 days";
}

if($fwhere!='')
{
	$fwhere=dbreadystr($fwhere);
	$where.= " And buildingroom='$fwhere'";
	$title.= " in $fwhere";
}

if($fcat!='')
{
	$fwhere=dbreadystr($fcat);
	$where.= " And category='$fcat'";
	$title.= " in $fcat";
}

if($ftype!='')
{
	$fwhere=dbreadystr($ftype);
	$where.= " And type='$ftype'";
	$title.= " of type $ftype";
}


?>
<div class='side'>

<h3>Filters</h3>
<?php

	
	
		if($where!=$simplewhere)
		{
			echo("<div>Remove Filters:</div>");
			echo("<ul><li class='fillink'><a href='events.php'>Show All</a></li></ul>");
		}
		?>
		<div>Starting:</div>
		<ul>
		<?php
		echo("<li class='fillink'><a href='$filterstr&when=today'>Today</a></li>");
		echo("<li class='fillink'><a href='$filterstr&when=tomorrow'>Tomorrow</a></li>");
		echo("<li class='fillink'><a href='$filterstr&when=next7'>In the next 7 days</a></li>");
		echo("</ul>Where:<ul>");
		$res=getres("SELECT DISTINCT buildingroom FROM event WHERE status='approved' AND start>$now GROUP BY buildingroom ORDER BY COUNT(buildingroom) DESC");
		$lc=0;
		while( ($arr=mysql_fetch_array($res,MYSQL_NUM)) && $lc<10)
		{
			$loc=$arr[0];
			if($loc!='')
			{
				$lc++;
				echo("<li class='fillink'><a href='$filterstr&where=$loc'>$loc</a></li>");
			}
		}
		echo("</ul>");
	
		
		echo("<div>Group:</div>");
		echo("<ul><li class='fillink'><a href='groups.php'>Groups...</a></li></ul>");
		
		echo("<div>Category:</div><ul>");
		
		
		foreach( $catop as $catli)
		{
			$escspaces=rawurlencode($catli);//str_replace(" ","%20",$catli);
			echo("<li class='fillink'><a href='$filterstr&cat=$escspaces'>$catli</a></li>");
		}
		echo("</ul>");
		
		echo("<div>Type:</div><ul>");
		foreach( $typeop as $typeli)
		{
			$escspaces=rawurlencode($typeli);//sstr_replace(" ","%20",$typeli);
			echo("<li class='fillink'><a href='$filterstr&type=$escspaces'>$typeli</a></li>");
		}
		echo("</ul>");
		
	?>
</div>
</div>

<div class='mainbit'>
<h2>Events</h2>

<?php

if($uid!='')
{
?>

	<div><a href="addevent.php">Add an event</a></div>

<?php
}

else
{
	$fwd='?fwd=addevent.php';
	echo("<div><a href='login.php$fwd'>login</a> to add an event</div>");
}

?>

<?php

//echo("SELECT id,title,description,start,end,org,price,contactname,contactemail,tel,web,book,buildingroom,address1,address2,towncity,postcode,status FROM event $where ORDER BY start ASC");

$res=getres("SELECT id FROM event $where ORDER BY start ASC");
//echo('<table>');
$ec=0;
while($arr=mysql_fetch_array($res,MYSQL_NUM))
{

	$ec+=1;
	$arr=event_data($arr[0]);
	echo("<a href='event.php?eid=$arr[id]'>");
	eventbox($arr);
	echo("</a>");
	
	/*$sdesc=substr($arr[2],0,30);
	if(strlen($sdesc)<strlen($arr[2]))
	{
		$sdesc.="...";
	}
	$startdate=formatdate($arr[3]);
	$groupid=$arr[5];
	$glink='';
	if($groupid!=0)
	{
		$group=getraw('org','orgname',$groupid,$null);
		$glink='<a href="group.php?id='.$groupid.'">'.$group.'</a>';
	}
	else
		$group="none";
	
	
	echo("<tr><td><a href=\"event.php?eid=$arr[0]\">$arr[1]</a></td><td>$sdesc</td><td>$startdate</td><td>$glink</td></tr>");
	
	*/
	
}
if($ec==0)
{
	if($title!='') {
		echo("No events found $title.");
	} else {
		echo("No events found.");
	}
}
//echo('</table>');

echo("</div>");//main

endhtmlpage();
?>