<?php
//PHP SCRIPT: getimagesdb.php
Header("content-type: application/x-javascript");
require_once 'config.php';
session_start();
if (isset($_SESSION['max_width']) && ($_SESSION['max_width'] != 0 ))
	$max_width = $_SESSION['max_width'];
else
	$max_width = $GLOBALS['max_width'];
$displayName = $_GET['displayName'];
$desc = $_GET['desc'];
$series = strip_tags($_GET['series']) . "%";
$prefix ="";

function returnSeries($prefix, $series) {
	$anonyQ = "";
	if (isset($displayName)) {
		$anonyQ = " AND `Images`.Anonymous = 0 ";
	}
	$ctItem = 0;
	$ctSeries = 0;

	//get the current series (this may be multiple)
	$toSend = "SELECT `Series`.`SeriesID` FROM `Series` "
	         ." WHERE `Series`.`Tag` LIKE :series ;";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(":series"=>$series)) or errormail($email, "Image query error", "Image Query Error", "alert('Unknown Query Error Occured');");
	$rows = $stmt->fetchAll();
	$numrows = $stmt->rowCount();
	if ($numrows > 0)
	{
		foreach ($rows as $row)
		{
			$seriesID[$ctSeries] = $row['SeriesID'];
			$ctSeries++;
		}
		$fvSeries = $ctSeries;
	}

	//populate what series to check
	foreach ($seriesID as $curSeries)
	{
		$toSend = "SELECT `Series`.`SeriesID` FROM `Series` "
		       . " WHERE `Series`.`SeriesOf` = :series ;";
		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->execute(array(":series"=>$curSeries)) or die("Query Error");
		$numrows = $stmt->rowCount();
		if ($numrows > 0)
		{
			foreach ($rows as $row)
			{
				$seriesID[$ctSeries] = $row['SeriesID'];
				$ctSeries++;
			}
		}
	}

	$ctSeries = 0;
	//iterate through each series to check and populate the searchItems
	while ($ctSeries < count($seriesID))
	{
		//search for the series provided...
			// need to know the tags, and SeriesOf initially to build the real search...
		$toSend = "SELECT `Series`.`Tag` FROM `Series` "
				 ." WHERE `Series`.`SeriesID` = :series ;";
		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->execute(array(":series"=>$curSeries)) or die("Query Error");
		$numrows = $stmt->rowCount();
		if ($numrows > 0)
		{
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$curTag = $row['Tag'];
				// seperate tag into words if necessary
				while (($spacePos = strpos($curTag, ' ')) > 0)
				{
					$restOf = substr($curTag, $spacePos+1);
					$curTag = substr($curTag, 0, $spacePos);
					$searchItem[$ctItem] = "@" . $curTag . "%";
					$curTag = $restOf;
					$ctItem++;
				}
				$searchItem[$ctItem] = "@". $curTag . "%";
				$ctItem++;
				$ctSeries++;
			}
		}
	}

	$fvItem = count($searchItem);
	if ($fvItem > 0)
	{
		//build a search query
		$toSend = "SELECT `User`.UploadPath, `User`.DisplayName, `Images`.ImgID, `Images`.FileName, `Images`.Name, `Images`.Desc FROM `Images`,`User` "
	          ." WHERE `User`.Email = `Images`.Email "
			  ." AND ( `Images`.Desc LIKE ? ";

		// append or's
		for ($i=1; $i<$fvItem ; $i++)
		{
			$toSend .= " OR `Images`.Desc LIKE ? ";
		}
		$toSend .= " ) ORDER BY `Images`.Date;";

		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']) or die("Image Series Querry Error");
		$stmt->execute(array_values($searchItem));
		$rows = $stmt->fetchAll();
		$numrows= $stmt->rowCount();
		if ($numrows>0)
		{
			$curimage= 0;
			foreach ($rows as $row)
			{
				$filepath = $row['UploadPath'];
				if (substr($filepath,strlen($filepath)-2,1)!='/')
				{	$filepath.= '/'; }
				$displayName = $row['DisplayName'];

				$imgID = $row['ImgID'];
				$filename = $prefix . $filepath ;

				$file = $row['FileName'];
				$file = str_replace(" ", "%20", $file);
				$filename.= $file;

				$name = $row['Name'];
				$filedesc = $row['Desc'];
				if ($filedesc[0] == '@')
				{
					$ct = 0;
					// finds the index
					while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));

					$filedesc=substr_replace($filedesc,"",0,$ct);
				}

				//Output it as a JavaScript array element
				echo 'imageString['.$curimage.']="'.$filename.'";';
				echo 'descString['.$curimage.']="'.$filedesc.'";';
				echo 'nameString['.$curimage.']="'.$name.'";';
				echo 'submittedBy['.$curimage.']="'.$displayName.'";';
				echo 'galleryarray['.$curimage.']=new Image();';
				echo 'galleryarray['.$curimage.'].id='.$imgID.';';
				if (($curimage==0)||($curimage==$numrows-1))
				{
					echo 'galleryarray['.$curimage.'].src="'.$filename.'";';  //Show the file asap
				}
				$curimage++;
			}

		}
	}
	return;
}

//This function gets the file names of all images in the current directory
//and ouputs them as a JavaScript array
function returnimages($prefix, $email, $desc, $displayName, $filepath='images/') {
	$anonyQ = "";
	if (empty($desc)) {
		$anonyQ = " AND `Images`.Anonymous = :anonymous ";
	}
	$toSend = "SELECT `Images`.ImgID, `Images`.FileName, `Images`.Name, `Images`.Desc, `Images`.Anonymous FROM `Images`
	           WHERE `Images`.Email = :email
			     AND `Images`.Desc LIKE :desc
				 $anonyQ
	           ORDER BY `Images`.Date;";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->bindValue(":email", $email, PDO::PARAM_STR);
	$stmt->bindValue(":desc", $desc . "%", PDO::PARAM_STR);
	if (!empty($anonyQ)) {
		$stmt->bindValue(":anonymous", 0, PDO::PARAM_INT);
	}
	$stmt->execute() or errormail($email, "return image query failed", "unable to return images", "images not found");
	$rows = $stmt->fetchAll();
	$numrows= $stmt->rowCount();
	if ($numrows>0)
	{
		$curimage= 0;
		$lastimage= 0;
		foreach ($rows as $row)
		{
			$imgID = $row['ImgID'];
			$filename = $prefix . $filepath ;
            $file = $row['FileName'];
            $file = str_replace(" ", "%20", $file);
			$filename.= $file;
			$name = $row['Name'];
			$filedesc = $row['Desc'];
			$anony = ($row['Anonymous']==1);
			if (($filedesc[0] == '@') && (empty($desc)))
			{
				continue;
			}

			if ($filedesc[0] == '@')
			{
				$ct = 0;
				// finds the index
				while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));

				$filedesc=substr_replace($filedesc,"",0,$ct);
			}

			//Output it as a JavaScript array element
			echo 'imageString['.$curimage.']="'.$filename.'";';
			echo 'descString['.$curimage.']="'.$filedesc.'";';
			echo 'nameString['.$curimage.']="'.$name.'";';
			if ($anony) {
				echo 'submittedBy['.$curimage.']="Anonymous"; ';
			} else {
				echo 'submittedBy['.$curimage.']="'.$displayName.'"; ';
			}
			echo 'galleryarray['.$curimage.']=new Image();';
			echo 'galleryarray['.$curimage.'].id='.$imgID.';';
			if (($curimage==0)||($curimage==$numrows-1))
			{
				echo 'galleryarray['.$curimage.'].src="'.$filename.'";';
			}
			$lastimage = $curimage;
			$curimage++;
		}
		echo 'galleryarray['.$lastimage.'].src="'.$filename.'";';
	}

	return;
}

function returnimagesNoEmail($prefix, $desc) {
	$toSend = "SELECT `User`.UploadPath, `User`.DisplayName, `Images`.ImgID, `Images`.FileName, `Images`.Name, `Images`.`Desc` FROM `Images`,`User` "
	          ." WHERE `User`.Email = `Images`.Email "
			  ." AND `Images`.`Desc` LIKE :desc "
	          ." ORDER BY `Images`.Date; ";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(':desc'=>$desc."%")) or errormail("No Email", "No Email, error getting images", "Unable to retrieve images");
	$numrows= $stmt->rowCount();
	$rows = $stmt->fetchAll();
	if ($numrows>0)
	{
		$curimage= 0;
		foreach ($rows as $row) {
			$filepath = $row['UploadPath'];
			if (substr($filepath,strlen($filepath)-2,1)!='/')
			{	$filepath.= '/'; }
			$displayName = $row['DisplayName'];

			$imgID = $row['ImgID'];
			$filename = $prefix . $filepath ;

            $file = $row['FileName'];
            $file = str_replace(" ", "%20", $file);
			$filename.= $file;

			$name = $row['Name'];
			$filedesc = $row['Desc'];
			if ($filedesc[0] == '@')
			{
				$ct = 0;
				// finds the index
				while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));

				$filedesc=substr_replace($filedesc,"",0,$ct);
			}

			//Output it as a JavaScript array element
			echo 'imageString['.$curimage.']="'.$filename.'";';
			echo 'descString['.$curimage.']="'.$filedesc.'";';
			echo 'nameString['.$curimage.']="'.$name.'";';
			echo 'submittedBy['.$curimage.']="'.$displayName.'";';
			echo 'galleryarray['.$curimage.']=new Image();';
			echo 'galleryarray['.$curimage.'].id='.$imgID.';';
			if (($curimage==0)||($curimage==$numrows-1))
			{
				echo 'galleryarray['.$curimage.'].src="'.$filename.'";';  //Show the file asap
			}
			$curimage++;
		}
	}

	return;
}

echo "var loadingImg = new Image();";
echo "loadingImg.src = 'loading.png';";
echo 'var galleryarray=new Array();'; //Define array in JavaScript
echo 'var imageString=new Array();';
echo 'var descString=new Array();';
echo 'var nameString=new Array();';
echo 'var submittedBy=new Array();';
if ((isset($displayName)) && ($displayName!='_ANY_'))
{
	$toSend = "SELECT `User`.UploadPath, `User`.Email FROM `User`
	           WHERE `User`.DisplayName = :displayName;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(":displayName"=>$displayName)) or errormail($displayName, "Failed to retrieve Upload Path and Email for $displayName", "Failed to query for Path and Email");
	$numrows = $stmt->rowCount();
	if ($numrows > 0)
	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$filepath = $row['UploadPath'];
		$email = $row['Email'];
	}
	if (substr($filepath,strlen($filepath)-2,1)!='/')
	{	$filepath.= '/'; }
	returnimages($prefix, $email,$desc,$displayName,$filepath); //Output the array elements containing the image file names
}
else if (isset($series))
{
	returnSeries($prefix, $series);
}
else
{
	returnimagesNoEmail($prefix, $desc);
}


?>
