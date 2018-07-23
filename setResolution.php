<?php
session_start();
$max_width = $_GET['max'];
if (isset ($_SESSION['email']))
{
	if (isset ($_GET['max']))
		$_SESSION['max_width'] = $max_width;
	echo "/* set to $max_width */";
}
else
	echo "/* cannot set! */";





?>