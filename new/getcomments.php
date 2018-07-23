<?php
//PHP SCRIPT: getcomments.php
session_start();

$ses_email = $_SESSION['email'];
$fileName= $_GET['fileName'];
$order= strip_tags($_GET['order']);

if ($fileName)
{
	$toSend = "SELECT `comments`.CommentID, `comments`.CommentDate, `comments`.Comment, 
	           `User`.DisplayName, `User`.Email FROM `comments`,`User` 
	           WHERE `User`.Email = `comments`.Email
	           AND `comments`.FileName ='$fileName'
			   AND `User`.UserLevelID > 0
	           ORDER BY `comments`.CommentDate";
			   
	if ($order == "desc")
		$toSend .= "$order ;";
	else
		$toSend .= ";";
	
	$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'], $GLOBALS['DB_PASSWORD']) or die("Couldn't connect!");
	mysql_select_db($GLOBALS['DB_NAME']) or die ("couldn't find db!");
	$query = mysql_query($toSend);
	
	echo "\n <br />";	
	$numrows= mysql_num_rows($query);
	if ($numrows!=1)
	{
		echo "$numrows comments submitted <br />\n";
	}
	else
	{
		echo "$numrows comment submitted <br />\n";
	}
	
	if ($numrows>0)
	{		
		while ($row = mysql_fetch_assoc($query))
		{
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
			echo "</span> </td> </tr>";
			echo "\n</table><br />";
			
		}
	}
	else
	{
		echo "\nNo comments have been posted";
	}	
}

?>