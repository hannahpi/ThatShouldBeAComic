<?php session_start(); ?>
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />

<?php
//PHP SCRIPT: getcomments.php
date_default_timezone_set("America/New_York");

$step = 5;
$ses_email = $_SESSION['email'];
$fileName= $_GET['fileName'];
$order= strip_tags($_GET['order']);
$page = strip_tags($_GET['page']);
$goBack = $_SESSION['lastPage'];


if ($fileName)
{
	$toSend = "SELECT `comments`.CommentID, `comments`.CommentDate, `comments`.Comment,
	           `User`.DisplayName, `User`.Email FROM `comments`,`User`
	           WHERE `User`.Email = `comments`.Email
	           AND `comments`.FileName = :fileName
			   AND `User`.UserLevelID > 0
	           ORDER BY `comments`.CommentDate ";

	$connect = connect_tsbac();
	$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
	if ($order == "desc") {
		$toSend .= " :order ";
		$stmt->execute(array(":fileName"=>$fileName, ":order"=>$order));
	} else {
		$stmt->execute(array(":fileName"=>$fileName));
	}

	echo "\n <br />";
	$totnumrows= $stmt->rowCount();


	if ($totnumrows!=1)
	{
		echo "$totnumrows comments submitted <br />\n";
	}
	else
	{
		echo "$totnumrows comment submitted <br />\n";
	}

	if (($totnumrows>0)&&($totnumrows<=5))
	{
		$rows = $stmt->fetchAll();
		foreach ($rows as $row)
		{
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];
                        $date = date("M d Y h:i:s A.",strtotime($date));


			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			echo "\n<span class=\"username\"><br /> $displayName on $date";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='editcomment.php?id=$commentid'>edit</a>"; }
			echo "</span> </td> </tr>";
			echo "\n</table><br />";

		}
	}
	else if ($totnumrows>5)
	{
		$startComment = $totnumrows - 5;
		if (empty($page))
		{
			$toSend .= "LIMIT :startComment , :step ;";
			$page = ceil($totnumrows/5);
		}
		else
		{
			$startComment = ($page -1) * $step;
			$toSend .= "LIMIT :startComment, :step ;";
		}

		$connect = connect_tsbac();
		$stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
		$stmt->bindValue(":fileName", $fileName, PDO::PARAM_STR);
		$stmt->bindValue(":startComment", $startComment, PDO::PARAM_INT);
		$stmt->bindValue(":step", $step, PDO::PARAM_INT);
		if (!empty($order)) {
			$stmt->bindValue(":order", $order, PDO::PARAM_STR);
		}
		$stmt->execute() or errormail($email, "(More than 5 comments) Query failure in viewcomments.php","Query Error in viewcomments", "View Comment Query Error");

		$numrows= $stmt->rowCount();
		echo "\n<table class=\"comment\" border=\"0\">";
		echo "\n<tr>";
		echo "\n<td> <a href='$goBack' alt='Go back to previous main page'>go back</a> | </td>";
		for ($i=0; $i<$totnumrows; $i+=$step)
		{
			echo "\n<td>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\">>>></span>";
			echo "<a href='viewcomments.php?page=" . ($i/$step+1) . "&fileName=$fileName'>Page ". ($i/$step+1) ."</a>";
			if (($i/$step)==$page-1)
				echo "<span class=\"small\"><<<</span>";
			echo "\n</td>";
		}
		echo "\n</tr>";
		echo "\n</table><br />";

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$commentid= $row['CommentID'];
			$date = $row['CommentDate'];
			$comment = $row['Comment'];
			$displayName = $row['DisplayName'];
			$dbemail = $row['Email'];
            $date = date("M d Y h:i:s A.",strtotime($date));

			echo "\n<table class=\"comment\" border=\"0\">";
			echo "\n<tr> <td><span class=\"comments\"> $comment </span> ";
			echo "\n<span class=\"username\"><br /> $displayName on $date";
			if ($dbemail==$ses_email)
			{   echo "<br><a href='editcomment.php?id=$commentid'>edit</a>"; }
			echo "</span> </td> </tr>";
			echo "\n</table><br />";
		}
	}
	else
	{
		echo "\nNo comments have been posted";
	}
}

?>
