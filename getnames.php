<?php
//PHP SCRIPT: getdispnames.php
Header("content-type: application/x-javascript");
require_once 'config.php';
$minLevel = $GLOBALS['MIN_USER_LEVEL_LISTED'];
$toSend = "SELECT DisplayName FROM `User` WHERE UserLevelID >= :minLevel ";
$connect = connect_tsbac();
$stmt = $connect->prepare($toSend, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$stmt->bindValue(":minLevel", $minLevel, PDO::PARAM_INT);
$stmt->execute() or errormail("Unknown User","Failed get user names.  getnames.php","no names found","no names found");
$rowCt = $stmt->rowCount();
$rows = $stmt->fetchAll();

if ($rowCt>0)
{
	$ct = 0;
	echo "\n$(function() { ";
	echo "\n	var availableNames = [";
	foreach ($rows as $row)
	{
		$ct++;
		$name = $row['DisplayName'];
		if ($ct < $rowCt)
		{
			echo ' "' . $name . '" , ';
		}
		else
		{
			echo ' "' . $name . '" ';
		}
	}
	echo "\n ]; ";
	echo "\n$( \"#toRecipient\" ).autocomplete({ ";
	echo "\n	  source: availableNames ";
	echo "\n		   }); ";
	echo "\n		}); ";
} else {
	echo "\nNo Users Found $rowCt ";
}

?>
