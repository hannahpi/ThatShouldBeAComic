<?php session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<!--- header and style definitions --> 
<title>File Upload - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamersnet.net/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script src="files.js" type="text/javascript"></script>
</head>

<?php
session_start();


?>

<form id="frmSelect" enctype="multipart/form-data" action="upload.php" method="POST">
<table id="filesUpload">
  <tr>
	<td>File to upload:* </td>
	<td>Name:* </td>
	<td>Description: </td>
	<td>Anonymous </td>
  </tr>
  <tr>
	<td><input name="uploadedfile[]" type="file" /></td>
	<td><input name="imageName[]" type="text" /></td>
	<td><input name="desc[]" type="text" /></td>
	<td><input name="anonymous[]" type="checkbox" value="1" /></td>
  </tr>
</table>

<table>
  <tr>
    <td><button type="button" id="btnAddMore">Add More </button></td>
	<td><input type="submit" value="Upload File" /></td>
  </tr>
</table>


</form>