<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>Members - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>

<?php
session_start();
$email = $_SESSION["email"];
$displayName= $_SESSION['displayName'];

if ($email)
{
	echo "<br>Welcome ". $displayName;
	echo "<br /><a href='fileselect.php'> Upload a file! </a>";
	echo "<br /><a href='http://www.thatshouldbeacomic.com/index.html'>Back to index</a>";
	echo "<br /><a href='http://www.thatshouldbeacomic.com/journal.php'>Back to journal</a>";
	echo "<br /><a href='logout.php'>Log out!</a>";
}
else
{
	die("Access Denied");
}

?>