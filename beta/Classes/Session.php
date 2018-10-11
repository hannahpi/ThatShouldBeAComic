<?php
session_start();
require_once 'DebugHelper.php';
require_once '../config/config.php';

class Session {
    private $conn;
    private $table_name = 'Session';
    private $attributes;
    private $debugH;
    private $sessionID;
    private $userID; //primary key, no reason to change this.
    private $dirty;

    public $ipAddr;
    public $fingerprint;
    public $lastAction;
    public $lastActionTime;
    public $loginTime;

    /**
     * function: interpretItem
     * purpose: converts extracted data from db to an array.
     */
    private function interpretItem($dbRow) {
        $dbSession = array(
            "SessionID" => $dbRow["SessionID"],
            "UserID" => $dbRow["UserID"],
            "ipAddr" => $dbRow["IP_Address"],
            "fingerprint" => $dbRow["Fingerprint"],
            "lastAction" => $dbRow["LastAction"],
            "lastActionTime" => $dbRow["LastActionTime"],
            "loginTime" => $dbRow["LoginTime"]
        );
        return $dbSession;
    }

    public function __construct($conn, $attributes) {
        $this->attributes = $attributes;
        $this->conn = $conn;
        $this->debugH = new DebugHelper(true);
        $this->debugH->addObject($this);
        $this->dirty = false;
    }

    /**
     * function: createNew
     *
     */
    public function createNew($userID, $ipAddr, $lastAction="login", $lastActionTime=NULL, $loginTime=NULL) {
        $this->userID = $userID;
        $this->ipAddr = $_SERVER['REMOTE_ADDR'];
        $this->fingerprint = hash_hmac('sha256', $_SERVER['HTTP_USER_AGENT'], hash('sha256', $_SERVER['REMOTE_ADDR'], true));
        $this->loginTime = time();
        $_SESSION["lastActionTime"] = time();
        $_SESSION["lastAction"] = "login";
        $_SESSION["loginTime"] = time();
        $_SESSION["fingerprint"] = $this->fingerprint;

        $query = " INSERT INTO Session (UserID, IP_Address, Fingerprint, LastAction, LastActionTime, LoginTime) "
                ." VALUES (:userID, :ipAddr, :fingerprint, :lastAction, :lastActionTime, :loginTime ) ;";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":sessionID", $this->sessionID, PDO::PARAM_INT);  //this should be NULL
        $stmt->bindValue(":ipAddr", $this->ipAddr, PDO::PARAM_STR);
        $stmt->bindValue(":fingerprint", $this->fingerprint, PDO::PARAM_STR);
        $stmt->bindValue(":lastAction", $this->lastAction, PDO::PARAM_STR);
        $stmt->bindValue(":lastActionTime", $this->lastActionTime, PDO::PARAM_INT);
        $stmt->bindValue(":loginTime", $this->loginTime, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Create new session failed", "Create Session Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return print_r(json_encode($this->interpretItem($row)),true);
    }

    public function setAction($action) {
        $this->lastAction = $action;
        $this->lastActionTime = time();
        $this->dirty = true;
    }

    public function getBySession($sessionID, $json=false) {
        $query = "SELECT SessionID, UserID, IP_Address, Fingerprint, LastAction, LastActionTime, LoginTime "
               . " FROM Session "
               . " Where Session.SessionID = :id ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":id", $sessionID, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get by ID failed", "Session Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($json) {
            return print_r(json_encode($this->interpretItem($row)), true);
        } else {
            $this->sessionID= $row["SessionID"];
            $this->userID = $row["UserID"];
            $this->ipAddr = $row["IP_Address"];
            $this->fingerprint = $row["Fingerprint"];
            $this->lastAction = $row["LastAction"];
            $this->lastActionTime = $row["LastActionTime"];
            $this->loginTime = $row["LoginTime"];
        }
    }

    public function get($userID, $json=false) {
        $query = " SELECT SessionID, UserID, IP_Address, Fingerprint, LastAction, LastActionTime, LoginTime "
               . " FROM Session "
               . " Where Session.UserID = :id "
               . " ORDER BY LoginTime DESC ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":id", $userID, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get by ID failed", "Session Query failed.");
        if ($stmt->rowCount()==0)
            return;
        if ($json) {
            $rows= $stmt->fetchAll();
            foreach ($rows as $row)
            {
                if empty($sessionsArray) {
                    $sessionsArray = array($this->interpretItem($row));
                } else {
                    array_push($sessionsArray, $this->interpretItem($row));
                }
            }
            return print_r(json_encode($sessionsArray), true);
        } else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->sessionID= $row["SessionID"];
            $this->userID = $row["UserID"];
            $this->ipAddr = $row["IP_Address"];
            $this->fingerprint = $row["Fingerprint"];
            $this->lastAction = $row["LastAction"];
            $this->lastActionTime = $row["LastActionTime"];
            $this->loginTime = $row["LoginTime"];
        }
    }

    public function updateDB() {
        if (isset($this->sessionID) && $this->dirty) {
            $query = " Update `Session` set IP_Address = :ipAddr, Fingerprint = :fingerprint, "
                   . " LastAction = :lastAction, LastActionTime = :lastActionTime, "
                   . " CreationDate = :creationDate, "
                   . " UploadPath = :uploadPath "
                   . " WHERE `Session`.SessionID = :sessionID ;";

            $stmt = $this->conn->prepare($query, $this->attributes);
            $stmt->bindValue(":ipAddr", $this->ipAddr, PDO::PARAM_STR);
            $stmt->bindValue(":fingerprint", $this->fingerprint, PDO::PARAM_STR);
            $stmt->bindValue(":lastAction", $this->lastAction, PDO::PARAM_STR);
            $stmt->bindValue(":lastActionTime", $this->lastActionTime, PDO::PARAM_INT);
            $stmt->execute() or $this->debugH->errormail("Unknown", "Update Session failed", "Update Session Query failed.");
            if ($stmt->rowCount() == 0)
                return json_encode(array("message"=>"already up to date!"));
            else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return print_r(json_encode($this->interpretItem($row)),true);
            }
        } else {
            return json_encode(array("message"=>"no changes found to update!"));
        }
    }

    ?>
