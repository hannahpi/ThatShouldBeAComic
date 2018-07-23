<?php
session_start();
require_once 'config.php';
$imgID = strip_tags($_GET['imgID']);
$email = $_SESSION['email'];
if (!($email))
{  $email = strip_tags($_POST['Email']); }
$goBack = $_SESSION['lastPage'];

if ($imgID)
{
	$toSend = "SELECT ImgID, Email FROM `licks` WHERE ImgID= :imgID AND Email= :email;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']) or die("Image Series Querry Error");
	$stmt->execute(array(":imgID"=>$imgID, ":email"=>$email));
	$numrows= $stmt->rowCount();

	$toSend = "SELECT * FROM `licks` WHERE ImgID=:imgID;";
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']) or die("Image Series Querry Error");
	$stmt->execute(array(":imgID"=>$imgID));

	$numlicks = $stmt->rowCount();
	$wordhas = ($numlicks==1)?"has":"have";
	$giraffe = ($numlicks==1)?"giraffe":"giraffes";

	if ($numrows==1)
	{
		$numlicks--;
		$giraffe = ($numlicks==1)?"giraffe":"giraffes";
		$wordhas = ($numlicks==1)?"has":"have";
		echo "<strong>You</strong> and $numlicks other $giraffe $wordhas licked this comic!";
	}
	else
	{
		if ($email)
		{
			echo "$numlicks $giraffe $wordhas licked this comic!";
		}
		else
		{   echo "$numlicks $giraffe $wordhas licked this comic!  Login to lick! <br />"; }

	}
}


?>
