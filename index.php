<?php
require_once("functions.php");
starthtmlpage("Visions","A directory of radical groups and events in Norwich; managed by Visions for Change - A resource for individuals and groups working for a just and sustainable future.",true); 
// Sidebar not closed.  Add further sidebar items before the </div> below
$res1=getres("SELECT title FROM about"); ?>
	<div class="side">
		<h3>About sections</h3>
		<?php
		$i = 1;
		while($ar=mysql_fetch_array($res1,MYSQL_NUM)) {
			$name=str_replace(' ', '', $ar[0]);
			echo("<p onclick=\"coll('$i')\"><a href='#$name'>$ar[0]</a></p>");
			$i++;
		} ?>
	</div>
</div> <?php // closes sidebar ?>
<h2>What is Visions for Change?</h2>

<div class="collapse"><?php

	$res=getres("SELECT title,short,long_text FROM about");
	$i = 1;
	while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
		$first=$arr[1];
		$second=$arr[2];
		$name=str_replace(' ', '', $arr[0]);
		echo("<a name=\"$name\"><div onclick=\"coll('$i')\"><b>$arr[0]</b></a> $first");
		if($second!='') {
			echo("<span id=\"m$i\" class=\"more\"> <a href=\"javascript:void(0)\">read more...</a></span>");
			echo("<span id=\"c$i\" title=\"Click again to collapse\">$second</span>");
		}
		echo("</div>");
		$i++;
	}

?></div>

<?php
endhtmlpage();
?>