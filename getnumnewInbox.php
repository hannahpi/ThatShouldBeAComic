<?php
session_start();
Header("content-type: application/x-javascript");
require_once 'config.php';
date_default_timezone_set("America/New_York");
$email = $_SESSION['email'];

$toSend = "Select `chatter`.`MsgID` FROM `chatter`,`User` WHERE `User`.Email = :email AND `chatter`.`recipient` = `User`.`DisplayName` AND ( NOT `read` OR `read` IS NULL) ;";
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
$stmt->execute(array(":email"=>$email)) or errormail($email, "Can't count those messages in inbox", "Error finding your inbox!");
$numrows= $stmt->rowCount();
if ($numrows > 0)
{
	echo "$numrows";
}

?>
