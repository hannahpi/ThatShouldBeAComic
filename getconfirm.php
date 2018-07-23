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
<h1>Verify Email (Get Confirmation Email)</h1>
<table> <tr> <td>This can also reset your password if you've forgotten it!</tr></td></table>
<br> <br>
<?php
require_once 'config.php';
$submit = $_POST['submit'];
$email = trim($_POST['Email']);

if ($submit&&$email)
{
	//get dbemail or die
	$toSend = "SELECT Email FROM `User` WHERE Email = :email;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$stmt->execute(array(':email'=>$email)) or errormail($email,"Failed to execute query.  getconfirm.php","Find account failed","Failed to retrieve user information");
	$numrows = $stmt->rowCount();

	if ($numrows>0)	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$dbemail = $row['Email'];
	} else {
		die("\nNo user for $email created yet.  <a href=\"adduser.php\">Create user</a>");
	}
	if(isset($_POST['Email']) && !empty($_POST['Email']))
	{
		if(substr_count($email,"@") != 1 || stristr($email," ") || stristr($email,"\\") || stristr($email,":")){$errors[] = "Email address is invalid";}else{$exploded_email = explode("@",$email);if(empty($exploded_email[0]) || strlen($exploded_email[0]) > 64 || empty($exploded_email[1])){$errors[] = "Email address is invalid";}else{if(substr_count($exploded_email[1],".") == 0){$errors[] = "Email address is invalid";}else{$exploded_domain = explode(".",$exploded_email[1]);if(in_array("",$exploded_domain)){$errors[] = "Email address is invalid";}else{foreach($exploded_domain as $value){if(strlen($value) > 63 || !preg_match('/^[a-z0-9-]+$/i',$value)){$errors[] = "Email address is invalid"; break;}}}}}}
	}
	if ($dbemail)
	{
		//send email with confirmation link
		$headers = "From: " . $GLOBALS['AUTO_ADMIN_NAME'] . " " . $GLOBALS['AUTO_ADMIN_EMAIL'];
		$subject = "Confirm your email address";
		$passGen = rand(1000000, 9999999);
		$message = "Please confirm your email address at ". $GLOBALS['FQP'] . "/verifyemail.php?confirmNum=$passGen&Email=$dbemail .
		            If you have problems you may go back to ". $GLOBALS['FQP'] . "/getconfirm.php and try again!";
		mail($dbemail,$subject,$message,$headers);

		//update database
		$passGen = md5($passGen);
		$toSend= "UPDATE `User` SET  `Password` =  :passGen WHERE  `User`.`Email` =  :dbemail;";
		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':passGen'=>$passGen,':dbemail'=>$dbemail)) or errormail($email,"Failed to execute query.  getconfirm.php","Update account failed","Failed to update user information");
		$numrows= $stmt->rowCount();
		if ($numrows == 1)
			die("Ok everything looks good go ahead and check your email!");
		else
			errormail($email, "getconfirm.php : update user", "query ok but update user did not work", "Failed to update your user account");
	}
}
?>

<form action="getconfirm.php" method="POST">
	<table>
		<tr>
			<td>
				E-Mail:
			</td>
			<td>
				<input type="text" name="Email"></input>
		</tr>
	</table>
	<input type="submit" name="submit" value="Send Email"></input>
</form>
