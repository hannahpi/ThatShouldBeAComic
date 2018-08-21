<?php

require_once '../config/config.php';

class DebugHelper {
    private $trackedObjects;
    private $debugMode;

    public function addObject($obj) {
        if (!empty($this->trackedObjects)) {
            array_push($this->trackedObjects, $obj);
        } else {
            $this->trackedObjects = array($obj);
        }
    }

    public function setTesting($debugMode=false) {
        $this->debugMode = $debugMode;
    }

    public function errormail($userEmail, $adminMessage, $userDieMessage) {
        $headers = "From: ". $GLOBALS['BUG_MAIL_NAME']. " <" . $GLOBALS['BUG_EMAIL'] .">";
        $subject = "Error for $userEmail";
        $errorInfo = print_r($this->trackedObjects, true);
        $adminMessage .= "\nDebug Helper was tracking these objects: \n $errorInfo \n ";
        mail($GLOBALS['ACTUAL_ADMIN'],$subject,$adminMessage,$headers);
        if ($this->debugMode)
            die("Debug Mode is on.  $adminMessage");
        else
            die("$userDieMessage");
    }

    public function __construct($debugMode=false) {
        $this->debugMode = $debugMode;
    }
}


?>
