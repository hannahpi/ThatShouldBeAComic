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
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" content="A website developed for a web comic form of blogging.  Users can upload their own comics.">
<meta name="keywords" content="online comics, webcomic, comication, dino web comic, dinogirl, That Should Be A Comic">
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
<script type="text/javascript" src="phpget.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
</head>
<body>

<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>

<?php
date_default_timezone_set("America/New_York");
require_once 'config.php';
$toSend = "SELECT `User`.`DisplayName`, "
			  ." ( SELECT LevelName FROM `UserLevel` WHERE `User`.UserLevelID = `UserLevel`.UserLevelID ) As `ULevelName` , "
			  ." ( SELECT Count(*) FROM `Images` WHERE `Images`.`Email` = `User`.Email ) As `NumSubmissions` , "
              ." ( SELECT Count(*) FROM `comments` WHERE `comments`.`Email` = `User`.Email ) AS `NumComments` "
	           ." FROM `User` WHERE `User`.`UserLevelID` > 0 "
			   ." ORDER BY `NumSubmissions` DESC, `NumComments` DESC ;";

$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
$stmt->execute();
$rows = $stmt->fetchAll();
$totnumrows = $stmt->rowCount();

if ($totnumrows >0)
{
	echo "<table border='1' style='border-color:#EC008C; margin-left:auto; margin-right:auto; margin-top:5%;'> ";
	foreach ($rows as $row)
	{
		$userDisplay = $row['DisplayName'];
		$ulvlName = $row['ULevelName'];
		$submissionCt = $row['NumSubmissions'];
		$commentCt = $row['NumComments'];

		if ($userDisplay=="Guest")
			continue;

		echo "<tr>";
		echo "<td> <a href='". $GLOBALS['FQP'] . "/userView.php?display=" .$userDisplay . "'> " . $userDisplay . " </a> </td>";
		echo "<td> <span class='desc'>" . $ulvlName . " </span> </td> " ;
		echo "<td> <span class='desc'>" . $submissionCt . " submissions </span> </td> " ;
		echo "<td> <span class='desc'>" . $commentCt . " comments </span> </td> " ;
		echo "</tr>";
	}
	echo "</table>";
}
else
{
	echo "<table> <tr> <td> No users found! </td> </tr> </table>";
}

?>

<?php require($DOCUMENT_ROOT . "mainmenu.html"); ?>
