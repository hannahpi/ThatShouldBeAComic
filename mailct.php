<?php
session_start();
$headBack = $_SESSION['lastPage'];
$email = $_SESSION['email'];
$displayName = $_SESSION['displayName'];
if (empty($email))
{
	die(0);
}

$toSend = "Select `MsgID`, `Nickname`, `DateTime`, `Message` FROM `chatter` WHERE `recipient` = :displayName AND ( NOT `read` OR `read` IS NULL) ;";
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
$stmt->execute(array(:displayName=>$displayName)) or die ("Unknown Retreive Query error 9998");
$numrows= $stmt->rowCount();
echo $numrows;
?>
