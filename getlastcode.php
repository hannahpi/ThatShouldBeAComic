<?php
//PHP SCRIPT: getimages.php
//Header("content-type: application/x-javascript");
date_default_timezone_set("America/New_York");
//This function finds the most recent html/php file edit date.
//returns as unix timestamp.
function returndate($dirname=".") {
	$pattern="(\.htm$)|(\.html$)|(\.php$)|(\.css$)|(\.js$)"; //valid image extensions
	//$files = array();
	$curimage=0;
	$newest = -1;
	if($handle = opendir($dirname)) {
		while(false !== ($file = readdir($handle))){
			if(eregi($pattern, $file)){ //if this file is a valid image
				//Check the date.
				if (($curdate=filemtime($file)) > $newest)
				{
					$newest = $curdate;
				}
			}
		}
		closedir($handle);
	}
	return($curdate);
}

echo 'Code last updated:'. date("F d Y H:i:s.",returndate());  //Output the last mod date.
?>
