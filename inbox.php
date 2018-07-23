<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<head>
<?php
session_start();
$headBack = $_SESSION['lastPage'];
$email = $_SESSION['email'];
$seeAll = $_GET['seeAll'];
if (empty($email))
{
	if ($headBack)
	{
		$headBack = "Location: $headBack";
		header($headBack);
	}
}
?>
<title>Inbox - ThatShouldBeAComic</title>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25075932-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>
<?php
date_default_timezone_set("America/New_York");
require_once 'config.php';
$halt = $_GET['halt'];
if ($halt)
{
	$lastmsg = $_SESSION['lastInbox'];
}
$image = $_GET['image'];
$submit = $_POST['submit'];
$email = $_SESSION['email'];
$displayName = $_SESSION['displayName'];
$toRec = strip_tags($_POST['toRecipient']);
$fillTo = strip_tags($_GET['fillTo']);
if (!($email))
{  $email = strip_tags($_POST['Email']); }
if (!($displayName))
{  $displayName = strip_tags($_POST['DisplayName']); }
$message = strip_tags($_POST['Message']);
$date = date("Y-m-d H:i:s");
$headBack = $GLOBALS['FQP'] . "/inbox.php";
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if ($headBack) {
	$headBack = "Location: $headBack";
}

if (empty($seeAll)) {
	$readsql = "AND ( NOT `read` OR `read` IS NULL) ";
}

if (empty($lastmsg)) {
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message`, `read` FROM `chatter` WHERE `recipient` = :displayName $readsql;";
	$lastmsg=0;
} else {
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message`, `read` FROM `chatter` WHERE `MsgID` > :lastmsg AND `recipient` = :displayName $readsql;";
}

$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$stmt->bindValue(':displayName', $displayName, PDO::PARAM_STR);
if ($lastmsg > 0)
	$stmt->bindValue(':lastmsg', $lastmsg, PDO::PARAM_INT);
$stmt->execute() or errormail($email,"Failed to check inbox -- inbox.php","inbox check failed","failure to get inbox");
$numrows = $stmt->rowCount();
$rows = $stmt->fetchAll();
if ($numrows>0)
{
	foreach ($rows as $row)
	{
		echo "<p>";
		$msgID = $row['MsgID'];
		echo "\n<table id='message$msgID'>";
		$nick = $row['Nickname'];
		$link = '<a href="inbox.php?fillTo='. $nick .'">';
		$nick = $link . $nick . '</a>';
		$time = $row['DateTime'];
		$msg = $row['Message'];
		$isRead = $row['read'];
		echo "\n   <tr> ";
		echo "\n      <td><span class='username'>$nick : </span></td>";
		echo "\n      <td><span class='small'> [$time] ";
		if (!($isRead))
		{
			echo "<a href=\"#\" alt=\"mark read\" onclick=\"markRead($msgID);\">x</a>";
		}
		else
		{
			echo "[Read]";
		}
		echo "</span></td>";
		echo "\n   </tr> ";
		echo "\n   <tr> ";
		echo "\n      <td colspan='2'>$msg</td>";
		echo "\n   </tr>";
		echo "\n</table>";
		echo "</p>";
		$lastmsg = $msgID;
	}
	echo "\n<div id='log'> </div>";
	$_SESSION['lastInbox'] = $lastmsg;
}
echo "\n</div> ";

if ($submit && $toRec)
  {
	if ($email)
	{
		$toSend = "SELECT Email FROM `User` WHERE Email = :email;";
		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':email'=>$email)) or errormail($email,"Failed to get user account inbox.php","failed to get account","Failed to find your account");
		$numrows = $stmt->rowCount();

		if ($numrows>0)	{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$dbemail = $row['Email'];
		} else {
			echo "\nNo user created yet.  <a href=\"adduser.php\">Create user</a>";
		}

	}
	else
		die("Email not entered");


	if  ($dbemail && $message) {
		$toSend =
		   "INSERT INTO `chatter`
		    VALUES (NULL, :displayName, :email, :date, :message, :toRec, NULL);";
		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(":displayName"=>$displayName, ":email"=>$email, ":date"=>$date, ":message"=>$message, ":toRec"=>$toRec))
			or errormail($email,"Failed insert message.  inbox.php","Error occured while sending","an error occured while sending, please try again later");
		$rowCt = $stmt->rowCount();
		if ($rowCt ==0)
			errormail($email, "failed to send (verification count) inbox.php", "Unable to verify rowct and sent message","Error: message didn't send.");
	}
	else
		echo "<strong>Blank field detected!</strong>";
  }
echo "<form action='inbox.php' method='POST'>";


?>
<link type="text/css" href="jquery/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="getnames.php"></script>
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript">
window.onload=function(){
    var timeoutIdMsg=0;
	timeoutIdMsg = setInterval("getInbox()", 2000 );
}
</script>

<?php
	if (empty($seeAll))
	{
		echo "<a href='inbox.php?seeAll=true'>See all messages</a>";
	}
	else
	{
		echo "<a href='inbox.php'>Hide read messages</a>";
	}
	?>
   <table>
		<?php if (!$email) echo "<!--"?><tr>
			<td>
				<div class="ui-widget">
					<label for='toRecipient'>To : </label> </td>
					<td><input type='text' id='toRecipient' name='toRecipient' <?php if ($fillTo) echo "value='$fillTo'" ?>></input>
				</div>
			</td>
		</tr><?php if (!$email) echo "-->";?>
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<?php if ($email) echo "<!--"?>
				<input type='text' name='Email'></input>
				<?php if ($email) echo "--> $email";?>
			</td>
		</tr>
		<td>
			Message:
		</td>
		<td>
			<textarea rows="4" cols="100" name='Message'></textarea>
		</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' value='Send'></td>
		</tr>
	</table>
</form>

<?php require($DOCUMENT_ROOT . "inboxmenu.html"); ?>
<br>
<br>
<br>
<br>
<br>
<br>
