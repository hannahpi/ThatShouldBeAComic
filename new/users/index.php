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
?>

<form action="login.php" method="POST">
<table>
	<tr>
		<td>Email:</td>
		<td><input type="text" name="email"></input> </td>
	</tr>
	<tr>
		<td>Password: </td>
		<td><input type="password" name="password"></input> </td>
	</tr>
	<tr>
		<td></td><td><input type="submit" name="submit" value="Login"></td>
	</tr>
</table>
</form>

