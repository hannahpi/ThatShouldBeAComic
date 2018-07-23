<?php
//PHP SCRIPT: getlast.php
//Header("content-type: application/x-javascript");
date_default_timezone_set("America/New_York");
require_once 'config.php';
//This function finds the most recent html/php file edit date.
//returns as unix timestamp.
function getLastCode($dirname=".") {
	$pattern="/(\.htm$)(\.html$)(\.php$)(\.css$)(\.js$)/"; //valid image extensions
	//$files = array();
	$curimage=0;
	$newest = new DateTime(-1);
	if($handle = opendir($dirname)) {
		while(false !== ($file = readdir($handle))){
			if(preg_match($pattern, $file)){ //if this file is a valid image
				//Check the date.
				$curdate=new DateTime(filemtime($file));
				if ($curdate > $newest)
				{
					$newest = new DateTime($curdate);
				}
			}
		}
		closedir($handle);
	}
	return($newest);
}

function getLastUpdate(&$imgID)
{
	$toSend = "SELECT Date, ImgID FROM `Images` "
             ." Where `Desc` not like \"%@art%\"  "
			 ." AND `Images`.Anonymous = 0 "
	         ." ORDER BY Date DESC "
			 ." LIMIT 0, 1; ";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute() or errormail($ses_email,"Failed to execute query.  getlast.php","ImageID: $imgID SQL ERROR:" . print_r($stmt->errorInfo(),true), "failed to get last update");
	$rows = $stmt->fetchAll();
	$numrows = $stmt->rowCount();

	if ($numrows==1)
	{
		foreach ($rows as $row) {
			$newest = $row['Date'];
			$imgID= $row['ImgID'];
			return ($newest);
		}
	}
}

function getNumComments(&$imgID)
{
	$toSend = "SELECT * FROM `comments` ;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute() or die("Last Update Query Error");
	$numrows= $stmt->rowCount();

	$toSend = "Select ImgID from `comments` ORDER BY CommentDate DESC  LIMIT 0,1;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute() or die("Last Update Query ID Error");
	$nrows = $stmt->rowCount();
	if ($nrows == 1)
	{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$imgID = $row['ImgID'];
	}
	return ($numrows);
}

function getNumLicks()
{
	$toSend = "SELECT * FROM `licks` ;";
	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute() or die("Last Update Query Licks Error");
	$numrows= $stmt->rowCount();
	return ($numrows);
}

echo '<table class="lastupdate"> <tr> <td> Newest content:</td><td>'. date("F d Y h:i:s A.",strtotime(getLastUpdate($imgID)));
echo '[<a href="showimg.php?image='. $imgID.'">View it!</a>] </td></tr>';
echo '<tr> <td>  Code last updated:</td><td>'. getLastCode()->format("F d Y h:i:s A.") . ' </td></tr>';
echo '<tr> <td>  Comments submitted: </td> <td>'.getNumComments($imgID);
echo '[<a href="showimg.php?image='. $imgID.'">See last!</a>]</td></tr>';
echo '<tr> <td>  Licks: </td> <td>'.getNumLicks().' giraffes licking comics</td></tr>';
echo '<tr> <td> User List : </td> <td> <a href="viewUsers.php">View it!</a> </td> </tr>';
echo '<tr> <td> ThatShouldBeAComic.com &copy; 2011-2018  </table>';  //Output the last mod date.
?>
