<?php

require_once '../config/config.php';

class DebugHelper {
    private $trackedObjects;

    public function addObject($obj) {
        if (empty($trackedObjects)) {
            $trackedObjects = array($obj);
        } else {
            array_push($trackedObjects, $obj);
        }
    }

    public function errormail($userEmail, $adminMessage, $userDieMessage) {
        $headers = "From: ". $GLOBALS['BUG_MAIL_NAME']. " <" . $GLOBALS['BUG_EMAIL'] .">";
        $subject = "Error for $userEmail";
        $errorInfo = print_r($trackedObjects, true);
        $adminMessage .= "\nDebug Helper was tracking these objects: \n $errorInfo \n ";
        mail($GLOBALS['ACTUAL_ADMIN'],$subject,$adminMessage,$headers);
        echo '<link rel="stylesheet" type="text/css" href="'. $GLOBALS['CSS'] . '" />';
        die("$userDieMessage");
    }
}


?>
