//date
var monthNames=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var calen=0;

//preload
pic1= new Image(100,25);
pic1.src="loading.gif";


ajaxObject = function() {
    try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); } catch(e){
    try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); } catch(e){
    try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch(e){
    try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e){
    try { return new XMLHttpRequest() } catch(e){
    throw new Error( "This browser does not support XMLHttpRequest." );
    }}}}}}

function ajaxsendform(that,gotopage)
{
    var request = new ajaxObject();

	// alert(gotopage);
    if(gotopage != null)
	{
		var add = "&fwd="+gotopage;
	}

    request.open('POST', 'submit.php?ajax=y'+add, true);

    request.onreadystatechange = function() {
    var done = 4, ok = 200;
    if (request.readyState == done && request.status == ok) {
      document.body.style.cursor = 'auto';
        if(request.responseText!='')
        {
            //alert(request.responseText);
            lbit=Left(request.responseText, String("Goto:").length);
            //alert(lbit);
            if(lbit=="Goto:")
            {
                window.location.href=Right(request.responseText,String(request.responseText).length-String("Goto:").length);
                //if we have stayed on the same page but moved to an anchor the page may not have been refreshed. If not we shopulkdnt get this far
                //but if we do
                urlstr= new String(window.location.href);
                if(urlstr.indexOf('#')!=-1)
                    window.location.reload();
            }
            else
            {
                erel=document.getElementById('errordiv');
                erel2=document.getElementById('errordiv2');
                if(null!=erel)
                {
                    erel.innerHTML=request.responseText;
                    erel2.innerHTML=request.responseText;
                }
                else
                {
                    erel.innerHTML='';
                    erel2.innerHTML='';
                    alert(request.responseText);
                }
                /*
                if(window.Recaptcha!=undefined)
                {
                    Recaptcha.reload();
                }
                */
            }
        }
        else
        {
		 //alert("empty response");

            if(gotopage == null)
                window.location.reload();
            else
            {
                window.location.href=gotopage;
            }
            
        }


    }
  };

  el=that;
  var tn=el.tagName;
  tn=tn.toLowerCase();
  while(tn !='form' )
  {
      el=el.parentNode;
      tn=el.tagName;
      tn=tn.toLowerCase();
  }

  var elem = el.elements;
  var poststring='';
  for(var i = 0; i < elem.length; i++)
    {
        if(poststring!='')
            poststring+='&';

        if( (elem[i].tagName.toLowerCase()!="input") || (elem[i].getAttribute("type").toLowerCase()!='checkbox') || (elem[i].checked) )
            poststring += elem[i].name + '=' + encodeURIComponent(elem[i].value);
    }
    //alert(poststring);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //request.setRequestHeader("Content-length", poststring.length); // generating an error on modern browsers
    //request.setRequestHeader("Connection", "close");

  request.send(poststring);
  document.body.style.cursor = 'wait';
  document.getElementById('errordiv').innerHTML='<img style="margin:auto;" src="loading.gif" alt="wait"/>';
  document.getElementById('errordiv2').innerHTML='<img style="margin:auto;" src="loading.gif" alt="wait"/>';

}

function Left(str, n)
{
     if (n <= 0)
         return "";
     else if (n > String(str).length)
         return str;
     else
       return String(str).substring(0,n);
}

function Right(str, n)
{
     if (n <= 0)
        return "";
     else if (n > String(str).length)
        return str;
     else {
        var iLen = String(str).length;
        return String(str).substring(iLen, iLen - n);
     }
}

function iwriteCalendar()
{
     this.firstofmonth.setDate(1);
     this.firstofmonth.setFullYear(this.curdate.getFullYear());
     this.firstofmonth.setMonth(this.curdate.getMonth());
     firstDay=this.firstofmonth.getDay();

     this.lastDayInMonth.setFullYear(this.curdate.getFullYear());
     this.lastDayInMonth.setDate(1);
     this.lastDayInMonth.setMonth(this.curdate.getMonth()+1);
     this.lastDayInMonth.setDate(1);
     this.lastDayInMonth.setDate(this.lastDayInMonth.getDate()-1);
     daysInMonth=this.lastDayInMonth.getDate();

     this.nowdate=new Date();
	var lastd=this.nowdate.getDate();
	var lastm=this.nowdate.getMonth();
	lastm+=3;
	var lasty=this.nowdate.getFullYear();
	while(lastm>12)
	{
		lastm-=12;
		lasty++;
	}
     htmlstr="<table class='calendar' id='caltab'><tr><th  class='minus' onclick=\"calen.moveMonth(-1);\" >-</th><th colspan=5>" + monthNames[this.curdate.getMonth()] + " " + this.curdate.getFullYear() + "</th><th class='plus' onclick=\"calen.moveMonth(1);\" >+</th></tr><tr>" + "<th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr><tr>";
     for(n=0;n<firstDay;n++)
          htmlstr+="<td></td>";
     for(n=1;n<=daysInMonth;n++)
     {
          if((n+firstDay-1)%7==0)
               htmlstr+="</tr><tr>";
          htmlstr+="<td class='calendar";
          if( (n==this.pickeddate.getDate()) && (this.firstofmonth<=this.pickeddate) && (this.lastDayInMonth>=this.pickeddate) )
          {
               htmlstr+=" picked' onclick=\"calen.chooseDate(" +n +",event);\"";
          }
	  else
	  {
		if(  (this.curdate.getFullYear()<this.nowdate.getFullYear()) || (( (this.curdate.getMonth()<this.nowdate.getMonth()) && (this.curdate.getFullYear()==this.nowdate.getFullYear()) )) || (( (n<this.nowdate.getDate()) && (this.curdate.getMonth()==this.nowdate.getMonth()) && (this.curdate.getFullYear()==this.nowdate.getFullYear()) ))  )
			htmlstr+=" unavail'";
		else
		{
			/*if(  (this.curdate.getFullYear()>lasty) || (( (this.curdate.getMonth()>lastm) && (this.curdate.getFullYear()==lasty) )) || (( (n>lastd) && (this.curdate.getMonth()==lastm) && (this.curdate.getFullYear()==lasty) ))  )
				htmlstr+=" unavail'";
			else*/
				htmlstr+="' onclick=\"calen.chooseDate(" +n +",event);\"";
		}

	  }
	  htmlstr+=">" + n + "</td>";

     }

     for(n=0;((firstDay+daysInMonth+n)%7)!=0;n++)
          htmlstr+="<td></td>";
     htmlstr+="</tr></table>";

     this.caldiv.innerHTML=htmlstr;

}

function ichooseDate(nday,event)
{
    event.cancelBubble = true;

     this.curdate.setDate(nday);
     this.input.value=this.curdate.getDate() + "/" + (this.curdate.getMonth() + 1) + "/" + this.curdate.getFullYear();
     //window.close();
     //this.writeCalendar();
     this.caldiv.innerHTML='';
     calen=0;
}

function imoveMonth(amount)
{
     this.curdate.setMonth(this.curdate.getMonth()+amount);
     //window.opener.document.getElementById(window.name).value=this.curdate.getDate() + "/" + (this.curdate.getMonth() + 1) + "/" + this.curdate.getFullYear();
     this.writeCalendar();
}
function idate(that,inputid)
{
    if(that.innerHTML=='')
    {
     this.writeCalendar=iwriteCalendar;
     this.chooseDate=ichooseDate;
     this.moveMonth=imoveMonth;

     this.caldiv=that;
     this.input=document.getElementById(inputid);

     dstr=document.getElementById(inputid).value;

     if((dstr=='00/00/0000')||(dstr=='')||(dstr=='//'))
     {
          this.curdate=new Date();
	  this.pickeddate=new Date();
     }
     else
     {
		this.day=dstr.match(/^[^\/]*/);
		mstr='fgd';
		mstr=dstr.match(/\/[^\/]*/);
		mstr2=mstr[0];
		this.month=mstr2.match(/[0-9]*$/);
		this.year=dstr.match(/[^\/]*$/);

		this.curdate=new Date();
		this.curdate.setDate(this.day);
		this.curdate.setMonth(this.month-1);
		this.curdate.setFullYear(this.year);
		this.pickeddate=new Date();
		this.pickeddate.setDate(this.day);
		this.pickeddate.setMonth(this.month-1);
		this.pickeddate.setFullYear(this.year);
     }

     this.firstofmonth=new Date();
     this.lastDayInMonth=new Date();

     this.writeCalendar(that);
     //document.selection.empty();
     window.getSelection().removeAllRanges();
     }


}

function googleloc(send)
{
    //alert('here');
	add=document.getElementsByName('postcode')[0];
	lat=document.getElementsByName('lat')[0];
	lng=document.getElementsByName('lng')[0];
	gcd=document.getElementsByName('geolocdone')[0];
	errdiv=document.getElementById('errordiv');

	if(add.value!='')
	{
		var geocoder = new GClientGeocoder();
		geocoder.getLatLng(add.value,function(point)
		{
			if (!point)
			{
				alert(add.value + " not understood by google, please try expressing your address differently.");
			}
			else
			{
				//add.value='';//this is so we don't repeat the geocoding sever side
				gcd.value='yes';
				lat.value=point.lat();
				lng.value=point.lng();
				//alert("Google say:"+lat.value+','+lng.value);
				if(send)
                {
                    if((add.form.method=='get') || (add.form.method=='GET'))
                        add.form.submit();
                    else
					    ajaxsendform(add);
                }
				else
				{
					document.getElementById('sform').submit();
				}
			}
		});

	}
	else
	{
		if(send)
			ajaxsendform(add);
		else
			document.getElementById('sform').submit();
	}

}

function cloneObject(what) {
    for (i in what) {
        if (typeof what[i] == 'object') {
            this[i] = new cloneObject(what[i]);
        }
        else
            this[i] = what[i];
    }
}
