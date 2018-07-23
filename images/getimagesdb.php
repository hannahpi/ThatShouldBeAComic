<?php
//PHP SCRIPT: getimagesdb.php
Header("content-type: application/x-javascript");

$emailsearch='shilohskellyton@thatshouldbeacomic.com';
$filepath='images/';


//This function gets the file names of all images in the current directory
//and ouputs them as a JavaScript array
function returnimages() {
	$toSend = "SELECT `Images`.FileName FROM `Images` 
	           WHERE `Images`.Email = 'shilohskellyton@thatshouldbeacomic.com'
	           ORDER BY `Images`.Date;";
	$connect = mysql_connect("localhost", "dreamre2_cRead", "cReader12") or die("Connect error");
	mysql_select_db("dreamre2_comicReq") or die("DB not found");
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
			echo 'galleryarray['.$curimage.'].id="'.substr($filename,0,strlen($filename)-4).'";';
			$curimage++;
		}
		
	}
	else
		echo 'yeah something isn\'t right here!';

	return;
}

echo 'var galleryarray=new Array();'; //Define array in JavaScript
returnimages() //Output the array elements containing the image file names
?> 