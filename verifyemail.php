<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
require_once 'config.php';
$confirmNum = md5(strip_tags($_GET['confirmNum']));
$unenConfirm = strip_tags($_GET['confirmNum']);
$email = strip_tags($_GET['Email']);
$submit = $_POST['submit'];
$password= strip_tags($_POST['newpassword']);
$repassword = strip_tags($_POST['repeatpassword']);
$userLevelID = 0;


if ($confirmNum)
{
	$toSend = "SELECT Email, Password, UserLevelID FROM `User` WHERE Email =:email;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$stmt->execute(array(':email'=>$email)) or errormail($email,"Failed to execute query.  verifyemail.php","failed to check user","failed to get user info");
	$numrows = $stmt->rowCount();
	if ($numrows>0)
	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$dbemail = $row['Email'];
		$dbpassword = $row['Password'];
		$userLevelID = $row['UserLevelID'];
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
  if (($password == $repassword)&&($dbpassword == $confirmNum)&&(isset($password)))
  {
    $password=md5($password);
	$repassword=md5($repassword);

	if ($userLevelID < 2)
		$userLevelID = 2;  //confirmed email address

	//set password
	$toSend= "UPDATE `User` SET  `Password` =  :password, `UserLevelID` = :userLevelID WHERE  `User`.`Email` =  :dbemail;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$stmt->execute(array(':password'=>$password, 'userLevelID'=>$userLevelID, ':dbemail'=>$dbemail)) or errormail($dbemail,"Failed to execute query.  verifyemail.php","Update Password failed","Failed to update user information");
	$rowCt = $stmt->rowCount();
	if ($rowCt <= 0)
		errormail($dbemail, "Email not found verifyemail.php", "Email not found", "Email not found");


	//send email that confirmation is complete
	$headers = "From: " . $GLOBALS['AUTO_ADMIN_NAME'] . " " . $GLOBALS['AUTO_ADMIN_EMAIL'];
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
