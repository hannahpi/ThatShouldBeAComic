<?php

require_once '../config/config.php';

class Database {
    private $hostname;
    private $username;
    private $password;
    private $db;
    private $attributes;

    function getConnection() {
    	try {
    		$dbh = new PDO("mysql:host=$this->hostname;dbname=$this->db", $this->username, $this->password);
    		return $dbh;
        } catch(PDOException $e) {
            errormail($email, $e->getMessage(), "No info", $e->getMessage());
        }
    }

    function getAttributes() {
        return $this->attributes;
    }

    function __construct() {
        $this->hostname = $GLOBALS['DB_HOSTNAME'];
        $this->username = $GLOBALS['DB_FULLUSER'];
        $this->password = $GLOBALS['DB_PASSWORD'];
        $this->db = $GLOBALS['DB_NAME'];
        $this->attributes = $GLOBALS['PDO_ATTRIBS'];
    }

}


 ?>
