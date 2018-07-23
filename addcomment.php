<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>Comment - ThatShouldBeAComic</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>
<?php
session_start();
require_once 'config.php';
date_default_timezone_set("America/New_York");
$imgID= strip_tags($_GET['imgID']);
$image = $_GET['image'];
$submit = $_POST['submit'];
$email = $_SESSION['email'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
$tmp11 = $_POST['comment'];
$comment = strip_tags($_POST['Comment']);
$date = date("Y-m-d H:i:s");
$goBack = $_SESSION['lastPage'];
if ($image)
{	$goBack .= "?image=$image"; }
$headBack = $_SESSION['lastPage'];
if ($headBack)
{
	$headBack = "Location: $headBack";
}


if ($imgID)
{
  if ($comment)
  {
	if ($email)
	{
		$toSend = "SELECT Email FROM `User` WHERE Email =:email;";
		try {
			$connect = connect_tsbac();
			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute(array(':email'=>$email))
			 	or errormail($email,"Failed to execute query.  addcomment.php","add comment failed, failed to get user info","failed to add comment");;
			$connect=null; // disconnect
			$rowCt = $stmt->rowCount();
			if ($rowCt == 0)
				die("\nNo user created yet.  <a href=\"adduser.php\">Create user</a>");
			else {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$dbemail = $row['Email'];
			}
		} catch (PDOException $e) {
			errormail($email, $e->getMessage(), "No info", $e->getMessage());
		}
	}
	else
		die("Email not entered");


	if  ($dbemail && $comment)
	{
		$toSend = "INSERT INTO `comments` VALUES (NULL, :imgID , :date , :email , :comment );";
		try {
			$connect = connect_tsbac();
			$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
			$stmt->execute( array (':imgID'=>$imgID, ':date'=>$date, ':email' => $email, ':comment'=>$comment))
			 	or errormail($email,"Failed to execute query.  addcomment.php","add comment failed","failed to add comment");;
			$connect=null; // disconnect
			$rowCt = $stmt->rowCount();
			if ($rowCt > 0)
				die("Comment submitted");
			else
				errormail($email, "Query executed ok, no rows updated", "Comment submit failed", "Failed to add comment");
		} catch (PDOException $e) {
			errormail($email, $e->getMessage(), "No info", $e->getMessage());
		}
	}
	else
		echo "<strong>Blank field detected!</strong>";

  }
}
else
{
	echo "<br />Unknown file, internal error occured!  <a href=$goBack>Go back</a> and try again!";
}

if ($image)
{
	echo "<form id='form' action='addcomment.php?imgID=$imgID&image=$image' method='POST'>";
}
else
{
	echo "<form id='form' action='addcomment.php?imgID=$imgID' method='POST'>";
}

?>
   <table>
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<?php if ($email) echo "<!--"?><input type='text' name='Email'></input><?php if ($email) echo "--> $email";?>
			</td>
		</tr>
		<tr>
			<td>
				Comment:
			</td>
			<td>
				<textarea rows="5" cols="30" name='Comment'></textarea>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' id='submit' name='submit' onclick="commentSubmit(); return false;" value='Comment'></td>
		</tr>
	</table>
</form>
