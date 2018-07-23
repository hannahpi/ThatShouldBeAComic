//Get Data
function formatData(div, url, prefix, suffix)
{
	var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange= function() {
			if(xmlhttp.readyState== 4) //4 is recv'd all responses
			{
				var resp = xmlhttp.responseText;
				document.getElementById(div).innerHTML= prefix + resp + suffix;				
			}			
		}
		xmlhttp.open("GET", url , true);
		xmlhttp.send(null);
	}
	else
		alert("Your browser doesn't seem to support ajax");
}

//Get Data
function loadData(div, url)
{
	var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange= function() {
			if(xmlhttp.readyState== 4) //4 is recv'd all responses
			{
				var resp = xmlhttp.responseText;
				if (div=="log")
				{
					document.getElementById(div).innerHTML+= resp;
				}
				else
				{
					document.getElementById(div).innerHTML= resp;
				}
				
				if (div=="licks")
				{
					document.getElementById("btnLick").disabled = (document.getElementById(div).innerHTML.indexOf("You") >= 0 );
				}
			}			
		}
		xmlhttp.open("GET", url , true);
		xmlhttp.send(null);
	}
	else
		alert("Your browser doesn't seem to support ajax");
}

function postDataLogin(url)
{
var serialized = $('form').serialize();
//alert(serialized);
var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange = function() {
			var resp = xmlhttp.responseText;
			document.getElementById('temp').innerHTML = resp;
			if (resp.indexOf("You") >= 0)
			{				
				location.reload(true);
			}
		}
		xmlhttp.open("POST", url , true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(serialized);
		
	}
	else
		alert("Your browser doesn't seem to support ajax");

}
function postData(url)
{
var serialized = $('form').serialize();
//alert(serialized + "\nurl:" + url );
var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange = function() {
			var resp = xmlhttp.responseText;
			document.getElementById('temp').innerHTML = resp;
		}
		xmlhttp.open("POST", url , true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		xmlhttp.send(serialized);
	}
	else
		alert("Your browser doesn't seem to support ajax");

}

function postDataDivClear(div, url, clearIf, doAfter)
{
var serialized = $('form').serialize();
//alert(serialized + "\nurl:" + url );
var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange = function() {
			var resp = xmlhttp.responseText;
			document.getElementById(div).innerHTML = resp;
			if (resp.indexOf(clearIf) >= 0)
			{
				document.getElementById(div).innerHTML = "";
				doAfter();
			}
		}
		xmlhttp.open("POST", url , true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		xmlhttp.send(serialized);
	}
	else
		alert("Your browser doesn't seem to support ajax");

}


//submits a page in the background
function doData(url)
{
	var XMLHTTP= XMLHttpRequest || ActiveXObject("Microsoft.XMLHTTP");
	if (typeof XMLHTTP!= "undefined" ) 
	{
		var xmlhttp = new XMLHTTP;
		xmlhttp.onreadystatechange = function() {
			var resp = xmlhttp.responseText;
		}
		xmlhttp.open("GET", url , true);
		xmlhttp.send(null);
	}
	else
		alert("Your browser doesn't seem to support ajax");
}


//get comments
function getComments()
{
	loadData("comments","getcomments.php?imgID=" + galleryarray[curimg].id);
}

function getLicks()
{
	try
	{
		document.getElementById("btnLick").disabled = false;
	} catch (err)
	{            	}
	
	loadData("licks", "getlicks.php?imgID=" + galleryarray[curimg].id);
}

function commentSubmit()
{
	postDataDivClear("addcomment","addcomment.php?imgID=" + galleryarray[curimg].id, "submitted", getComments );
	document.getElementById("temp").innerHTML="";
	document.getElementById("addcomment").innerHTML="";
	loadData("comments", "getcomments.php?imgID=" + galleryarray[curimg].id);
	cancel();
}

function editSubmit(cID)
{
	postDataDivClear("addcomment","editcomment.php?id=" + cID, "successfully", getComments );
	document.getElementById("temp").innerHTML="";
	document.getElementById("addcomment").innerHTML="";
	loadData("comments", "getcomments.php?imgID=" + galleryarray[curimg].id);
	cancel();
}

function addComment()
{
	loadData("addcomment","addcomment.php?imgID=" + galleryarray[curimg].id);
	document.getElementById("commentClick").setAttribute("onclick","cancel()");
	document.getElementById("commentClick").innerHTML="Cancel";
}

function editComment(cID)
{
	loadData("addcomment", "editcomment.php?id=" + cID);
	document.getElementById("commentClick").setAttribute("onclick","cancel()");
	document.getElementById("commentClick").innerHTML="Cancel";
}

function addIdea()
{	
	loadData("addcomment","addcomment.php?imgID=31");
	document.getElementById("btnTSBAC").setAttribute("onclick","cancelIdea()");
	document.getElementById("btnTSBAC").innerHTML="Cancel";	
}

function lick()
{
	doData("lick.php?imgID=" + galleryarray[curimg].id);
	loadData("licks", "getlicks.php?imgID=" + galleryarray[curimg].id);
}

function cancel()
{
	document.getElementById("addcomment").innerHTML="";
	try
	{
		document.getElementById("commentClick").setAttribute("onclick","addComment()");
		document.getElementById("commentClick").innerHTML="Comment";
	} catch (err)
	{            	}
	
}

function cancelIdea()
{
	document.getElementById("addcomment").innerHTML="";
	document.getElementById("btnTSBAC").setAttribute("onclick","addIdea()");
	document.getElementById("btnTSBAC").innerHTML="TSBAC";
}

function cancelLogin()
{
	document.getElementById("temp").innerHTML="";
	document.getElementById("btnLogin").setAttribute("onclick","login()");
	document.getElementById("btnLogin").innerHTML="Login";
}

function login()
{
	loadData("temp","login.php");
	document.getElementById("btnLogin").setAttribute("onclick","cancelLogin()");
	document.getElementById("btnLogin").innerHTML="Cancel Login";
	//document.getElementById("submit").setAttribute("onclick", "loginSubmit()");
}

function loginSubmit()
{
	postDataLogin("login.php");	
}

function showRecent()
{
	loadData("temp","getnewcomments.php");
	document.getElementById("btnShowRecent").setAttribute("onclick","cancelShow()");
	document.getElementById("btnShowRecent").innerHTML="Hide Recent";
}

function cancelShow()
{
	document.getElementById("temp").innerHTML="";
	document.getElementById("btnShowRecent").setAttribute("onclick","showRecent()");
	document.getElementById("btnShowRecent").innerHTML="Recent";
}

function addBug()
{
	loadData("temp","addbug.php");
	document.getElementById("btnAddBug").setAttribute("onclick","cancelBug()");
	document.getElementById("btnAddBug").innerHTML="Cancel Report";
}

function addBugSubmit()
{
	postDataDivClear("addcomment","addbug.php", "submitted", cancelBug );
	document.getElementById("temp").innerHTML="";
	document.getElementById("addcomment").innerHTML="";
	cancelBug();
}

function cancelBug()
{
	document.getElementById("temp").innerHTML="";
	document.getElementById("btnAddBug").setAttribute("onclick","addBug()");
	document.getElementById("btnAddBug").innerHTML="Feedback";
	document.getElementById("addcomment").innerHTML="";
}

function getMessages()
{
	document.getElementById('log').scrollTop = document.getElementById("log").scrollHeight;
	loadData("log", "getchatter.php?halt=1");
}

function checkNewMessages()
{
	formatData("btnInbox", "getnumnewInbox.php", "Inbox (", ")");
	document.getElementById('btnInbox').setAttribute('Title',(document.getElementById('btnInbox').innerHTML));
}

function getInbox()
{
	loadData("log", "getnewinbox.php?halt=1");
}

function markRead(msgID)
{
	doData("markRead.php?msg=" + msgID);
	var elemToRemove = document.getElementById('message' + msgID);
	var parent = elemToRemove.parentNode
	parent.removeChild(elemToRemove);
}