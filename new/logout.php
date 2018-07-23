<?php
session_start();

$headBack = $_SESSION['lastPage']; 
session_unset();
session_destroy();
if ($headBack)
{
	$headBack = "Location: $headBack";
}
header($headBack);

?>