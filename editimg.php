<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--- header and style definitions -->
<head>
<title>Edit Image - ThatShouldBeAComic.com</title>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<link type="text/css" href="jquery/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25075932-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php
require_once 'config.php';
$email = $_SESSION['email'];
$curimage=$_GET['image'];
if (!$curimage)
{
	$_SESSION['lastPage'] = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
else
{
	$_SESSION['lastPage'] = $GLOBALS['FQP'] . "/editimg.php?image=$curimage";
}
?>
<script src="getimage.php?image=<?php echo $curimage ?>"></script>
</head>

<!--- Content-->
<body>
<a href="index.php">
  <img src="tsbacbanner.png" style="margin-left:10%; margin-right:10%; width:80%; opacity:.8;" />
  </a>

<div align="center">
<?php require($DOCUMENT_ROOT . "mainmenu.html"); ?>
<br />
<!------Comic Viewer------------>
<a class="comicview" href="#">
	<canvas id="canvas" name="canvas"></canvas>
	<span>
		<!-- *** Code to show box when hover -->
		<div align="center">
		<table id="infobox" class="imgdesc">
		<tr> <td><span id="filedesc"></span> </tr> </td>
		</table>
		</div>
	</span>
</a>
<!----------------------------->
<?php require($DOCUMENT_ROOT . "rotatemenu.html"); ?>
</div>
<p class="clear">
<script type="text/javascript" src="phpget.js"></script>
<script type="text/javascript" src="cview.js"></script>
<script type="text/javascript">
window.onload=function(){
	init(0);
}
</script>
</p>

<?php
$displayName= $_SESSION['displayName'];
$submit = $_POST['submit'];

if ($submit)
	$dbemail = strip_tags($_POST['Email']);

if (isset($curimage))
{
	$toSend = "SELECT `Name`, `Desc`, `Date` FROM `Images` WHERE `ImgID` = :curimage AND `Email` = :email;";
    try {
        $connect = connect_tsbac();
        $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
        $stmt->execute(array(':curimage'=>$curimage, ':email'=>$email))
            or errormail($email,"Failed to execute query.  editimg.php","edit image failed, image query failed!","image not found");
        $connect=null; // disconnect
    } catch (PDOException $e) {
        errormail($email, $e->getMessage(), $message, $e->getMessage());
    }
    $rowCt = $stmt->rowCount();
    if ($rowCt == 0)
        die("\nImage Not Found?  Are you sure you're logged in?");
    else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbname = $row['Name'];
        $dbdesc = $row['Desc'];
        $dbdate = $row['Date'];
    }
}
$date = strtotime(strip_tags($_POST['date']));
$imageName = strip_tags($_POST['imageName']);
$desc = strip_tags($_POST['desc']);
$userlevel = $_SESSION['userlevel'];
$goBack = $_SESSION['lastPage'];
$target_path = $_SESSION['UploadPath'];
if (substr($target_path,0,1)=="/")
{	$target_path = substr($target_path,1,strlen($target_path)-1); }
if (substr($target_path,strlen($target_path)-2,1)!='/')
{	$target_path.= '/'; }

if (isset($_FILES['uploadedfile'])) {
	$file_path = $target_path . basename( $_FILES['uploadedfile']['name']);
    $uploadNeeded = true;
} else {
	$uploadNeeded = false;
}


if ($submit)
{
	if ($email&& $curimage && ($userlevel>=50) && ($uploadNeeded))
	{
		if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $file_path)) {
			echo "<br />The file ".  basename($_FILES['uploadedfile']['name'])
				. " has been uploaded";
			$fileName = $_FILES['uploadedfile']['name'];
            $connect = connect_tsbac();
            if ($date) {
			    $toSend = "Update `Images` "
			       ." Set `Filename`= :fileName, `Name`=:imageName, `Date`=FROM_UNIXTIME(:date), "
			       ."     `Desc` = :desc "
				   ."    WHERE `ImgID` = :curimage ";
            } else {
			    $toSend = "Update `Images` "
			       ." Set `Filename`=:fileName, `Name`=:imageName, "
				   ."     `Desc` = :desc "
				   ."    WHERE `ImgID` = :curimage ";
			}
            //dbug helper:
                $message= "fileName: $fileName \n target_path: $target_path \n imageName: $imageName \n submit: $submit \n email: $email";
                $message.= "\n desc: $desc \n Date: $date  \n GoBack: $goBack \n Sending: \n $toSend";

            try {
                $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
                $stmt->bindValue(":fileName", $fileName, PDO::PARAM_STR);
                $stmt->bindValue(":imageName", $imageName, PDO::PARAM_STR);
                if ($date) $stmt->bindValue(":date", $date, PDO::PARAM_INT);
                $stmt->bindValue(":desc", $desc, PDO::PARAM_STR);
                $stmt->bindValue(":curimage", $curimage, PDO::PARAM_STR);
                $stmt->execute()
                    or errormail($email, $message,"edit image failed, image query failed!","image not found");
                $connect=null; // disconnect
            } catch (PDOException $e) {
                errormail($email, $message, $e->getMessage(), "Image could not be updated.");
            }
			echo "<br /><a href=$goBack>Go back</a>";
		} else {
			//dbug helper:
				$message= "The file " . $_FILES['uploadedfile']['tmp_name'] . " could not be uploaded to $target_path " . basename( $_FILES['uploadedfile']['name']);
				$usermsg = $message;
				$message.= "fileName: $fileName \n target_path: $target_path \n imageName: $imageName \n submit: $submit \n email: $email";
				$message.= "\n desc: $desc \n Date: $date  \n GoBack: $goBack";

			errormail($email,$message,"move failed", $usermsg);
		}
	} else if ($curimage && $email && ($userlevel>=50)) {
        $connect = connect_tsbac();
        if ($date) {
            $toSend = "Update `Images` "
               ." Set `Name`=:imageName, `Date`=FROM_UNIXTIME(:date), "
               ."     `Desc` = :desc "
               ."    WHERE `ImgID` = :curimage ";
        } else {
            $toSend = "Update `Images` "
               ." Set `Name`=:imageName, "
               ."     `Desc` = :desc "
               ."    WHERE `ImgID` = :curimage ";
        }
			//dbug helper:
				$message= "fileName: $fileName \n target_path: $target_path \n imageName: $imageName \n submit: $submit \n email: $email";
				$message.= "\n desc: $desc \n Date: $date  \n GoBack: $goBack \n Sending: \n $toSend";

            try {
                $stmt = $connect->prepare($toSend, $GLOBALS['PDO_ATTRIBS']);
                $stmt->bindValue(":imageName", $imageName, PDO::PARAM_STR);
                if ($date) $stmt->bindValue(":date", $date, PDO::PARAM_INT);
                $stmt->bindValue(":desc", $desc, PDO::PARAM_STR);
                $stmt->bindValue(":curimage", $curimage, PDO::PARAM_STR);
                $stmt->execute()
                    or errormail($email, $message,"edit image failed, image query failed!","image not found");
                $connect=null; // disconnect
            } catch (PDOException $e) {
                errormail($email, $message, $e->getMessage(), "Image could not be updated.");
            }
			echo "<br /><a href=$goBack>Go back</a>";
	}
	else if ($userlevel < 50)
	{
		errormail($email,"email: $email \n userlevel $userlevel", "insufficient user priveleges", "Permission Denied.  You have insufficient priveleges");
	}
	else if (empty($email))
	{
		errormail("Not logged in (no user)","$message","email: $email \n Go back: $goBack","probably didn't login","Are you sure you logged in?  <a href=$goBack>Go back</a>");
	}
	else
		errormail("Blank field detected!","$message","email: $email", "I'm not sure what you just did, try again? <a href=$goBack>Go back</a>") ;
}

?>
<form enctype="multipart/form-data" action="editimg.php?image=<?php if ($curimage) echo $curimage ?>" method="POST">
  <table>
  <tr>
    <input type='hidden' name='Email' <?php if ($dbemail) echo "value='$dbemail'";?>></input><?php if ($dbemail) echo "$dbemail";?>
	<td>If new, choose a file to upload:</td>
	<td><input name="uploadedfile" type="file" /></td>
  </tr>
  <tr>
	<td>Date Override:</td>
	<td><input name="date" id="date" type="text" <?php if ($dbdate) echo 'value="' . htmlspecialchars($dbdate) . '"'; ?> />
  </tr>
  <tr>
    <td>Name:</td>
	<td><input name="imageName" type="text" <?php if ($dbname) echo 'value="' . htmlspecialchars($dbname) . '"'; ?> />
  </tr>
  <tr>
    <td>Description:</td>
	<td><input name="desc" type="text" <?php if ($dbdesc) echo 'value="' . htmlspecialchars($dbdesc) . '"'; ?> />
  </tr>
  <tr>
    <td></td><td><input type="submit" name="submit" id="submit" value="Save Changes" /></td>
  </tr>
</table>


</form>
<script>
	$(function() {
		$( "#date" ).datepicker({ dateFormat: 'yy-mm-dd' });;
	});
	$.datepicker.setDefaults({
		showOn: 'both',
		buttonImageOnly: true,
		buttonImage: 'jquery/images/calendar.gif',
		changeYear: true,
		yearRange: 'c-10:+0',
		buttonText: 'Calendar' });
</script>
<br>
<br>
<br>
<br>
</body>
</html>
