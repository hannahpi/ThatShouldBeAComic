<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>Edit Account - ThatShouldBeAComic</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link type="text/css" href="jquery/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>
<h1>Edit Your Account</h1>

<?php
require_once 'config.php';
date_default_timezone_set("America/New_York");
$email= $_SESSION['email'];
$displayName= $_SESSION['displayName'];
$submit = $_POST['submit'];
if ($submit)
	$dbemail = strip_tags($_POST['Email']);

if (!isset($dbemail))
{
	$toSend = "SELECT Email, DisplayName, FirstName, LastName, Password FROM `User` WHERE `User`.Email = :email ;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(':email'=>$email)) or errormail($email,$message,"failed to check user (edituser.php)","failed to get user info");
	$numrows = $stmt->rowCount();
	if ($numrows==0)
	{
		die("Oddly enough we couldn't find your user account!?");
	}
	else
	{
		$row= $stmt->fetch(PDO::FETCH_ASSOC);
		$dbemail = $row['Email'];
		$dbdisplayName = $row['DisplayName'];
		$dbfirstName = $row['FirstName'];
		$dblastName = $row['LastName'];
		$dbpassword = $row['Password'];

		$toSend = "SELECT `DisplayName`, `Birthdate`,  `Location`, `AboutMe`, `Interests`, `School` "
		          ." FROM `bio`,`User` WHERE `User`.`Email` = :dbemail AND `User`.Email = `bio`.Email; " ;
		try {
			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute(array(':dbemail'=>$dbemail));
			$numrows = $stmt->rowCount();
		} catch (PDOException $e) {
			errormail($email, $e->getMessage(), "error in edituser.php", $e->getMessage());
		} finally {
			if ($numrows==0) {  // Failed to get biographical information, create blank.
				$toSend = "INSERT INTO `bio` VALUES ( NULL, :dbemail, NULL, NULL, NULL, NULL, NULL ) ; " ;
				$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
				$stmt->execute(array(':dbemail'=>$dbemail)) or errormail($email, "unable to initialize bio information edituser.php", "couldn't initialize", "Couldn't initialize biographical information");
			} else {  //get the bio info
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$dbdisplayName = $row['DisplayName'];
				$dbbirthdate = $row['Birthdate'];
				$dblocation = $row['Location'];
				$dbaboutme = $row['AboutMe'];
				$dbinterests = $row['Interests'];
				$dbschool = $row['School'];
			}
		}
	}
}
$firstName= strip_tags($_POST['firstName']);
$lastName= strip_tags($_POST['lastName']);
$displayName= strip_tags($_POST['displayName']);
$dbpassword = strip_tags($_POST['dbpassword']);
$password = strip_tags($_POST['password']);
$newpassword = strip_tags($_POST['newpassword']);
$birthdate = strip_tags($_POST['Birthdate']);
$location = strip_tags($_POST['Location']);
$aboutme = strip_tags($_POST['AboutMe']);
$interests = strip_tags($_POST['Interests']);
$school = strip_tags($_POST['School']);
$confirmpassword = strip_tags($_POST['confirmpassword']);
$goBack = $_SESSION['lastPage'];

if ($submit)
{
	if ($dbemail&&$submit)
	{
		if (($newpassword == $confirmpassword) && (isset($newpassword)))
		{
			$toSend = "SELECT Password FROM `User` WHERE `User`.Email = :email ;";
			$connect = connect_tsbac();
			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute(array(':email'=>$email)) or errormail($email,$message,"failed to check user (edituser.php)","failed to get user info");
			$numrows = $stmt->rowCount();
			if ($numrows==0) {
				die("Oddly enough we couldn't find your user account!?");
			} else {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$dbpassword = $row['Password'];
			}

			$newpassword = md5($newpassword);
			$password = md5($password);
			if ($password==$dbpassword)
			{
				echo "\n Updating password \n";
				$setpassword = $newpassword;
			} else {
				//$setpassword = $dbpassword;
			}
		}
		try
		{
			$connect = connect_tsbac();
			$toSend = "UPDATE `User`,`bio` SET `User`.`DisplayName` = :displayName , `User`.`FirstName` = :firstName, "
			        . "`User`.`LastName` = :lastName, `User`.Password = :setpassword, `bio`.`Birthdate` = :birthdate, "
					. "`bio`.`Location` = :location, `bio`.`AboutMe` = :aboutme, `bio`.`Interests` = :interests , "
					. "`bio`.`School` = :school WHERE User.`Email` = :dbemail AND `User`.`Email` = `bio`.`Email`; ";
					//dbug helper:
						$message= "displayName: $displayName \n firstName: $firstName \n lastName: $lastName \n submit: $submit \n email: $dbemail";
						$message.= "\nbirthdate: $birthdate \n location: $location \n About Me: $aboutme \n Interests: $interests \n School: $school ";
						$message.= "\n comment:$comment \n GoBack: $goBack \n Sending: \n $toSend in edituser.php";

			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute(array(':displayName'=>$displayName, ':firstName'=>$firstName, ':lastName'=>$lastName, ':setpassword'=>$setpassword, ':birthdate'=>$birthdate, ':location'=>$location, ':aboutme'=>$aboutme, ':interests'=>$interests, ':school'=>$school, ':dbemail'=>$dbemail ));
			$numrows = $stmt->rowCount();
			if ($numrows == 0)
				die("<br><p style='margin-top:30px;'><strong> No changes were made! </strong> $date <a href=$goBack>Go back!</a><p>");
			$connect=null; // disconnect
		} catch (PDOException $e) {
			errormail($email, $message, $e->getMessage(), "Error updating user information");
		}
		die("<br><p style='margin-top:30px;'><strong> Thanks!!! You successfully saved! </strong> $date <a href=$goBack>Go back!</a><p>");
	}
	else
		echo "<strong>Blank field detected!</strong>";
}

?>

<form action='edituser.php' method='POST'>
   <table>
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<input type='hidden' name='Email' <?php if ($dbemail) echo "value='$dbemail'";?>></input><?php if ($dbemail) echo "$dbemail";?>
			</td>
		</tr>
		<tr>
			<td>
				Display Name*:
			</td>
			<td>
				<input type='text' name='displayName' <?php if ($dbdisplayName) echo "value='$dbdisplayName'"?>></input>
			</td>
		</tr>
		<tr>
			<td>
				First Name:
			</td>
			<td>
				<input type='text' name='firstName' <?php if ($dbfirstName) echo "value='$dbfirstName'"?>></input>
			</td>
		</tr>
		<tr>
			<td>
				Last Name:
			</td>
			<td>
				<input type='text' name='lastName' <?php if ($dblastName) echo "value='$dblastName'"?>></input>
			</td>
		</tr>
		<tr>
			<td>
				If changing passwords make sure to fill this out:
				<br />Old Password:
			</td>
			<td>
				<input type='password' name='password'></input>
			</td>
		</tr>
		<tr>
			<td>
				New Password:
			</td>
			<td>
				<input type='password' name='newpassword'></input>
			</td>
		</tr>
		<tr>
			<td>
				Confirm Password:
			</td>
			<td>
				<input type='password' name='confirmpassword'></input>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' value='Save Changes'></td>
		</tr>
	</table>
	<br>
	<table>
		<tr>
			<td>Bio Page Info: </td>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				Birthdate:
			</td>
			<td>
				<input type='text' name='Birthdate' id='Birthdate' <?php if ($dbbirthdate) echo "value='$dbbirthdate'" ?>></input>
			</td>
		</tr>
		<tr>
			<td>
				Location:
			</td>
			<td>
				<input type='text' name='Location' <?php if ($dblocation) echo "value='$dblocation'" ?>></input>
			</td>
		</tr>
		<tr>
			<td>
				About Me:
			</td>
			<td>
				<textarea rows="4" cols="100" name='AboutMe'><?php if ($dbaboutme) echo "$dbaboutme" ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				Interests:
			</td>
			<td>
				<textarea rows="4" cols="100" name='Interests'><?php if ($dbinterests) echo "$dbinterests" ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				School:
			</td>
			<td>
				<textarea rows="4" cols="100" name='School'><?php if ($dbschool) echo "$dbschool" ?></textarea>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' value='Save Changes'></td>
		</tr>
	</table>
</form>
<script>
	$(function() {
		$( "#Birthdate" ).datepicker({ dateFormat: 'yy-mm-dd' });;
	});
	$.datepicker.setDefaults({
		showOn: 'both',
		buttonImageOnly: true,
		buttonImage: 'jquery/images/calendar.gif',
		changeYear: true,
		yearRange: '-120:+0',
		buttonText: 'Calendar' });
</script>
