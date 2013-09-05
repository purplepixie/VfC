<?php
require_once("functions.php");
starthtmlpage("Visions - Admin - Edit Homepage Info"); ?>
<script type="text/javascript">
	function redirectMe (sel) {
		var url = 'editinfo.php?section='+sel[sel.selectedIndex].value;
		window.location = url;
	}
</script>
<div class="">
</div>
<?php
if(ismoderator()) {
	echo('<h2>Edit Homepage Info</h2>');
	
	echo("Choose a section to edit or start typing to create a new section");
	startform(); // Defined in form_functions.php, along with control(), txtbx(), endform()
		$res=getres("SELECT id,title,short,long_text FROM about");
		echo('<select id="orgsel" class="orgselect"  name="group" onchange="redirectMe(this)">');
		echo('<option selected="selected" value="0">New Section</option>'."\n");
		
		while($arr=mysql_fetch_array($res,MYSQL_NUM)) {
			if($_GET['section']==$arr[0])
				$current=$arr;
			$sel='';
			if($arr[0]==$_GET['section'])
				$sel='selected="selected"';				
			echo('<option value="'.$arr[0].'" '.$sel.'>'.$arr[1].'</option>'."\n");
		}
		echo('</select>');
	endform();
	
	startform(); 
		hidden('id',$current[0]);
		txtbx('Section Title',$current[1],'sectiontitle');
		txta('Text as collapsed',$current[2],'short');
		txta('Additional Text for expanded version',$current[3],'long');
		btn('submit','submit');
	endform();
	
} else {
	echo("Please log in  as a moderator to see this page");
}

endhtmlpage();

?>