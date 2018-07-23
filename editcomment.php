<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>Edit Comment - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="images/getimages.php"></script>
</head>
<?php
//echo "<h1>Comment</h1>";
require_once 'config.php';
date_default_timezone_set("America/New_York");
$imgID = strip_tags($_GET['imgID']);
$image = strip_tags($_GET['image']);
$commentID= strip_tags($_GET['id']);
$submit = $_POST['submit'];
$email = $_SESSION['email'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
$comment = strip_tags($_POST['Comment']);
$date = strip_tags($_POST['date']);
if (!$date)
{  $date = date("Y-m-d H:i:s");  }
$goBack = $_SESSION['lastPage'];
if ($image)
{	$goBack .= "?image=$image"; }

/*echo "File: $imgID";
if ($imgID)
{ */
  if ($comment) {
	if ($email) {
		$toSend = "SELECT Email FROM `User` WHERE Email =:email;";
		//dbug helper:
			$message= "fileName: $imgID \n image: $image \n commentID: $commentID \n submit: $submit \n email: $email";
			$message.= "\n comment:$comment \n Date: $date  \n GoBack: $goBack \n Sending: \n $toSend";

        try {
            $connect = connect_tsbac();
            $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
            $stmt->execute(array(':email'=>$email))
                or errormail($email,"Failed to execute query.  editcomment.php","add comment failed, failed to get user info","failed to add comment");
            $connect=null; // disconnect
            $rowCt = $stmt->rowCount();
            if ($rowCt == 0)
                die("\nNo user created yet.  <a href=\"adduser.php\">Create user</a>");
            else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $dbemail = $row['Email'];
            }
        } catch (PDOException $e) {
        	errormail($email, $e->getMessage(), $message, $e->getMessage());
        }
	}
	else
		die("Email not entered");


	if  ($dbemail&&$comment)
	{
        $toSend = "UPDATE `comments` SET `Comment` = :comment , `CommentDate` = :date WHERE CommentID = :commentID ;";
        try {
            $connect = connect_tsbac();
            $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
            $stmt->execute(array(':comment'=>$comment, ':date'=>$date, ':commentID'=>$commentID ));
            $connect=null; // disconnect
            $rowCt = $stmt->rowCount();
            if ($rowCt == 0)
                die("<br><p style='margin-top:30px;'><strong> No changes were made! </strong> $date <a href=$goBack>Go back!</a><p>");
            else
                die("<br><p style='margin-top:30px;'><strong> Thanks!!! You successfully saved! </strong> $date <a href=$goBack>Go back!</a><p>");
        } catch (PDOException $e) {
        	errormail($email, $e->getMessage(), "No info", $e->getMessage());
        }
	}
	else
		echo "<strong>Blank field detected!</strong>";

  } //comment
else  //no filename
{
	$toSend = "SELECT `CommentDate`, `ImgID`, `Email`, `Comment` FROM comments
               WHERE Email = :email
			   AND CommentID= :commentID ;";
	//dbug helper:
			$message= "fileName: $imgID \n image: $image \n commentID: $commentID \n submit: $submit \n email: $email";
			$message.= "\n comment:$comment \n Date: $date  \n GoBack: $goBack \n Sending: \n $toSend";

	try {
        $connect = connect_tsbac();
        $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
        $stmt->execute(array(':commentID'=>$commentID, ':email'=>$email));
        $connect=null; // disconnect
    } catch (PDOException $e) {
        errormail($email, $e->getMessage(), "No info", $e->getMessage());
    }
    $rowCt = $stmt->rowCount();
    if ($rowCt == 0) {
        errormail($email, $message, "Unable to find comment, get filename returned 0 rows", "<br><p style='margin-top:30px;'><strong> Comment not found! </strong> <a href=$goBack>Go back!</a><p>");
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $date = $row['CommentDate'];
        $imgID = $row['ImgID'];
        $dbemail = $row['Email'];
        $comment = $row['Comment'];
    }
}

if ($image)
{
	echo "<form action='editcomment.php?imgID=$imgID&image=$image&id=$commentID' method='POST'>";
}
else
{
	echo "<form action='editcomment.php?imgID=$imgID&id=$commentID' method='POST'>";
}

?>
   <table>
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<?php if ($dbemail) echo "<!--"; ?><input type='text' name='Email'></input><?php if ($dbemail) echo "--> $email";?>
			</td>
		</tr>
		<?php if (!$date) echo "<!--"; ?>
		<tr>
			<td>
				Date:
			</td>
			<td>
				<input type="text" name="date" value=<?php echo "'$date'"; ?>></input>
			</td>
		</tr>
		<?php if (!$date) echo "-->"; ?>
		<?php if (!$comment) echo "<!--"; ?>
		<tr>
			<td>
				Comment:
			</td>
			<td>
				<textarea rows="5" cols="40" name='Comment'><?php echo "$comment"; ?></textarea>
			</td>
		</tr>
		<?php if (!$comment) echo "-->"; ?>
		<tr>
			<td></td><td><input type='submit' id='submit' name='submit' onclick="editSubmit(<?php echo $commentID; ?>); return false;" value='Save Changes'></td>
		</tr>
	</table>
</form>
