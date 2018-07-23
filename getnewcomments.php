<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />

<?php
//PHP SCRIPT: getcomments.php
session_start();
require_once 'config.php';

$step = 5;
$ses_email = $_SESSION['email'];
$fileName= strip_tags($_GET['fileName']);
$order= strip_tags($_GET['order']);
$page = strip_tags($_GET['page']);
$goBack = $_SESSION['lastPage'];



	$toSend = "SELECT `comments`.ImgID, `comments`.CommentID, `comments`.CommentDate, `comments`.Comment, "
	          ." `User`.DisplayName, `User`.Email FROM `comments`,`User` "
	          ." WHERE `User`.Email = `comments`.Email "	          
			  ." AND `User`.UserLevelID > 0 "
	          ." ORDER BY `comments`.CommentDate DESC ";
	
	$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'], $GLOBALS['DB_PASSWORD']) or die("Couldn't connect!");
	mysql_select_db($GLOBALS['DB_NAME']) or die ("couldn't find db!");
	$query = mysql_query($toSend) or die ("Query problem");
	
	echo "\n <br />";	
	$totnumrows = mysql_num_rows($query);
	
	
	if ($totnumrows!=1)
	{
		echo "$totnumrows comments submitted <br />\n";
	}
	else
	{
		echo "$totnumrows comment submitted <br />\n";
	}
	
	if (($totnumrows>0)&&($totnumrows<=5))
	{
		while ($row = mysql_fetch_assoc($query))
		{
			$imgID = $row['ImgID'];
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];
					
			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			echo "\n<br /><span class=\"username\"> $displayName on $date";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='editcomment.php?id=$commentid'>edit</a>"; }
			echo "</span><span class='small'><a href='showimg.php?image=$imgID'>view comment</a></span> </td> </tr>";
			echo "\n</table></a><br />";
			
		}
	}
	else if ($totnumrows>5)
	{
		/*echo "\n<table class=\"comment\" border=\"0\">";
		echo "\n<tr>"; */
		$toSend .= "LIMIT 0 , 5";
		$page = ceil($totnumrows/5);
		
		$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'], $GLOBALS['DB_PASSWORD']) or die("Couldn't connect!");
		mysql_select_db($GLOBALS['DB_NAME']) or die ("couldn't find db!");
		$query = mysql_query($toSend) or die("Comment with limit query problem!  Couldn't send $toSend");
		
		$numrows= mysql_num_rows($query);
		
		
	/*	for ($i=0; $i<$totnumrows; $i+=$step)
		{
			echo "\n<td>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\">>>></span>";
			echo "<a href='getcomments.php?page=" . ($i/$step+1) . "&imgID=$imgID'>Page ". ($i/$step+1) ."</a>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\"><<<</span>";
			echo "\n</td>";
		}
		echo "\n</tr>";
		echo "\n</table><br />"; */
		
		while ($row = mysql_fetch_assoc($query))
		{
			$imgID = $row['ImgID'];
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];
					
			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			echo "\n<span class=\"username\"><br /> $displayName on $date";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='editcomment.php?id=$commentid'>edit</a>"; }
			echo "</span><span class='small'><a href='showimg.php?image=$imgID'>view comment</a></span> </td> </tr>";
			echo "\n</table><br />";
		}				
	}
	else
	{
		echo "\nNo comments have been posted";
	}	


?>