<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--- header and style definitions -->
<title>Upload - ThatShouldBeAComic.com</title>
<head>
<!--[if lt IE 9]>
    <script src="http://www.dreamreign.com/include/excanvas.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
</head>

<?php
require_once 'config.php';
date_default_timezone_set("America/New_York");
$email = $_SESSION['email'];
$date = strtotime(strip_tags($_POST['date']));
if (!($date))
{	$date = date("Y-m-d H:i:s"); }
$imageName = $_POST['imageName'];
$desc = $_POST['desc'];
$anonymous = $_POST['anonymous'];
$userlevel = $_SESSION['userlevel'];
$goBack = $_SESSION['lastPage'];
$target_path = $_SESSION['UploadPath'];
if (substr($target_path,0,1)=="/")
{	$target_path = substr($target_path,1,strlen($target_path)-1); }
if (substr($target_path,strlen($target_path)-2,1)!='/')
{	$target_path.= '/'; }
$target_path = $GLOBALS['BASE_FILE_UPLOAD_PATH'] . $target_path;

if ($email)
{
  if($userlevel>=50)
  {
	$numFiles = count($_FILES['uploadedfile']['tmp_name']);
	for ($i=0; $i< $numFiles; $i++)
	{
		$file_path = $target_path . basename( $_FILES['uploadedfile']['name'][$i]);
		if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'][$i], $file_path)) {
			echo "<br />The file ".  basename( $_FILES['uploadedfile']['name'][$i]).
				" has been uploaded";
			$imageName[$i] = strip_tags( $imageName[$i] ) ;
			$desc[$i] = strip_tags( $desc[$i] ) ;
			if (empty($anonymous[$i]))
			{	$anonymous[$i] = 0; }
			else
			{   $anonymous[$i] = 1; }
			$fileName[$i] = $_FILES['uploadedfile']['name'][$i];

			try
			{
				$hostname = "localhost";
				$username = $GLOBALS['DB_FULLUSER'];
				$password = $GLOBALS['DB_PASSWORD'];
				$db = $GLOBALS['DB_NAME'];

				$dbh = new PDO("mysql:host=$hostname;dbname=$db", $username, $password);
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt = $dbh->prepare("INSERT INTO `Images` Values(NULL, :fileName, :imageName, :email, NULL, :desc, :anonymous );");
				$stmt->execute( array (':fileName'=>$fileName[$i], ':imageName'=>$imageName[$i], ':email'=>$email, ':desc'=> $desc[$i], ':anonymous'=> $anonymous[$i]));
				$connect=null; // disconnect
			} catch (PDOException $e) {
				errormail($email, $e->getMessage(), "Problem saving to database! ", $e->getMessage());
			}
		} else {
			//dbug helper:
				$message= "The file" . $_FILES['uploadedfile']['tmp_name'][$i] . " could not be uploaded to $file_path " . basename( $_FILES['uploadedfile']['name'][$i]);
				$usermsg = $message;
				$message.= "fileName: $fileName[$i] \n file_path: $file_path \n imageName: $imageName \n submit: $submit \n email: $email";
				$message.= "\n desc: $desc \n Date: $date  \n GoBack: $goBack";

				errormail($email,$message,"move failed", $usermsg);
		}
	}
	echo "<br /><a href=$goBack>Go back</a>";
   }
else
	errormail($email,"email: $email \n userlevel $userlevel", "insufficient user priveleges", "Permission Denied.  You have insufficient priveleges");
}
else
{
	errormail("Not logged in (no user)","email: $email \n Go back: $goBack","probably didn't login","Are you sure you logged in?  <a href=$goBack>Go back</a>");
}
?>
