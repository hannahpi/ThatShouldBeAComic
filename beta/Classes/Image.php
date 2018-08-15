<?php


class Image {
    private $conn;
    private $table_name = 'Image';
    private $attributes;
    private $debugH;
    private $displayName;
    private $filePath;

    public $imgID;
    public $userID;
    public $fileName;
    public $name;
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
            "UserID" => $dbRow["UserID"],
            "FileName" => $dbRow["FileName"],
            "DisplayName" => $dbRow["DisplayName"],
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

    public function createNew($userID, $fileName, $imgID=NULL, $name=NULL,
        $date=NULL, $desc=NULL, $anonymous=false) {

        $query = " INSERT INTO Images (UserID, FileName, ImgID, Name, "
                    ."  Date, Desc, Anonymous ) "
                    ." VALUES (:userID, :fileName, :imgID, :name, "
                    ."  :date, :desc, :anonymous)";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":userID", $userID, PDO::PARAM_INT);
        $stmt->bindValue(":fileName", $fileName, PDO::PARAM_STR);
        $stmt->bindValue(":imgID", $imgID, PDO::PARAM_INT);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":date", $date, PDO::PARAM_INT);
        $stmt->bindValue(":desc", $desc, PDO::PARAM_STR);
        $stmt->bindValue(":anonymous", $anonymous, PDO::PARAM_BOOL);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Create new image failed", "Create Image Query failed.");
        if ($stmt->rowCount()==0)
            return;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $query = " SELECT DisplayName, UploadPath FROM Users "
                ." WHERE User.UserID = :userID ; ";

        $stmt->bindValue(":userID", $userID, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "image: Account information check failed", "Create Image Query failed. Account details missing.");
        if ($stmt->rowCount()==0)
            return;
        $rowUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $row["DisplayName"] = $rowUser["DisplayName"];
        $row["UploadPath"] = $rowUser["UploadPath"];
        return print_r(json_encode($this->interpretItem($row)),true);
    }

    public function setDisplayName($displayName) {
        if ($this->imgID) {
            if ($this->email != $email) {
                $this->email = $email;
                $this->dirty = true;
            }
        } else {
            throw new Exception('Set email for an uninitialized ' . get_class($this));
        }
    }

    public function getByID($id, $json=false) {
        $query = "SELECT i.ImgID, i.UserID, i.FileName, u.DisplayName,  i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
               . "FROM Images i, User u "
               . "Where i.ImgID = :id "
               . " AND i.UserID = u.UserID ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute() or $this->debugH->errormail("Unknown", "Get Image by Name failed", "Image Name Query failed.");
        if ($stmt->rowCount()==0) {
            if ($json)
                return json_encode(array("message" => "No image found by that id, $id"));
            else
                return;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $row = $this->interpretItem($row);
        if ($json) {
            return print_r(json_encode($row), true);
        } else {
            $this->imgID = $row["ImgID"];
            $this->userID= $row["UserID"];
            $this->fileName = $row["FileName"];
            if ($row["Anonymous"])
                $this->anonymous=true;
            else
                $this->displayName = $row["DisplayName"];
            $this->name = $row["Name"];
            $this->date = $row["Date"];
            $this->desc = $row["Desc"];
            $this->tags = $row["Tags"];
            $this->filePath = $row["UploadPath"];
        }

    }

    /* getByName
     * returns json of images that match the name.
     * or an array of image/s that match the name.
     */
    public function getByName($name, $json=true) {
        $query = " SELECT i.ImgID, i.UserID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ." FROM Images i, User u "
                ." WHERE i.Name LIKE :name "
                ." AND i.UserID = u.UserID ";

        $stmt = $this->conn->prepare($query, $this->attributes);
        $stmt->bindValue(":name", $name . "%", PDO::PARAM_STR);
        $stmt->execute() or $this->debugH->errormail("Unknown", "get by name failed", "Image Search Query failed.");
        $rowCt = $stmt->rowCount();
        if ($stmt->rowCount()==0) {
            if ($json)
                return json_encode(array("message" => "No images found by that name $name"));
            else
                return;
        } else if ($stmt->rowCount()==1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($json) {
                return print_r(json_encode($this->interpretItem($row)), true);
            } else {
                return array($this->interpretItem($row));
            }
        } else {
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                if (empty($imageArray)) {
                    $imageArray = array($this->interpretItem($row));
                } else {
                    array_push($userArray, $this->interpretItem($row));
                }
            }
            if ($json) {
                return print_r(json_encode($imageArray), true);
            } else {
                return $imageArray;
            }
        }
    }

    public function getJson($id) {
        $query = "SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ." FROM Images i, User u "
                ." WHERE Images.ImgID = :id "
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
        $query = " SELECT i.ImgID, u.DisplayName, i.FileName, i.Name, i.Date, i.Desc, i.Anonymous, u.UploadPath "
                ." FROM Images i, User u "
                ." WHERE i.UserID = u.UserID "
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
                array_push($imageArray, $row);
            }
        }
        return print_r(json_encode($imageArray), true);
    }
}

 ?>
