<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--- header and style definitions --> 
<head>
<title>Commissions - ThatShouldBeAComic.com</title>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="getimagesdb.php?displayName=smithyisspiffy&desc=@c"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25075932-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<?php
require_once 'config.php';
$email = $_SESSION['email'];
$curimage=$_GET['image'];
if (!$curimage)
{
	$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
else
{
	$_SESSION['lastPage'] = "commissions.php";
}
?>
<!--- Content-->
<body>
<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>
<!--menu-->
<div class="mainmenu">
<div id="temp">
</div>
<div id="addcomment">
</div>
<table style="padding:5px">
	<tr>
		<td><button type="button" id="btnTSBAC" onclick="addIdea()" title="I have an idea!" alt="I have an idea!">That Should Be a Comic!!!</button></td>
		<td><button type="button" id="btnUpdate" onclick="location.href='updates.html'" title="View updates" alt="View updates">Updates</button></td>
		<?php if ($email) echo "<!--";?>
			<td><button type="button" id="btnAddUser" onclick="location.href='adduser.php'" title="Sign me up!" alt="Sign me up!">Register</button></td>
		<?php if ($email) echo "-->";?>
		<?php if ($email) echo "<!--";?>
			<td><button type="button" id="btnLogin" onclick="login()" title="Log me in!" alt="Log me in!">Login</button></td>
		<?php if ($email) echo "-->";?>
		<?php if (!($email)) echo "<!--";?>
			<td><button type="button" id="btnLogout" onclick="location.href='logout.php'" title="Log out" alt="Log out">Logout</button></td>
		<?php if (!($email)) echo "-->";?>
		<?php if (!($email)) echo "<!--";?>
			<td><button type="button" id="btnUpload" onclick="location.href='fileselect.php'" title="Upload" alt="Upload">Upload</button></td>
		<?php if (!($email)) echo "-->";?>
		<?php if (!($email)) echo "<!--";?>
			<td><button type="button" id="btnEditAccount" onclick="location.href='edituser.php'" title="Edit Account" alt="Edit Your Account Info">Edit Account</button></td>
		<?php if (!($email)) echo "-->";?>
		<?php if (!$email) echo "<!--"; ?><td><button type="button" id="btnLick" onclick="lick()" alt="Lick the comic!" title="Lick the comic!">Lick!</button></td> <?php if (!$email) echo "-->"; ?>
		<td><button type="button" id="commentClick" onclick="addComment()">Comment</button></td>
	</tr>
</table>
</div> <!--menu-->

<br />
<!------Comic Viewer------------>
<div align="center">
<a class="comicview" href="#">
	<canvas id="canvas" name="canvas"></canvas>
	<span>		
		<!-- *** Code to show box when hover -->
		<div align="center">
		<table id="infobox" class="imgdesc">
		<tr> <td><span id="filedesc"></span> </tr> </td>
		</table>
		</div>		
	</span>
</a>
<!----------------------------->
</div>
<p class="clear">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript" src="cview.js"></script>
<script type="text/javascript">
window.onload=function(){
	var curimg=-1;
	<?php if (empty($curimage))
			echo "init(-1);";
		  else 
			echo "init($curimage);";
			                         ?>	
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
</form>
</p>
</div>

<div align="center" id="licks">
</div>
<div align="center" id="comments">
</div>
<div align="center">
<form><button type="button" id="commentClick" onclick="addComment()">Comment</button></form>
</div>
<div align="center" id="addcomment">
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