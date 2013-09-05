function collapse_load()
{
	for(var i=1;i<15;i++) {
		if(document.getElementById("c"+i) != null) {
			document.getElementById("c"+i).style.display="none"
			document.getElementById("m"+i).style.display="inline"
		}
	}
}
function coll(el)
{
	if (document.getElementById('c'+el).style.display=="none")
		{
			document.getElementById('c'+el).style.display="inline"
			document.getElementById('m'+el).style.display="none"
		}
	else if (document.getElementById('c'+el).style.display=="inline")
		{
			document.getElementById('c'+el).style.display="none"
			document.getElementById('m'+el).style.display="inline"
		}
}