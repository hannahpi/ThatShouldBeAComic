<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>Chatter - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>
<?php
require_once 'config.php';
date_default_timezone_set("America/New_York");
$halt = $_GET['halt'];
$lastmsg = $_SESSION['lastMsg'];
$image = $_GET['image'];
$submit = $_POST['submit'];
$email = $_SESSION['email'];
$displayName = $_SESSION['displayName'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
if (!($displayName))
{  $displayName = strip_tags($_POST['DisplayName']); }
$message = strip_tags($_POST['Message']);
$date = date("Y-m-d H:i:s");
$headBack = $GLOBALS['FQP'] . "addchatter.php"; 
if ($headBack)
{
	$headBack = "Location: $headBack";
}

if (empty($lastMsg))
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE recipient IS NULL;";
}
else
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE MsgID > $lastmsg AND recipient IS NULL;";
}
$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("cannot connect!");
mysql_select_db($GLOBALS['DB_NAME']) or die ("Cannot find DB!");
$query = mysql_query($toSend) or die ("Unknown Retreive Query error 9998");
$numrows= mysql_num_rows($query);
if ($numrows>0)
{
	echo "\n<div id='newmsg'>";
	echo "\n<textarea id='log' rows='30' cols='100' name='log'>";
	while ($row = mysql_fetch_assoc($query))
	{
		$msgID = $row['MsgID'];
		$nick = $row['Nickname'];
		$time = $row['DateTime'];
		$msg = $row['Message'];
		echo "\n[$time] <$nick> $msg ";
	}
	echo "\n</textarea> </div>";
	if ($halt > 0)
	{
		die();
	}
}


if ($submit)
  {
	if ($email)
	{
		$toSend = "SELECT Email FROM `User` WHERE Email ='$email';";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");
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
		
   
	if  ($dbemail && $message)
	{
		$toSend = 
		   "INSERT INTO `chatter` 
		    VALUES (NULL, '$displayName','$email','$date','$message', NULL, NULL);";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");
		mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
		$query = mysql_query($toSend) or die("Unknown Query error!  9999");
		header($headBack);
	}
	else
		echo "<strong>Blank field detected!</strong>";
		
  }	

echo "<form action='addchatter.php' method='POST'>";


?>

<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript"> 
window.onload=function(){
    var timeoutIdMsg=0;
	timeoutIdMsg = setInterval( "getMessages()", 2000 );
	document.getElementById('log').scrollTop = document.getElementById("log").scrollHeight;
}
</script>


   <table>		
		<?php if ($email) echo "<!--"?><tr>
			<td>
				E-mail:
			</td>
			<td>
				<input type='text' name='Email'></input>
			</td>
		</tr><?php if ($email) echo "--> $email";?>
		<?php if ($displayName) echo "<!--"?><tr>
			<td>
				Nickname:
			</td>
			<td>
				<input type='text' name='DisplayName'></input>
			</td>
		<tr><?php if ($displayName) echo "--> $displayName";?>
			<td>
				Message:
			</td>
			<td>
				<textarea rows="2" cols="100" name='Message'></textarea>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' value='Send'></td>
		</tr>
	</table>	
</form>
		