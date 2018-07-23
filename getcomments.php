<?php session_start(); ?>
<!--<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />-->

<?php
//PHP SCRIPT: getcomments.
require_once 'config.php';
date_default_timezone_set("America/New_York");
$step = 5;
$ses_email = $_SESSION['email'];
$fileName= strip_tags($_GET['fileName']);
$order= strip_tags($_GET['order']);
$page = strip_tags($_GET['page']);
$imgID = strip_tags($_GET['imgID']);
$goBack = $_SESSION['lastPage'];

if ($imgID)
{
	$toSend = "SELECT `comments`.CommentID, `comments`.CommentDate, `comments`.Comment,
	           `User`.DisplayName, `User`.Email FROM `comments`,`User`
	           WHERE `User`.Email = `comments`.Email
	           AND `comments`.ImgID = :imgID
			   AND `User`.UserLevelID > 0
	           ORDER BY `comments`.CommentDate ";

	if ($order == "desc")
		$toSend .= "$order \n";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	$stmt->execute(array(':imgID'=>$imgID)) or errormail($ses_email,"Failed to execute query.  getcomments.php","initial comment retrieval failed","failed to get comments");
	$rows = $stmt->fetchAll();
	$rowCt = $stmt->rowCount();

	echo "\n <br />";
	if ($rowCt>0 && $rowCt<=$step)
	{
		foreach ($rows as $row) {
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];

			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			$date = date("M d Y h:i:s A.",strtotime($date));
			echo "\n<span class=\"username\"><br /> $displayName on $date.";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='#' onclick='editComment($commentid); return false;'>edit</a>"; }
			echo "</span> </td> </tr>";
			echo "\n</table><br />";
		}
	} else if ($rowCt>5) {
		$startComment = $rowCt-5;
		$toSend .= " LIMIT :startComment , :comments ;";
		if (empty($page))
			$page = ceil($rowCt/5.0);
		else
			$startComment = ($page - 1) * $step;

		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->bindValue(":imgID", $imgID, PDO::PARAM_STR);
		$stmt->bindValue(":startComment", intval($startComment), PDO::PARAM_INT);
		$stmt->bindValue(":comments", intval($step), PDO::PARAM_INT);
		$stmt->execute() or errormail($ses_email,"Failed to execute query.  getcomments.php","Possible limit query issue. ImageID: $imgID StartComment: $startComment Comments: $step Rows: $rowCt Page: $page SQL ERROR:" . print_r($stmt->errorInfo(),true), "failed to get comments");
		$rows = $stmt->fetchAll();

		echo "\n<table class=\"comment\" border=\"0\">";
		echo "\n<tr>";
		for ($i=0; $i<$rowCt; $i+=$step)
		{
			echo "\n<td>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\">>>></span>";
			echo "<a href='#' onclick=\"loadData('comments', 'getcomments.php?page=" . ($i/$step+1) . "&imgID=$imgID'); return false;\">Page ". ($i/$step+1) ."</a>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\"><<<</span>";
			echo "\n</td>";
		}
		echo "\n</tr>";
		echo "\n</table><br />";

		foreach ($rows as $row) {
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];

			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			$date = date("M d Y h:i:s A.",strtotime($date));
			echo "\n<span class=\"username\"><br /> $displayName on $date.";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='#' onclick='editComment($commentid); return false;'>edit</a>"; }
			echo "</span> </td> </tr>";
			echo "\n</table><br />";
		}
	} else {
		echo "\nNo comments have been posted";
	}
}
?>
