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

<form enctype="multipart/form-data" action="upload.php" method="POST">
<table>
  <tr>
	<td>Choose a file to upload:*</td>
	<td><input name="uploadedfile" type="file" /></td>
  </tr>
  <tr>
	<td>Date Override:</td>
	<td><input name="date" type="text" />
  </tr>
  <tr>
    <td>Name:</td>
	<td><input name="imageName" type="text" />
  </tr>
  <tr>
    <td>Description:</td>
	<td><input name="desc" type="text" />
  </tr>
  <tr>
    <td></td><td><input type="submit" value="Upload File" /></td>
  </tr>
</table>


</form>