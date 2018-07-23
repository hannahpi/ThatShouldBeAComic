<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>Create User - ThatShouldBeAComic</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamersnet.net/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="images/getimages.php"></script>
</head>
<h1>Add User</h1>

<?php
require_once 'config.php';
$firstName= strip_tags($_POST['firstName']);
$lastName= strip_tags($_POST['lastName']);
$displayName= strip_tags($_POST['displayName']);
$submit = $_POST['submit'];
$email = strip_tags($_POST['Email']);
//$date = date("Y-m-d");

function confirm($email)
{
	//send email with confirmation link
	$headers = "From: " . $GLOBALS['AUTO_ADMIN_NAME'] . " " . $GLOBALS['AUTO_ADMIN_EMAIL'];
	$subject = "Confirm your email address";
	$passGen = rand(1000000, 9999999);
	$message = "Please confirm your email address at ". $GLOBALS['FQP'] . "/verifyemail.php?confirmNum=$passGen&Email=$email .
	            If you have problems you may go back to ". $GLOBALS['FQP'] . "/getconfirm.php and try again!";
	mail($email,$subject,$message,$headers);

	//update database
	$passGen = md5($passGen);
	$toSend= "UPDATE `User` SET  `Password` =  :passGen WHERE  `User`.`Email` =  :email;";
	    //dbug helper:
	    $message = "firstName: $firstName \n lastName: $lastName \n displayName: $displayName \n submit: $submit \n email: $email \n
	                passGen: $passGen \n toSend: $toSend \n";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(':passGen'=>$passGen,':email'=>$email)) or errormail($email,$message,"failed to check user","failed to get user info");
	$numrows = $stmt->rowCount();

	if ($numrows==1)
		die("Ok everything looks good go ahead and check your email!  If you don't get anything <a href='getconfirm.php'>try again</a>");
	else
		errormail($email, $message, "Link sent but might not have updated in DB", "Confirmation link sent in email.  Try it and report a problem if it doesn't work!");
}



if ($submit) {
	if ($email&&$submit) {
			$toSend = "SELECT Email FROM `User` WHERE Email ='$email';";
				    //dbug helper:
					$message = "firstName: $firstName \n lastName: $lastName \n displayName: $displayName \n submit: $submit \n email: $email \n
								passGen: $passGen \n toSend: $toSend \n";
			$connect = connect_tsbac();
			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute(array(':passGen'=>$passGen,':email'=>$email)) or errormail($email, $message, "submit, email check: Unknown Query error!","Unknown Query Error.  This has been reported to admin.");
			$numrows = $stmt->rowCount();

			if ($numrows>0) {
				//dbug helper:
				$message = "firstName: $firstName \n lastName: $lastName \n displayName: $displayName \n submit: $submit \n email: $email \n
							passGen: $passGen \n toSend: $toSend \n";
				errormail($email, $message, "email in use error", "Email already in use.  You may need to email the webmaster!  parkerbl@gmail.com");
			} else {
				//we need to create the user!
				if ($displayName)
				{
					$toSend =
						"INSERT INTO `User`
						VALUES (:email, :displayName, :firstName, :lastName, NULL, '1', NULL);";
						    //dbug helper:
							$message = "firstName: $firstName \n lastName: $lastName \n displayName: $displayName \n submit: $submit \n email: $email \n
										passGen: $passGen \n toSend: $toSend \n";

					$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
					$stmt->execute(array(':email'=>$email,'displayName'=>$displayName,':firstName'=>$firstName, ':lastName'=>$lastName)) or errormail($email, $message, "submit, email check: Unknown Query error!","Unknown Query Error.  This has been reported to admin.  You may want to contact the admin.");
					$numrows = $stmt->rowCount();

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
