<?php
$cache_expire = 60*60*24*365;
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>ThatShouldBeAComic.com</title>
<head>

<!--[if lt IE 9]>
    <script src="http://www.dreamersnet.net/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" content="A website developed for a web comic form of blogging.  Users can upload their own comics.">
<meta name="keywords" content="online comics, webcomic, comication">
</head>

<?php
if (isset($_SESSION['email']))
	$email = $_SESSION['email'];
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>
<!--- Content-->
<body>
<div align="center">
<img id="mappedImg" src="img.php?src=tsbacIndex.jpg&width=1000&height=824" usemap="#indexmap" width="1000" height="824" />
</div>
<map name="indexmap"> <!-- add: onMouseOver="changeMap('')" onMouseOut="changeMap('tsbacIndex.jpg')" -->
	 <area shape="rect" coords="128,132,238,177" alt="Comics" id="selComic" href="comics.php" onMouseOver="changeMap(1)" onMouseOut="changeMap(0)" >
     <area shape="rect" coords="271,132,475,177" alt="Illustrations" id="selIllus" href="viewSeries.php?series=illu">
     <area shape="rect" coords="509,132,625,177" alt="Artists" id="selArtists" href="viewUsers.php">
	 <area shape="rect" coords="346,685,418,724" alt="Paige" id="selPaige" href="#" onMouseOver="changeMap(2)" onMouseOut="changeMap(0)"> <!--"userView.php?display=paige"-->
	 <!--<area shape="rect" coords="167,685,264,724" alt="x" id="selX" href="userView.php?display=X" onMouseOver="changeMap(3)" onMouseOut="changeMap(0)">-->
	 <area shape="rect" coords="458,685,542,724" alt="Jessie" id="selJessie" href="userView.php?display=smithyisspiffy" onMouseOver="changeMap(3)" onMouseOut="changeMap(0)">
	 <area shape="rect" coords="593,685,647,724" alt="Allie" id="selAllie" href="ally.php" onMouseOver="changeMap(4)" onMouseOut="changeMap(0)">
	 <area shape="rect" coords="744,685,833,724" alt="Comics" id="selComic2" href="comics.php" onMouseOver="changeMap(1)" onMouseOut="changeMap(0)">
</map>
<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript">
var mouseOvers = new Array();
for (var i=0; i<10; i++)
{
	mouseOvers[i] = new Image();
}
mouseOvers[0].src = "img.php?src=tsbacIndex.jpg&width=1000&height=824";
mouseOvers[1].src = "img.php?src=MOcomics.jpg&width=1000&height=824";
mouseOvers[2].src = "img.php?src=MOpaige.jpg&width=1000&height=824";
mouseOvers[3].src = "img.php?src=MOjessie.jpg&width=1000&height=824";
mouseOvers[4].src = "img.php?src=MOallie.jpg&width=1000&height=824";

function changeMap(imgID)
{
	var mapImg = document.getElementById('mappedImg');
	mapImg.src = mouseOvers[imgID].src;
}

var curimg=0;
var galleryarray= new Array();
galleryarray[curimg]= new Image();
galleryarray[curimg].id = 31;

function init()
{
	getComments();
	loadData("lastupdate","getlast.php");
	<?php if (!($email)) echo "/*";?>
			checkNewMessages();
			var timeoutIdMsg=0;
			timeoutIdMsg = setInterval("checkNewMessages()", 300000 );
	<?php if (!($email)) echo "*/";?>
}

window.onload=function(){
	init();
}
</script>
<br />
<br />

<?php require "indexmenu.html"; ?>


</div>

<div id="comments">
</div>
<div id="lastupdate">
</div>
<br>
<br>
<br>
