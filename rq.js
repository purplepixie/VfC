function makeHttpObject() {	
  try {return new XMLHttpRequest();}
  catch (error) {}
  try {return new ActiveXObject("Msxml2.XMLHTTP");}
  catch (error) {}
  try {return new ActiveXObject("Microsoft.XMLHTTP");}
  catch (error) {}

  throw new Error("Could not create HTTP request object.");
}
function rq(url, success, failure) {
  var request = makeHttpObject();
  request.open("GET", url, true);
  request.send(null);
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      if (request.status == 200) {
        success(request.responseText);
      } else if (failure)
        failure(request.statusText, request.status);
    }
  };
}

function populate_location(object) {
	var room = object.value;
	rq("rq.php?it="+room,dit);
}

function dit(response) {	
	var n = response.split("|-|");
	document.getElementsByName('buildingroom')[0].value = n[0];
	document.getElementsByName('address1')[0].value = n[1];	
	document.getElementsByName('address2')[0].value = n[2];
	document.getElementsByName('towncity')[0].value = n[3];
	document.getElementsByName('pcode')[0].value = n[4];
	$('input, textarea').each(function() { toggleLabel.call(this); });
}

