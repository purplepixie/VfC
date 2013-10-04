<?php

require_once("functions.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="print.css" media="print" />
<title>Visions for Change Events</title>
</head>

<body>
<h1 style="text-align: center">Upcoming Events</h1>
<p style="text-align: center">More information and more events at www.visionsforchangenorwich.org.uk</p>
<?php

$now=strtotime("25 September 2013");
$future=$now + 60*60*24*14;

$res=getres("SELECT id FROM event WHERE status='approved' AND start>$now AND start<$future ORDER BY start ASC");
//echo('<table>');
$ec=0;
while($arr=mysql_fetch_array($res,MYSQL_NUM))
{

	$ec+=1;
	$arr=event_data($arr[0]);
	echo("<a href='event.php?eid=$arr[id]'>");
	echo reventbox($arr);
	echo("</a>");
	
}

?>
</body>
</html>