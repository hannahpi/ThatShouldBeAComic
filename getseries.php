<?php
//PHP SCRIPT: getimagesdb.php
//require_once 'config.php';

//$displayName = $_GET['displayName'];

//function to return the series that a user has posted to
function findSeries($displayName) {
	$ctItem = 0;
	$ctSeries = 0;

	//get email
	$toSend = "SELECT `User`.Email FROM `User`
	           WHERE `User`.DisplayName = :displayName;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(':displayName'=>$displayName));
	$numrows = $stmt->rowCount();
	if ($numrows > 0) {
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email = $row['Email'];
	}

	//get the current series (this may be multiple)
	$toSend = "SELECT `Series`.`Tag` FROM `Series` WHERE `Series`.Visible = 1;";

	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute();
	$rows = $stmt->fetchAll();
	$numrows = $stmt->rowCount();
	if ($numrows > 0)
	{
		foreach($rows as $row)
		{
			$curTag = $row['Tag'];
			// seperate tag into words if necessary
			while (($spacePos = strpos($curTag, ' ')) > 0)
			{
				$restOf = substr($curTag, $spacePos+1);
				$curTag = substr($curTag, 0, $spacePos-1);
				$searchItem[$ctItem] = "@" . $curTag;
				$curTag = $restOf;
				$ctItem++;
			} // no more spaces
			$searchItem[$ctItem] = "@". $curTag;
			$ctItem++;
			$ctSeries++;

		}
		$fvItems = $ctItem;
	}

    //check for posts into the series.
	$ctSeries = 0;
	for ($i=0; $i<$fvItems; $i++)
	{
		$toSend = "SELECT ImgID FROM `Images` "
		       . " WHERE `Desc` LIKE :searchItem AND "
			   . "       `Email` = :email ;";

		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->execute(array(":searchItem"=>"%$searchItem[$i]%", ":email"=>$email));
		$numrows = $stmt->rowCount();
		if ($numrows > 0)
		{
			$foundSeries[$ctSeries]= substr($searchItem[$i],1) . "%";
			$ctSeries++;
		}
	}

	//"blindly" get the names back based on the found series
	$ctSeries = 0;

	$fvItem = count($foundSeries);
	if ($fvItem > 0)
	{
		//build a search query
		$toSend = "SELECT DISTINCT Name, Tag FROM Series "
	          ." WHERE `Tag` LIKE ? ";

		// append or's
		for ($i=1; $i<$fvItem ; $i++)
		{
			$toSend .= " OR `Tag` LIKE ? ";
		}
		$toSend .= " ; ";

		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->execute(array_values($foundSeries));
		$rows = $stmt->fetchAll();
		$numrows = $stmt->rowCount();
		if ($numrows>0)
		{
			echo "<table class='series'> <tr><td> Series Posted To: </td> </tr> ";
			foreach ($rows as $row)
			{
				$linkDesc = $row['Name'];
				$series = $row['Tag'];

				//find space in tag
				if (($spacePos = strpos($series, ' ')) > 0) //if there's a space
				{
					//isolate series tag so we can put it in a link.
					$series = substr($series, 0, $spacePos);
				}

				echo "<tr> <td> ";
				echo "<a href='viewSeries.php?series=$series'> $linkDesc </a> ";
				echo "</td> </tr> ";
			}
			echo "</table> " ;
		}
	}
	return;
}

?>
