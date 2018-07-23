<?php
session_start();
require_once 'config.php';
date_default_timezone_set("America/New_York");
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
$headBack = "inbox.php";
$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
if ($headBack)
{
	$headBack = "Location: $headBack";
}

if (empty($lastmsg))
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE `recipient` = :displayName AND ( NOT `read` OR `read` IS NULL) ;";
	$lastmsg=0;
}
else
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE `MsgID` > :lastmsg AND `recipient` = :displayName AND (NOT `read` OR `read` IS NULL);";
}
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
if ($lastmsg > 0)
	$stmt->execute(array(":lastmsg"=>$lastmsg, ":displayName"=>$displayName)) or errormail($email, "Couldn't load inbox contents lastmsg: $lastmsg ", "Couldn't load your inbox!");
else
	$stmt->execute(array(":displayName"=>$displayName)) or errormail($email, "Couldn't load inbox contents.", "Couldn't load your inbox!");

$numrows= $stmt->rowCount();
$rows = $stmt->fetchAll();
if ($numrows>0)
{
	echo "\n<div id='log'>";
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
		if (($isRead=0)||(!($isRead)))
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
	$_SESSION['lastInbox'] = $lastmsg;
	echo "\n</div> ";

	if ($halt > 0)
	{
		exit;
	}
}
?>
