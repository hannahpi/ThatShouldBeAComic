<?php 
$cache_expire = 60*60*24*365;
session_start();
 /*header("Pragma: public");
 header("Cache-Control: max-age=".$cache_expire);
 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>ThatShouldBeAComic.com</title>
<head>

<!--[if lt IE 9]>
    <script src="http://www.dreamersnet.net/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="tsbacNew.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" content="A website developed for a web comic form of blogging.  Users can upload their own comics.">
<meta name="keywords" content="online comics, webcomic, comication, dino web comic, dinogirl, That Should Be A Comic">
	<meta property="og:title" content="ThatShouldBeAComic.com">
	<meta property="og:type" content="website">
	<meta property="og:image" content="http://www.thatshouldbeacomic.com/Giraffefinal.png">
	<meta property="og:url" content="http://www.thatshouldbeacomic.com">
	<meta property="og:site_name" content="That Should Be A Comic">
	<meta property="fb:admins" content="761118384,1038105034">
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
$email = $_SESSION['email'];
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>
<!--- Content-->
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div align="center">
<img src="tsbacIndex.jpg" usemap="#indexmap" width="1000" height="800" />
</div>
<map name="indexmap">
	 <!--<area shape="rect" coords="382,324,511,396" alt="Journal" href="journal.php">
     <area shape="rect" coords="624,425,735,497" alt="6th Period Adventures" href="period6.php">
     <area shape="rect" coords="656,107,812,245" alt="The Adventures of Dino girl!" id="dinogirl" href="dinogirl.php">
	 <area shape="rect" coords="399,91,561,171" alt="Burkhartigan Adventures" href="monstarthemonster.php">
     <area shape="rect" coords="458,534,572,608" alt="Ally Comics" href="ally.php">
	 <area shape="circ" coords="900,280,85" alt="Illustration!" href="illustrationMain.php">-->
	 <!--"830,265,935,335"--> 
</map>
<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript"> 
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

<?php require($DOCUMENT_ROOT . "indexmenu.html"); ?> 


</div>


<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>




<div id="comments">
</div>
<div id="lastupdate">
</div>