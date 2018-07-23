<?php 
session_start();
$headBack = $_SESSION['lastPage']; 
$email= $_SESSION['email'];
if ($headBack&&$email)
{
	$headBack = "Location: $headBack";
}
header($headBack);

?>

<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>
<?php
session_start();

$email = strip_tags($_POST['email']);
$password = md5(strip_tags($_POST['password']));
$submit = $submit = $_POST['submit'];
$goBack = $_SESSION['lastPage'];

function confirm($cemail)
{
	//send email with confirmation link
	$headers = "From: AutomatedAdmin ThatShouldBeAComic <no-reply@thatshouldbeacomic.com>";
	$subject = "Confirm your email address";
	$passGen = rand(1000000, 9999999);
	$message = "Please confirm your email address at http://www.thatshouldbeacomic.com/new/users/verifyemail.php?confirmNum=$passGen&Email=$cemail . 
	            If you have problems you may go back to http://www.thatshouldbeacomic.com/new/users/getconfirm.php and try again!";
	mail($cemail,$subject,$message,$headers);
		
	//update database
	$passGen = md5($passGen);
	$toSend= "UPDATE  `dreamre2_comicReq`.`User` SET  `Password` =  '$passGen' WHERE  `User`.`Email` = '$cemail';";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown Update Query error!");
		
	die("Ok everything looks good go ahead and check your email!  If you don't get anything <a href='users/getconfirm.php'>try again</a>");
}

if ($email&&$submit)
{
	$toSend = "SELECT `User`.Password, `User`.UserLevelID, `User`.DisplayName, `User`.UploadPath, `User`.Email FROM `User`
	           WHERE `User`.Email = '$email' OR `User`.DisplayName = '$email';";
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
			$uploadPath = $row['UploadPath'];
			$dbemail = $row['Email'];
			if ($dbemail != $email)
			{	$email = $dbemail; }
		}
		
		if ((!($dbpassword))&&$dbemail)
		{
			confirm($dbemail);
			die("<br />Email sent<br />");
		}
		
		if ($dbpassword==$password)
		{
			$_SESSION['email']=$email;
			$_SESSION['userlevel']=$userlevel;
			$_SESSION['displayName']=$displayName;
			//$_SESSION['loggedInName']=$displayName;
			$_SESSION['UploadPath']=$uploadPath;
			echo "<br />You're in $displayName!  <a href=$goBack>Click here</a> to go back!<br />";
		}
		else
		{
			die("Invalid email or password");
		}
	}
}


?>

<?php if ($submit) echo "<!--" ?>
<form action="login.php" method="POST">
<table>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="email"></input> </td>
	</tr>
	<tr>
		<td>Password: </td>
		<td><input type="password" name="password"></input> </td>
	</tr>
	<tr>
		<td></td><td><input type="submit" name="submit" value="Login"></td>
	</tr>
</table>
</form>
<?php if ($submit) echo "-->" ?>