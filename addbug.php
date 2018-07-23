<?php session_start(); ?>
<link rel="stylesheet" type="text/css" href="thatshouldbeacomic.css" />
<?php
require_once 'config.php';

date_default_timezone_set("America/New_York");
$email = $_SESSION['email'];
if (empty($email))
{
	$email = $_POST['email'];
}
$info = $_POST['info'];
$date = date("Y-m-d H:i:s");
$submit = $_POST['submit']; 
$headBack = $_SESSION['lastPage']; 
if ($headBack)
{
	$headBack = "Location: $headBack";
}

if ($info)
{
	$lastPage=$_SESSION['lastPage'];
	errormail($email, $info, "bug report submitted: $date on ( $lastPage )", "bug report submitted <a href=$goBack>Go back</a>");
	header($headBack);
}
else
{
	echo "<form action='addbug.php' method='POST'>";
}


?>


 <table>		
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<?php if ($email) echo "<!--"?><input type='text' name='Email'></input><?php if ($email) echo "--> $email";?>
			</td>
		</tr>
		<tr>
			<td>
			     Description of bug:
			</td>
			<td>
				<textarea rows="5" cols="50" name='info'></textarea>
			</td>
		</tr>
		<tr>
			<td></td><td><input type='submit' name='submit' onclick="addBugSubmit(); return false;" value='Submit Bug'></td>
		</tr>
	</table>	
</form>