<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="getimagesdb.php?displayName=_ANY_&desc=@p6"></script>
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
	$_SESSION['lastPage'] = $GLOBALS['FQP'] . "/period6.php";
}
?>
<!--- Content-->
<body>
<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>

<div align="center">
<?php require($DOCUMENT_ROOT . "mainmenu.html"); ?>
<br />
<!------Comic Viewer------------>
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
<?php require($DOCUMENT_ROOT . "rotatemenu.html"); ?>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript" src="cview.js"></script>
<script type="text/javascript">
window.onload=function(){
	var curimg=-1;
	<?php if (empty($curimage))
			echo "init(0);";
		  else
			echo "init($curimage);";
			                         ?>
	<?php if (!($email)) echo "/*";?>
			checkNewMessages();
			var timeoutIdMsg=0;
			timeoutIdMsg = setInterval("checkNewMessages()", 300000 );
	<?php if (!($email)) echo "*/";?>
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
