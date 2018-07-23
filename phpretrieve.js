//Get Comments 
function getData(div, url)
{
	var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange= function() {
			if(xmlhttp.readyState== 4) //4 is recv'd all responses
			{
				var resp = xmlhttp.responseText;
				document.getElementById(div).innerHTML= resp;
			}			
		}
		xmlhttp.open("GET", url, true);
		xmlhttp.send(null);
	}
	else
		alert("Your browser doesn't seem to support ajax");
}

function getComments(imgid)
{
	getData("comments", "getcomments.php?fileName=" + imgid + ".jpg");
}
