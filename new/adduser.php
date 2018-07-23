<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>Comment - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="images/getimages.php"></script>
</head>
<h1>Add User</h1>

<?php

$firstName= strip_tags($_POST['firstName']);
$lastName= strip_tags($_POST['lastName']);
$displayName= strip_tags($_POST['displayName']);
$submit = $_POST['submit'];
$email = strip_tags($_POST['Email']);
//$date = date("Y-m-d");

function confirm($email)
{
	//send email with confirmation link
	$headers = "From: AutomatedAdmin ThatShouldBeAComic <no-reply@thatshouldbeacomic.com>";
	$subject = "Confirm your email address";
	$passGen = rand(1000000, 9999999);
	$message = "Please confirm your email address at http://www.thatshouldbeacomic.com/new/users/verifyemail.php?confirmNum=$passGen&Email=$email . 
	            If you have problems you may go back to http://www.thatshouldbeacomic.com/new/users/getconfirm.php and try again!";
	mail($email,$subject,$message,$headers);
		
	//update database
	$passGen = md5($passGen);
	$toSend= "UPDATE  `dreamre2_comicReq`.`User` SET  `Password` =  '$passGen' WHERE  `User`.`Email` =  '$email';";
	$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
	mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
	$query = mysql_query($toSend) or die("Unknown Update Query error!");
		
	die("Ok everything looks good go ahead and check your email!  If you don't get anything <a href='users/getconfirm.php'>try again</a>");
}



if ($submit)
{
	if ($email&&$submit)
		{
			$toSend = "SELECT Email FROM `User` WHERE Email ='$email';";
			$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
			mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
			$query = mysql_query($toSend) or die("Unknown User Query error!");
		
			$numrows= mysql_num_rows($query);
			if ($numrows>0)
			{				
				die("Email already in use.  You may need to email the webmaster!  parkerbl@gmail.com  (TODO: add form for this)");
			}
			else
			{
				//we need to create the user!
				if ($displayName)
				{
					$toSend = 
						"INSERT INTO `User` 
						VALUES ('$email','$displayName','$firstName','$lastName',NULL,'1',NULL);";
					$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
					mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
					$query = mysql_query($toSend) or die("Unknown Insert Query error!");
		
					$backto="index.php";
					confirm($email);
					die("<strong> Thank you $firstName $lastName!!! You successfully created $displayName! </strong> <br /> Make sure to remember: $email is the email registered! <br /> <a href=$backto>Go back!</a>");
						
				}
				else
				{
					die("<strong>You MUST enter a name for the site to show (Display Name)</strong>");
				}
			}
		}
	else
		die("Email not entered");
}


?>

<form action='adduser.php' method='POST'>
   <table>		
		<tr>
			<td>
				E-mail*:
			</td>
			<td>
				<input type='text' name='Email'></input>
			</td>
		</tr>
		<tr>
			<td>
				Display Name*:
			</td>
			<td>
				<input type='text' name='displayName'></input>
			</td>
		</tr>
		<tr>
			<td>
				First Name:
			</td>
			<td>
				<input type='text' name='firstName'></input>
			</td>
		</tr>
		<tr>
			<td>
				Last Name:
			</td>
			<td>
				<input type='text' name='lastName'></input>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' value='Add User'></td>
		</tr>
	</table>	
</form>