<?php
session_start();
date_default_timezone_set("America/New_York");
$halt = $_GET['halt'];
$lastMsg = $_SESSION['lastMsg'];
$email = $_SESSION['email'];
$displayName = $_SESSION['displayName'];
$date = date("Y-m-d H:i:s");

if (empty($lastMsg))
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter`;";
}
else
{
	$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE MsgID > $lastMsg;";
}
$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("cannot connect!");
mysql_select_db($GLOBALS['DB_NAME']) or die ("Cannot find DB!");
$query = mysql_query($toSend) or die ("Unknown Retreive Query error 9998");
$numrows= mysql_num_rows($query);
if ($numrows>0)
{
	while ($row = mysql_fetch_assoc($query))
	{
		$msgID = $row['MsgID'];
		$nick = $row['Nickname'];
		$time = $row['DateTime'];
		$msg = $row['Message'];
		echo "\n[$time] <$nick> $msg ";
	}
	$_SESSION['lastMsg'] = $msgID ;
	if ($halt > 0)
	{
		die();
	}
}
