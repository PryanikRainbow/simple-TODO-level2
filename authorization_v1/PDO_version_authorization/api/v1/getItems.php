<?php

session_start();

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Credentials: true');
include('connect.php');
$connectDB = getDBConnect();

$itemsDB = $connectDB->prepare("SELECT * FROM items WHERE userID = :userID");
$itemsDB->execute(['userID' => $_SESSION['userID']]);
$result = $itemsDB->fetchAll(PDO::FETCH_ASSOC);
$itemsDBArray = ['items' => $result];

$connectDB = null;

header('Content-Type: application/json');
echo json_encode($itemsDBArray);
