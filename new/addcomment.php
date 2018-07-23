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
<?php
echo "<h1>Comment</h1>";
session_start();
$fileName= strip_tags($_GET['fileName']);
$image = $_GET['image'];
$submit = $_POST['submit'];
$email = $_SESSION['email'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
$comment = strip_tags($_POST['Comment']);
$date = date("Y-m-d H:i:s");
$goBack = $_SESSION['lastPage'];
if ($image)
{	$goBack .= "?image=$image"; }

echo "File: $fileName";
if ($fileName&&($fileName!="update"||$email=="parkerbl@gmail.com"))
{
  if ($submit)
  {
	if ($email)
	{
		$toSend = "SELECT Email FROM `User` WHERE Email ='$email';";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
		mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
		$query = mysql_query($toSend) or die("Unknown User Query error!");
		
		$numrows= mysql_num_rows($query);
		if ($numrows>0)
		{		
		
			while ($row = mysql_fetch_assoc($query))
			{
				$dbemail = $row['Email'];			
			}
		}
		else
		{
			echo "\nNo user created yet.  <a href=\"adduser.php\">Create user</a>";
		}
	}
	else
		die("Email not entered");
		
   
	if  ($dbemail && $comment)
	{
		$toSend = 
		   "INSERT INTO `comments` 
		    VALUES (NULL, '$fileName','$date','$email','$comment');";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
		mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
		$query = mysql_query($toSend) or die("Unknown Query error!");
		
		die("<br /><strong> Thanks!!! You successfully posted! </strong> <br /> $dbemail on $date <br /> <a href=$goBack>Go back!</a>");
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
	echo "<form action='addcomment.php?fileName=$fileName&image=$image' method='POST'>";
}
else
{
	echo "<form action='addcomment.php?fileName=$fileName' method='POST'>";
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
			<td></td><td><input type='submit' name='submit' value='Comment'></td>
		</tr>
	</table>	
</form>
		