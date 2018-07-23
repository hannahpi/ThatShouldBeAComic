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
require 'getseries.php';

function date_diff_y($date1, $date2) {
    $current = $date1;
    $datetime2 = date_create($date2);
    $count = 0;
    while(date_create($current) < $datetime2){
        $current = gmdate("Y-m-d", strtotime("+1 year", strtotime($current)));
        $count++;
    }
    return $count-1;
}

$email = $_SESSION['email'];
$displayName = $_GET['display'];
$curUser = $_SESSION['displayName'];
if ($curUser==$displayName)
{
	$page = "editimg.php";
}
else
{
	$page = "showimg.php";
}
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$toSend = "SELECT `DisplayName`, `Birthdate`, `Location`, `AboutMe`, `Interests`, `School` FROM `bio`,`User`
	           WHERE `User`.DisplayName = :displayName AND `User`.Email = `bio`.Email ;";

$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$stmt->execute(array(':displayName'=>$displayName));
$totnumrows = $stmt->rowCount();
if ($totnumrows == 1)
{
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$displayName = $row['DisplayName'];
	$today = date("Y-M-d");
	$age = date_diff_y($row['Birthdate'], $today) ;
	$location = $row['Location'];
	$aboutMe = $row['AboutMe'];
	$interests = $row['Interests'];
	$school = $row['School'];
	echo '<table id="bio">';
	echo '<tr> <td> User </td> <td> <br> ' . $displayName .' </td> </tr>';
	echo '</table><br>';
	if (isset($age))
	{
		echo '<table id="bio">';
		echo '<tr> <td> Age </td> <td><br> ' . $age . ' </td> </tr>';
		echo '</table><br>';
	}
	if ((isset($location)) && ($location != ''))
	{
		echo '<table id="bio">';
		echo '<tr> <td> Location </td><td><br> '. $location .' </td> </tr>';
		echo '</table><br>';
	}
	if ((isset($aboutMe)) && ($aboutMe != ''))
	{
		echo '<table id="bio">';
		echo '<tr> <td> About Me </td><td style="max-width:350px;"><br> '. $aboutMe .' </td> </tr>';
		echo '</table><br>';
	}
	if ((isset($interests)) && ($interests != ''))
	{
		echo '<table id="bio">';
		echo '<tr> <td> Interests </td><td style="max-width:350px;"><br> '. $interests .' </td> </tr>';
		echo '</table><br>';
	}
	if ((isset($school)) && ($school != ''))
	{
		echo '<table id="bio">';
		echo '<tr> <td> School </td><td style="max-width:350px;"><br> '. $school .' </td> </tr>';
		echo '</table><br>';
	}
}
else
{
	echo "<table> <tr> <td> No bio page found. </td> </tr> </table>";
}

findSeries($displayName);
$toSend = "SELECT `Images`.`ImgID`, `Images`.`Name`, `Images`.`Desc`, `Images`.`Date` FROM `Images`,`User` "
	           ." WHERE `User`.DisplayName = :displayName "
			   ." AND `User`.Email = `Images`.Email "
			   ." AND `Images`.Anonymous = 0 "
			   ." AND (`Images`.`FileName` LIKE '%.jpg' "
			   ."      OR `Images`.`FileName` LIKE '%.png' "
			   ."      OR `Images`.`FileName` LIKE '%.bmp') "
			   ." ORDER BY `Images`.Date DESC ;";
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$stmt->execute(array(":displayName"=>$displayName));
$rows= $stmt->fetchAll();
$totnumrows = $stmt->rowCount();
if ($totnumrows >0)
{
	echo "<table border='1' style='border-color:#EC008C;'> ";
	foreach ($rows as $row)
	{
		$imgID = $row['ImgID'];
		$imgName = $row['Name'];
		$filedesc = $row['Desc'];
		$date = $row['Date'];
		$date = date("M d Y h:i:s A.",strtotime($date));
		if (empty($imgName))
		{
			continue;
		}

		while ($filedesc[0] == '@')
		{
			$ct = 0;
			// finds the index
			while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));

			$filedesc=substr_replace($filedesc,"",0,$ct);
		}
		echo "<tr>";
		echo "<td> <a href='" . $GLOBALS['FQP'] . "/$page?image=" .$imgID . "'> " . $imgName . " </a> </td>";
		echo "<td style='max-width:200px'> <span class='desc'>" . $filedesc . " </span> </td> " ;
		echo "<td> <span class='small' style='font-weight:bold;'> " . $date . " </span> </td> " ;
		echo "</tr>";
	}
	echo "</table>";
}
else
{
	echo "<table> <tr> <td> No works submitted </td> </tr> </table>";
}

if (isset($email))
{
	echo "<br><table> <tr> <td> <a href='inbox.php?fillTo=$displayName'>Send me a message</a> !</td></tr></table><br><br>";
}
echo "<br> <br> <br>";
?>

<?php require($DOCUMENT_ROOT . "mainmenu.html"); ?>
