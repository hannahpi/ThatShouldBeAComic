<?php
$cache_expire = 60*60*24*365;
 header("Pragma: public");
 header("Cache-Control: max-age=".$cache_expire);
 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>ThatShouldBeAComic.com</title>
<head>

<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" content="A website developed for a web comic form of blogging.  Users can upload their own comics.">
</head>

<?php
$email = $_SESSION['email'];
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];



?>
<!--- Content-->
<body>
<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div align="center">
<img src="ellieillie.jpg" usemap="#illumap" height="1000" />
</div>
<map name="illumap">
	 <area shape="circ" coords="330,95,100" alt="Sketchbook" id="sketch" href="sketch.php">
     <area shape="circ" coords="462,135,100" alt="Illustrations" id="illus" href="illustrations.php">
	 <area shape="circ" coords="214,436,100" alt="Artist's Statement" id="artist" href="artist-statement.jpg">
     <!--<area shape="circ" coords="628,398,100" alt="Story books" id="storybook" href="goodbyetree.php">-->
	 <area shape="circ" coords="800,452,100" alt="Contact Me" href="colorTheoryBack.jpg">
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
