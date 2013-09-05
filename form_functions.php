<?php

// The functions here are to do with the appearance of forms. Functions associated with the submission of forms are found in submit.php.

function startform() {
	$fwd="'$_GET[fwd]'";
    echo('<form method="post" action="submit.php" accept-charset="utf-8" onsubmit="ajaxsendform(this,'.$fwd.');return false;">');
}

function endform() {
	echo("</form>");
}

$controlcount=0;
function issueId() {
    global $controlcount;
	$id="ctrl".$controlcount;
	$controlcount++;
	return $id;
}

function txtbx($label,$value,$submitname,$width='w660') {
	$id = issueId();
	echo('<label class="input '.$width.'" for="'.$id.'" title="'.$label.'">');
	echo('<span>'.$label.'</span>');
	echo('<input name="'.$submitname.'" type="text" id="'.$id.'" value="'.$value.'" />');
	echo('</label>');
}

function txta($label,$value,$submitname) {
	$id = issueId();
	echo('<label class="input w660" for="'.$id.'" title="'.$label.'">');
	echo('<span>'.$label.'</span>');
	echo('<textarea name="'.$submitname.'" type="text" id="'.$id.'">'.$value.'</textarea>');
	echo('</label>');
}

function btn($type, $label, $extras='') {
	$id = issueId();
	if($type=='button')
		echo('<input name="'.$label.'" class="stdbtn" type="button" id="'.$id.'ctrl" value="'.$label.'" '.$extras.' />');
	else
		echo('<input name="'.$label.'" class="stdbtn" type="submit" id="'.$id.'ctrl" value="'.$label.'" '.$extras.' />');
}

function hidden($type,$anid) {
	$id = issueId();
	echo('<input name="'.$type.'" class="stdin" type="hidden" id="'.$id.'ctrl" value="'.$anid.'" />');

}

function datesel($label,$value,$classlist,$submitname,$extraatribs='') {
	
	$id = issueId();
	if($classlist != '') {
		$classlist .= ' ';
	}
	
    echo('<div class="'.$classlist.'" id="'.$id.'div">');
    echo('<label for="'.$id.'ctrl">'.$label.'</label>');
	echo('<input name="'.$submitname.'" class="calinput nonbutton" type="datetime" id="'.$id.'ctrl" value="'.$value.'" '.$extraatribs.' />');
	echo('<div class="caldiv" onclick="if(calen==0)calen=new idate(this,\''.$id.'ctrl\');"></div>');
	echo('</div>');
}

function timesel($label,$value,$classlist,$submitname,$extraatribs='') {
	
	$id = issueId();
	if($classlist != '') {
		$classlist .= ' ';
	}
	
    echo('<div class="'.$classlist.'" id="'.$id.'div">');
    echo('<label for="'.$id.'ctrl">'.$label.'</label>');
	
	if($value=='')
	{
		$hour="--";
		$min=0;
	}
	$hour=(int)($value/60);
	$min=(int)($value%60);
	echo('<select id="'.$id.'ctrl" class="nonbutton"  name="'.$submitname.'hour">');
	$sel='';
	if($hour=='--')
		$sel='selected="selected"';
	echo('<option value="--" '.$sel.'>--</option>'."\n");
	for($ophour=0;$ophour<24;$ophour++)
	{
		$sel='';
		if($ophour==$hour)
			if($hour!='--')
				$sel='selected="selected"';
		if(strlen($ophour)<2)
			$cont='0'.$ophour;
		else
			$cont=$ophour;
		echo('<option value="'.$ophour.'" '.$sel.'>'.$cont.'</option>'."\n");
	}
	
	echo('</select>');
	$id = issueId();
	echo(':');
	echo('<select id="'.$id.'ctrl" class="nonbutton"  name="'.$submitname.'min">');
	
	for($opmin=0;$opmin<60;$opmin+=5)
	{
		$sel='';
		if($opmin==$min)
			$sel='selected="selected"';
		if(strlen($opmin)<2)
			$cont='0'.$opmin;
		else
			$cont=$opmin;
			
		echo('<option value="'.$opmin.'" '.$sel.'>'.$cont.'</option>'."\n");
	}

	echo('</select>');
	echo('</div>');	
}

function gpsel($groupid, $width='w660') {
	global $uid;
	$id = issueId();
	echo('<label class="input '.$width.'" for="'.$id.'">');
	echo('<span>Select an organising group (optional)</span>');
	echo('<select id="'.$id.'" class="orgselect" name="group">');
	if($groupid) {
		echo('<option value="0">No Group</option>'."\n");
	} else {
		echo('<option selected="selected" value="0"></option>'."\n");
	}
	
	$modstr='';
	if( ismoderator() )
		$modstr=" Or 1;";
	$res2=getres("SELECT org.id,org.orgname FROM org JOIN groupadmin ON (groupadmin.gid = org.id) WHERE groupadmin.uid=".$uid.$modstr);
	while($arr2=mysql_fetch_array($res2,MYSQL_NUM))
	{
		$sel='';
		if($arr2[0]==$groupid)
			$sel='selected="selected"';
		echo('<option value="'.$arr2[0].'" '.$sel.'>'.$arr2[1].'</option>'."\n");
	}
	echo('</select>');
	echo('</label>');
}

function chbx($label,$value,$class,$submitname,$extraatribs='') {
	$id = issueId();
	if($value=='yes')
		$checked="checked";
	echo('<div class="'.$class.'" id="'.$id.'div">');
	echo('<input name="'.$submitname.'" class="checkbox" type="checkbox" id="'.$id.'ctrl" '.$checked.' '.$extraatribs.' />');
	echo('<label for="'.$id.'ctrl">'.$label.'</label><br />');	
	echo('</div>');
}

function delete_form($type, $id) {
	startform();
	hidden("delete$type",$id);
	btn('submit','Delete',"onclick='confirm(\"Sure?\")'");
	echo("</form>");
}

function f_edit_event($event='') {
	global $uid;
	
	startform();
	hidden('eid',$event[id]);
	txtbx('Event Title',$event[title],'eventtitle');
	gpsel($event[groupid]);
	?><table><tr><td><?php
	f_time($event);
	?></td><td><?php
	f_location($event);
	?></td></tr></table><?php
	txta('Description of Event',br2nl($event[description]),'description');
	
	txtbx('Price',$event[price],'price');
	txtbx('Contact Name',$event[contactname],'contactname');
	txtbx('Contact Email',$event[contactemail],'contactemail');
	txtbx('Web Link',$event[website],'web');
	
	echo('<div class="quarter control">');
	echo('<label for="delsel2">Booking necessary</label><br /><span>');
	echo('<select id="delsel2" class="nonbutton"  name="book">');
	$optarr=array('no','yes','optional','optionalplaceslimited');
	$opttxtarr=array('no','yes','optional','optionalplaceslimited');
	
	foreach($optarr as $b)
	{
		$sel='';
		if($b==$event[book])
			$sel='selected="selected"';
		echo('<option '.$sel.' value="'.$b.'">'.$b.'</option>'."\n");
	}
	
	echo('</select></span>');
	echo('</div>');
	
	btn('submit','submit');
	endform();
}

function f_time($event) {
	?><h4>Date & Time</h4><?php
	
	$id = issueId();

    echo('<label for="'.$id.'ctrl" class="input w120" onclick=\'$("#dp").datepicker("show");\'><span>dd/mm/yyyy</span>');
	echo('<input name="startdate" type="datetime" id="datepicker" value="'.$event[startdatecal].'" onchange="onDateChange()" />');
	echo('</label>');
	
	if(isset($event[hour])) {
		$timetoinsert = 60*$event[hour]+$event[min];
	} else {
		$timetoinsert = '';
	}
	timesel('Start Time&nbsp;',$timetoinsert,'stdin','starttime');
	// if($event[end] == 0) {
		// $event[enddatecal] = '';
	// }
	// datesel('End Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$event[enddatecal],'stdin','enddate');
	timesel('End Time &nbsp;',60*$event[endhour]+$event[endmin],'stdin','endtime');
	
}

function f_location($event) { 
	?><h4>Location</h4><?php
	
	echo('<label class="input w320" for="locsel" title="Select a previously used location or enter a new one below"><span>Select a location or enter new below</span>');
	echo('<select id="locsel" class="locselect" onchange="populate_location(this)" name="location">');
	echo('<option selected="selected" value="0"></option>'."\n");
	
	$res3=getres("SELECT buildingroom,address1,address2,towncity,postcode,id FROM event WHERE status='approved' GROUP BY buildingroom");
	$i=1;
	while($arr3=mysql_fetch_array($res3,MYSQL_NUM))
	{
		$sel='';
		if($arr3[0]==$event[buildingroom])
			$sel='selected="selected"';
		if($arr3[0] != '')
			echo('<option value="'.$arr3[5].'" '.$sel.'>'.$arr3[0].' '.$arr3[1].'</option>'."\n");
		$i++;
	}
	echo('</select>');
	echo('</label>');
	
	txtbx('Building/Room',$event[buildingroom],'buildingroom','w320');
	txtbx('Address1',$event[address1],'address1','w320');
	txtbx('Address2',$event[address2],'address2','w320');
	txtbx('Town/City',$event[towncity],'towncity','w320');
	txtbx('Postcode',$event[postcode],'pcode','w320');
}

function f_edit_group($group='') {
	//echo(" * = Required");
	startform();
	hidden('gid',$group[0]);
	txtbx('Name of Group',$group[1],'orgname');
	txta('Description of Group',br2nl($group[2]),'orgdesc');
	txtbx('Contact Name',$group[3],'contactname');
	txtbx('Contact Email',$group[4],'publicemail');
	txtbx('Contact Telephone',$group[5],'tel');	
	txtbx('Website',$group[6],'website');
	btn('submit','submit');
	endform();
}

function pswd($label,$name,$width='w275') {
	$id = issueId();
	
	echo('<label class="input '.$width.'" for="'.$id.'" title="'.$label.'">');
	echo('<span>'.$label.'</span>');
	echo('<input name="'.$name.'" class="control" type="password" id="'.$id.'ctrl" maxlength="64" />');
	echo('</label>');

}

?>