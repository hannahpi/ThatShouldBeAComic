<?php
session_start();

$email = strip_tags($_POST['email']);
$password = md5(strip_tags($_POST['password']));

if ($email&&$password)
{
	$toSend = "SELECT `User`.Password, `User`.UserLevelID, `User`.DisplayName FROM `User`
	           WHERE `User`.Email = '$email';";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown User Query error!");
	$numrows = mysql_num_rows($query);
	
	if ($numrows==0)
	{
		die("No such email found!  <a href=\"../register.php\"> register? </a>");
	}
	else
	{
		//check passwords
		while ($row = mysql_fetch_assoc($query))
		{
			$dbpassword = $row['Password'];	
			$userlevel = $row['UserLevelID'];
			$displayName = $row['DisplayName'];			
		}
		
		if ($dbpassword==$password)
		{
			$_SESSION['email']=$email;
			$_SESSION['userlevel']=$userlevel;
			$_SESSION['displayName']=$displayName;
			echo "You're in!  <a href='member.php'>Click here</a> to enter the member page";
		}
		else
		{
			die("Invalid email or password");
		}
	}
}


?>