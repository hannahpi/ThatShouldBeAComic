<?php
session_start();
$headBack = $_SESSION['lastPage'];
$email= $_SESSION['email'];
?>

<?php
require_once 'config.php';
$email = strip_tags($_POST['email']);
$password = md5(strip_tags($_POST['password']));
$submit = $_POST['submit'];
$goBack = $_SESSION['lastPage'];

function confirm($cemail)
{
	//send email with confirmation link
	$headers = "From: AutomatedAdmin ThatShouldBeAComic <no-reply@thatshouldbeacomic.com>";
	$subject = "Confirm your email address";
	$passGen = rand(1000000, 9999999);
	$message = "Please confirm your email address at ". $GLOBALS['FQP'] . "/verifyemail.php?confirmNum=$passGen&Email=$cemail .
	            If you have problems you may go back to ". $GLOBALS['FQP'] . "/getconfirm.php and try again!";
	mail($cemail,$subject,$message,$headers);

	//update database
	$passGen = md5($passGen);
	$toSend= "UPDATE  `User` SET  `Password` =  :pass WHERE  `User`.`Email` = :cemail;";
	$connect= connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS["PDO_ATTRIBS"]);
	$stmt->execute(array(":pass"=>$passGen, ":cemail"=>$cemail)) or errormail($cemail, "failed to update user info", "failed to confirm email");
	if ($stmt->rowCount()>0) {
		die("<table> <tr> <td>Ok everything looks good go ahead and check your email!  If you don't get anything <a href='getconfirm.php'>try again</a></td> </tr> </table>");
	} else {
		die("<table> <tr> <td>That didn't seem to work, you might want to try again or contact the admin </td> </tr> </table>");
	}
}

if ($email && $password)
{
	$toSend = "SELECT `User`.Password, `User`.UserLevelID, `User`.DisplayName, `User`.UploadPath, `User`.Email FROM `User`
	           WHERE `User`.Email = :email OR `User`.DisplayName = :email;";
	$connect= connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS["PDO_ATTRIBS"]);
	$stmt->execute(array(":email"=>$email)) or errormail($email, "failed get user info", "failed get user info");
	$numrows = $stmt->rowCount();

	if ($numrows==0)
	{
		die("<table> <tr> <td>No such email found!  <a href=\"../register.php\"> register? </a> </td> </tr> </table>");
	}
	else
	{
		//check passwords
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$dbpassword = $row['Password'];
		$userlevel = $row['UserLevelID'];
		$displayName = $row['DisplayName'];
		$uploadPath = $row['UploadPath'];
		$dbemail = $row['Email'];
		if ($dbemail != $email)
		{	$email = $dbemail; }

		if ((empty($dbpassword))&&$dbemail)
		{
			confirm($dbemail);
			die("<br />Email sent<br />");
		}

		if ($dbpassword==$password)
		{
			$_SESSION['email']=$email;
			$_SESSION['userlevel']=$userlevel;
			$_SESSION['displayName']=$displayName;
			$_SESSION['UploadPath']=$uploadPath;
			echo "<br /><table> <tr> <td> You're in $displayName!  <a href=$goBack>Click here</a> to go back!<br /></td> </tr> </table>";
		} else {
			die("<table> <tr> <td> Invalid email or password.  <br>If you've forgotten your password <a href='getconfirm.php'>click here</a> <br> <a href='#' onclick='cancelLogin(); login();'> Try again </a>!</td> </tr> </table>");
		}
	}
} else {
	if ($email)
	{
		echo "<table> <tr> <td> submit not detected </td> </tr> </table>" ;
	}
}


?>

<?php if ($email && $password) echo "<!--" ?>
<form id="frmLogin" action="">
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
		<td></td><td><input type="submit" id="submit" onclick="loginSubmit(); return false;" name="submit" value="Login" /></td>
	</tr>
</table>
</form>
<?php if ($email && $password) echo "-->" ?>
