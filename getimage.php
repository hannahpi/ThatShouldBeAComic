<?php
//PHP SCRIPT: getimage.php
Header("content-type: application/x-javascript");
require_once 'config.php';
$image = strip_tags($_GET['image']);
$email = '';

function getEmail($image)
{
	$toSend = "SELECT `Images`.Email FROM `Images`
	           WHERE `Images`.ImgID = :imgID;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(":imgID"=> $image)) or errormail("No Email", "Image Email Query failed: $image", "Image Email Query failed.");

	$numrows = $stmt->rowCount();
	if ($numrows > 0)
	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return ($row['Email']);
	}
}

//This function gets the file names of all images in the current directory
//and ouputs them as a JavaScript array
function returnimages($image, $desc, $filepath, $displayName) {
	$toSend = "SELECT `Images`.ImgID, `Images`.FileName, `Images`.Name, `Images`.Desc , `Images`.Anonymous  FROM `Images`
	           WHERE `Images`.ImgID = :image;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(":image"=>$image)) or errormail($displayName, "Image Query failed: $image", "Image Query failed.");
	$rows = $stmt->fetchAll();

	$numrows= $stmt->rowCount();
	if ($numrows>0)
	{
		$curimage= 0;
		foreach ($rows as $row)
		{
			$imgID = $row['ImgID'];
			$filename = $filepath ;
            $file = $row['FileName'];
            $file = str_replace(" ", "%20", $file);
			$filename.= $file;
			$name = $row['Name'];
			$filedesc = $row['Desc'];
			$anony = ($row['Anonymous']==1);
			while ($filedesc[0] == '@')
			{
				$ct = 0;
				// finds the index
				while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));

				$filedesc=substr_replace($filedesc,"",0,$ct);
			}

			//Output it as a JavaScript array element
			echo 'imageString['.$curimage.']="'.$filename.'"; ';
			echo 'descString['.$curimage.']="'.$filedesc.'"; ';
			echo 'nameString['.$curimage.']="'.$name.'"; ';
			echo 'galleryarray['.$curimage.']=new Image(); ';
			echo 'galleryarray['.$curimage.'].id='.$imgID.'; ';
			if ($anony) {
				echo 'submittedBy['.$curimage.']="Anonymous"; ';
			} else {
				echo 'submittedBy['.$curimage.']="'.$displayName.'"; ';
			}
			if (($curimage==0)||($curimage==$numrows-1))
			{
				echo 'galleryarray['.$curimage.'].src="'.$filename.'"; ';
			}
			$curimage++;
		}

	}
		return;
}

echo 'var galleryarray=new Array();'; //Define array in JavaScript
echo 'var imageString=new Array();';
echo 'var descString=new Array();';
echo 'var nameString=new Array();';
echo 'var submittedBy=new Array();';
$email = getEmail($image);
$toSend = "SELECT DisplayName, UploadPath FROM `User` " ;
$toSend .= "WHERE Email = :email;";
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
$stmt->execute(array(":email"=>$email)) or errormail($email, "Error querying DisplayName and UploadPath for $email", "User Path by Email error");
$numrows = $stmt->rowCount();
if ($numrows > 0)
{
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$displayName = $row['DisplayName'];
	$filepath = $row['UploadPath'];
}

if (substr($filepath,strlen($filepath)-2,1)!='/')
{	$filepath.= '/'; }

returnimages($image, $desc, $filepath, $displayName); //Output the array elements containing the image file names
?>
