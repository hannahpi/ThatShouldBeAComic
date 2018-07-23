<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="getimagesdb.php?displayName=smithyisspiffy&desc=@j"></script>
</head>
<?php
session_start();
$email = $_SESSION['email'];
$curimage=$_GET['image'];
if (!$curimage)
{
	$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
else
{
	$_SESSION['lastPage'] = "http://www.thatshouldbeacomic.com/new/journal.php";
}
?>
<!--- Content-->
<body>
<a href="http://www.thatshouldbeacomic.com/new/">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>
<div align="center">
<table style="padding:5px">
	<tr>
		<td><button type="button" id="btnTSBAC" onclick="location.href='addcomment.php?fileName=thatshouldbeacomic'" title="I have an idea!" alt="I have an idea!">That Should Be a Comic!!!</button></td>
		<td><button type="button" id="btnUpdate" onclick="location.href='updates.html'" title="View updates" alt="View updates">Updates</button></td>
		<td><button type="button" id="gotoBugs" alt="report a bug" onclick="location.href='bugs.html'">Bugs</button></td>
		<?php if ($email) echo "<!--";?>
			<td><button type="button" id="btnAddUser" onclick="location.href='adduser.php'" title="Sign me up!" alt="Sign me up!">Register</button></td>
		<?php if ($email) echo "-->";?>
		<?php if ($email) echo "<!--";?>
			<td><button type="button" id="btnLogin" onclick="location.href='login.php'" title="Log me in!" alt="Log me in!">Login</button></td>
		<?php if ($email) echo "-->";?>
		<?php if (!($email)) echo "<!--";?>
			<td><button type="button" id="btnLogout" onclick="location.href='logout.php'" title="Log out" alt="Log out">Logout</button></td>
		<?php if (!($email)) echo "-->";?>
		<?php if (!($email)) echo "<!--";?>
			<td><button type="button" id="btnUpload" onclick="location.href='fileselect.php'" title="Upload" alt="Upload">Upload</button></td>
		<?php if (!($email)) echo "-->";?>
	</tr>
</table>
<br />
<canvas id="canvas" name="canvas"> </canvas>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript">
var curimg = galleryarray.length-1;    //Initialize counter for array.
var firstimg = galleryarray.length-1;  //Normal first image
</script>
<script src="getcomments.js" type="text/javascript"></script>
<script src="canvasviewer.js" type="text/javascript"> 

window.onload=function(){
	init();
} 

</script> 
<form>
<div align="center">
<br />
<button type="button" id="btnFirst" onclick="firstimage()">|<<</button>
<button type="button" id="btnPrev" onclick="previmage()"><<</button>
<button type="button" id="btnNext" onclick="nextimage()">>></button>
<button type="button" id="btnLast" onclick="lastimage()">>>|</button>
<br />
<button type="button" id="gotoBugs2" alt="report a bug" onclick="location.href='bugs.html'">Bugs</button>
<button type="button" id="commentClick" onclick="location.href='addcomment.php'">Comment</button>
</form>
</div>
<div align="center" id="comments">
</div>
<br>
<br>
<div align="center">
[ <a href="credits.html">Programming Credits</a> | <a href="showall.html">Show All</a> ]
</div>
<br>
<br>
</body>
</html>