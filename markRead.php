<?php
require_once 'config.php';
session_start();
date_default_timezone_set("America/New_York");
$msg = strip_tags($_GET['msg']);
$email = $_SESSION['email'];
$date = date("Y-m-d H:i:s");



if ($msg)
{
	if ($email)
	{
		$toSend = "SELECT DisplayName FROM `User` WHERE Email = :email ;";
		//dbug helper:
			$message= "Message: $msg \n email: $email";
			$message.= "\n comment:$comment \n Date: $date  \n Sending: \n $toSend";

		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':email'=>$email)) or errormail($email, $message, "failed to get user info","User Query error: Mark as read failed.");
		$numrows = $stmt->rowCount();

		if ($numrows>0)
		{
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			while ($row = mysql_fetch_assoc($query))
			{
				$dbdisplay = $row['DisplayName'];
			}
		}
		else
		{
			echo "\nNo user created yet.  <a href=\"adduser.php\">Create user</a>";
		}
	}
	else
		die("Not logged in!");


	if  ($dbdisplay)
	{
		$toSend = "UPDATE `chatter` SET `read` = True WHERE `recipient` ='$dbdisplay' AND `MsgID` = $msg ;";
		//dbug helper:
			$message= "Message: $msg \n email: $email";
			$message.= "\n comment:$comment \n Date: $date  \n Sending: \n $toSend";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or errormail($email, $message, "read message: Connect error","Cannot connect!");
		mysql_select_db($GLOBALS['DB_NAME']) or errormail($email, $message, "read message: Cannot find DB!", "Cannot find DB!");
		$query = mysql_query($toSend) or errormail($email, $message, "read message: Unknown Query error!", "Unknown Query error!");
	}
}
