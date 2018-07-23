//Get Comments 
function getComments()
{
	var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange= function() {
			if(xmlhttp.readyState== 4) //4 is recv'd all responses
			{
				var resp = xmlhttp.responseText;
				document.getElementById("comments").innerHTML= resp;
			}			
		}
		xmlhttp.open("GET", "http://www.thatshouldbeacomic.com/new/getcomments.php?fileName=" + galleryarray[curimg].id + ".jpg", true);
		xmlhttp.send(null);
	}
	else
		alert("Your browser doesn't seem to support ajax");
}