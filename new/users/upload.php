<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>Members - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="images/getimages.php"></script>
</head>

<?php
session_start();
$email = $_SESSION['email'];
$date = strip_tags($_POST['date']);
if (!($date))
{	$date = date("Y-m-d H:i:s"); }
$imageName = strip_tags($_POST['imageName']);
$desc = strip_tags($_POST['desc']);
$userlevel = $_SESSION['userlevel'];



$target_path = "uploads/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if ($email)
{
  if($userlevel >= 50)  bunch of random text..
  {
	if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
		" has been uploaded";
		$fileName = $_FILES['uploadedfile']['name'];
		$toSend = "INSERT INTO `Images`
			           Values(NULL, '$fileName', '$imageName', '$email', '$date', '$desc');";
		$connect= mysql_connect("localhost", $GLOBALS['DB_FULLUSER'],$GLOBALS['DB_PASSWORD']) or die("Cannot connect!");;
		mysql_select_db($GLOBALS['DB_NAME']) or die("Cannot find DB!");
		$query = mysql_query($toSend) or die("Unknown User Query error!");
	} 
	else
	{
		echo "There was an error uploading the file, please try again!";
	}
  }
  else
	die("Permission Denied.  You have insufficient priveleges.  (or there's a bug...)");  
}
else
{
	die ("Are you sure you logged in?  <a href='index.php'>Go back</a>");
}
?>