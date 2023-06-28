<?php

include('connect.php');
$connectDB = getDBConnect();

$itemsDB = $connectDB->prepare("SELECT * FROM items");
$itemsDB->execute();
$result = $itemsDB->fetchAll(PDO::FETCH_ASSOC);
$itemsDBArray = ['items' => $result];

$connectDB = null;
header('Content-Type: application/json');
echo json_encode($itemsDBArray);
