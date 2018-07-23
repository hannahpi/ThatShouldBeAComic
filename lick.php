<?php
session_start();
require_once 'config.php';
$imgID = strip_tags($_GET['imgID']);
//$submit = $_POST['submit'];
$email = $_SESSION['email'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
$goBack = $_SESSION['lastPage'];

if ($imgID && $email)
{
	$toSend = "SELECT ImgID, Email FROM `licks` WHERE ImgID=:imgID AND Email=:email;";
	$connect= connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(":imgID"=>$imgID, ":email"=>$email)) or errormail($email, "Lick lookup Failed", "Failed to lick");
	$numrows= $stmt->rowCount();
	if ($numrows==0)
	{
		$toSend =
		   "INSERT INTO `licks`
		    VALUES (NULL, :imgID, :email);";
		$connect= connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->execute(array(":imgID"=>$imgID, ":email"=>$email)) or errormail($email, "Lick Failed", "Failed to lick");
		$success = $stmt->rowCount()>0;
		echo "<br /> That was finger licking good! <a href=$goBack>Back</a>";
	}
}


?>
