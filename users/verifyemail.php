<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>Verify Email - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>
<h1>Verify Email</h1>

<?php
session_start();
$confirmNum = md5(strip_tags($_GET['confirmNum']));
$unenConfirm = strip_tags($_GET['confirmNum']);
$email = strip_tags($_GET['Email']);
$submit = $_POST['submit'];
$password= strip_tags($_POST['newpassword']);
$repassword = strip_tags($_POST['repeatpassword']);


if ($confirmNum)
{
	$toSend = "SELECT Email, Password FROM `User` WHERE Email ='$email';";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown User Query error!");
		
	$numrows= mysql_num_rows($query);
	if ($numrows>0)
	{			
		while ($row = mysql_fetch_assoc($query))
		{
			$dbemail = $row['Email'];
			$dbpassword = $row['Password'];	
		}
	}
	else
	{
		echo "\nNo user created yet.  <a href=\"adduser.php\">Create user</a>";
	}
}

if ($dbpassword == $confirmNum)
{
	echo "Change password!<br>";
}
else
	die("Invalid Confirmation Number!");

if ($submit&&$dbemail)
{
  if (($password == $repassword)&&($dbpassword == $confirmNum))
  {
    $password=md5($password);
	$repassword=md5($repassword);
	
	//set password
	$toSend= "UPDATE  `dreamre2_comicReq`.`User` SET  `Password` =  '$password' WHERE  `User`.`Email` =  '$dbemail';";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown Update Query error!");
	//set user level
	$toSend= "UPDATE  `dreamre2_comicReq`.`User` SET  `UserLevelID` =  '2' 
	          WHERE  `User`.`Email` =  '$dbemail'
			  AND `User`.`UserLevelID` < 2;";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown Update Query error!");
		
	//send email with confirmation link
	$headers = "From: AutomatedAdmin ThatShouldBeAComic <no-reply@thatshouldbeacomic.com>";
	$subject = "Email address confirmed";
	$message = "Thank you for confirming your email address and setting up a new password!";
	mail($dbemail,$subject,$message,$headers);	
	echo "<br />Password set successfully! <br />";  //Todo: put a link here.
  }
  else
  {
	echo "Password mismatch<br />";
  }
}
else if ($submit)
{
	echo "Email not found<br />";
}
	
?>

<form action='verifyemail.php<?php echo "?confirmNum=$unenConfirm&Email=$dbemail"?>' method='POST'>
	<table>
		<tr>
			<td>
				New Password:
			</td>
			<td>
				<input type="password" name="newpassword"></input>
		</tr>
		<tr>
			<td>
				Repeat New Password:
			</td>
			<td>
				<input type="password" name="repeatpassword"></input>
		</tr>
	</table>
	<input type="submit" name="submit" value="Reset Password"></input>
</form>



