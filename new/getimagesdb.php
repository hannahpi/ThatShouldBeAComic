<?php
//PHP SCRIPT: getimagesdb.php
Header("content-type: application/x-javascript");

$displayName = $_GET['displayName'];
$desc = $_GET['desc'];
//$filepath='../images/';

//This function gets the file names of all images in the current directory
//and ouputs them as a JavaScript array
function returnimages($email, $desc, $filepath='images/') {
	$toSend = "SELECT `Images`.FileName FROM `Images` 
	           WHERE `Images`.Email = '$email'
			     AND `Images`.Desc LIKE '$desc%'
	           ORDER BY `Images`.Date;";
	$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'], $GLOBALS['DB_PASSWORD']) or die("Connect error");
	mysql_select_db($GLOBALS['DB_NAME']) or die("DB not found");
	$query = mysql_query($toSend) or die("Query Error");
	
	$numrows= mysql_num_rows($query);
	if ($numrows>0)
	{		
		$curimage= 0;
		while ($row = mysql_fetch_assoc($query))
		{
			$filename = $filepath ;
			$filename.= $row['FileName'];
			
			//Output it as a JavaScript array element
			echo 'galleryarray['.$curimage.']=new Image();';
			echo 'galleryarray['.$curimage.'].src="'.$filename .'";';
			echo 'galleryarray['.$curimage.'].id="'.substr($filename,strlen($filepath),strlen($filename)-strlen($filepath)-4).'";';
			$curimage++;
		}
		
	}

	return;
}
$toSend = "SELECT `User`.UploadPath, `User`.Email FROM `User` 
	           WHERE `User`.DisplayName = '$displayName';";
$connect = mysql_connect("localhost", $GLOBALS['DB_FULLUSER'], $GLOBALS['DB_PASSWORD']) or die("Connect error");
mysql_select_db($GLOBALS['DB_NAME']) or die("DB not found");
$query = mysql_query($toSend) or die("Query Error");

$numrows = mysql_num_rows($query);
if ($numrows > 0)
{
	while ($row = mysql_fetch_assoc($query))
	{	
		$filepath = $row['UploadPath'];
		$email = $row['Email'];
	}
		
}
if (substr($filepath,strlen($filepath)-2,1)!='/')
{	$filepath.= '/'; }

echo 'var galleryarray=new Array();'; //Define array in JavaScript
returnimages($email,$desc,$filepath) //Output the array elements containing the image file names
?> 