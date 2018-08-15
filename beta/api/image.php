<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../Classes/DebugHelper.php';
require_once '../Classes/Image.php';
require_once '../Classes/Database.php';
require_once '../config/config.php';

$database = new Database();
$conn = $database->getConnection();
$attributes = $database->getAttributes();


$myImage = new Image($conn, $attributes);
if (isset($_SESSION['Email'])) {
    $myUser->get($_SESSION['Email'], false);
}
$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
$debugH = new DebugHelper();
$debugH->addObject($method);
$debugH->addObject($request);
$debugH->addObject($myImage);

function update_image($conn, $attributes, $request) {
    $image = new Image($conn, $attributes);
    if (count($request) % 2 == 0) {
        for ($i=0; $i<count($request); $i=$i+2) {
            $imageInfo[$request[i]] = strip_tags($request[i+1]);
        }
    }
    if ($imageInfo["ImgID"]) {
        $image->getByID($imageInfo["ImgID"]);
        $image->setEmail($imageInfo["Email"]);
    } else if ($imageInfo["Email"]) {
        $image->get($imageInfo["Email"],false);
    }
/*  $image->setDisplayName($imageInfo["DisplayName"]);
    $image->setFirstName($imageInfo["FirstName"]);
    $image->setLastName($imageInfo["LastName"]);
    $image->setCreationDate($imageInfo["CreationDate"]);
    $image->setUploadPath($imageInfo["UploadPath"]);
    $image->updateDB();  */
    print_r($request);
}

function add_image($conn, $attributes, $request) {
    $image = new Image($conn, $attributes);
    if (count($request) % 2 == 0) {
        for ($i=0; $i<count($request); $i=$i+2) {
            $imageInfo[$request[i]] = $request[i+1];
        }
    }
/*    $email = $imageInfo["Email"];
    $displayName = $imageInfo["DisplayName"];
    $firstName = $imageInfo["FirstName"];
    $lastName = $imageInfo["LastName"];
    $image->createImage($email, $displayName, $firstName, $lastName); */
    print_r($request);
}

function get_image($conn, $attributes, $request) {
    $image = new Image($conn, $attributes);
    switch (strtolower($request[0])) {
        case "image":
            $rImage = str_replace("%20", " ", str_replace("-", " ", strip_tags($request[1])));
            break;
        case "getall":
            echo $image->getAllJson();
            return;
        case "matchDesc":
            echo $image->getAllJson($request[1]);
            return;
        default:
            $rID = strip_tags($request[0]);
            break;
    }
    if (isset($rID)) {
        echo $image->getByID($rID, true);
    } else if (!empty($rImage)) {
        echo $image->getByName($rImage);
    }
}


switch ($method) {
    case 'PUT':
        update_image($conn, $attributes, $request);
        break;
    case 'POST':
        add_image($conn, $attributes, $request);
        break;
    case 'GET':
        get_image($conn, $attributes, $request);
        break;
    default:
        print_r(json_encode(array("message" => "Invalid method received")));
        $debugH->errormail($myUser->email, "API Call to image", "Invalid Image API call");



}



 ?>
