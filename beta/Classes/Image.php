<?php


class Image {
    private $conn;
    private $table_name = 'Image';
    private $attributes;
    private $debugH;

    public $imgID;
    public $userID;
    public $fileName;
    public $name;
    public $email;
    public $date;
    public $desc;
    public $anonymous;
    public $tags;

    /**
     * function: interpretItem
     * purpose: converts extracted data from db to an array.
     */
    private function interpretItem($dbRow) {
        $filedesc = $dbRow["Desc"];
        //separate tags from the description of the image.
        if ($filedesc[0] == '@')
        {
            $ct = 0;
            // finds the index
            while (($filedesc[$ct++] != ' ')&& ($ct < strlen($filedesc)));
            $tags = str_replace("@", "", substr($filedesc,0,$ct - 1));
            $filedesc=substr_replace($filedesc,"",0,$ct);
        }
        $dbImage = array(
            "ImgID" => $dbRow["ImgID"],
            "DisplayName" => $dbRow["DisplayName"],
            "FileName" => $dbRow["FileName"],
            "Name" => $dbRow["Name"],
            "Date" => $dbRow["Date"],
            "Desc" => $filedesc,
            "Tags" => $tags,
            "FilePath" => $dbRow["UploadPath"],
            "Anonymous" => $dbRow["Anonymous"]
        );
        return $dbImage;
    }

    public function __construct($conn, $attributes) {
        $this->attributes = $attributes;
        $this->conn = $conn;
        $this->debugH = new DebugHelper();
        $this->debugH->addObject($this);
    }

    public function getByID($id) {
        $query = "SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
               . "FROM Images i, User u "
               . "Where Images.ImgID = :id "
               . " AND Images.UserID = User.UserID ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get Image by Name failed", "Image Name Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $row = $this->interpretItem($row);
        $this->imgID = $row["ImgID"];
        $row["Anonymous"] ? $this->anonymous=true : $this->displayName = $row["DisplayName"];
        $this->fileName = $row["FileName"];
        $this->name = $row["Name"];
        $this->date = $row["Date"];
        $this->desc = $row["Desc"];
        $this->tags = $row["Tags"];
    }

    /* getByName
     * returns json of images that match the name.
     * or an array of image/s that match the name.
     */
    public function getByName($name, $json=true) {
        $query = "SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ."FROM Images i, User u "
                ."WHERE Images.Name = :name "
                ." AND Images.UserID = User.UserID ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get by ID failed", "User Query failed.");
        if ($stmt->rowCount()==0)
            return;
        else if ($stmt->rowCount()==1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($json) {
                print_r(json_encode($this->interpretItem($row)), true)
                return;
            } else {
                return array($this->interpretItem($row));
            }
        } else {
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                if (empty($imageArray)) {
                    $imageArray = array($this->interpretItem($row))
                } else {
                    array_push($userArray, $this->interpretItem($row));
                }
            }
            if ($json) {
                print_r(json_encode($imageArray));
                return;
            } else {
                return $imageArray;
            }
        }
    }

    public function getJson($id) {
        $query = "SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ."FROM Images i, User u "
                ."WHERE Images.Name = :name "
                ." AND Images.UserID = User.UserID ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get Image by ID failed", "Image Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return print_r(json_encode($this->interpretItem($row)), true);
    }

    public function getAllJson($descMatch="") {
        $query = "SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ."FROM Images i, User u "
                ." AND i.UserID = u.UserID "
                ." AND i.Desc LIKE :descMatch ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":descMatch", $descMatch . "%", PDO::PARAM_STR);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Images All Json", "Image Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            if (empty ($imageArray))
                $imageArray = array($this->interpretItem($row));
            else {
                $row = $this->interpretItem($row);
                array_push($imageArray, $row)
            }
        }
        return print_r(json_encode($imageArray), true);
    }
}

 ?>
