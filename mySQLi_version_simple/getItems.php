<?php

include('connect.php');

$connectDB = getDBConnect();

header('Content-Type: application/json');

$itemsBD = $connectDB->prepare("SELECT * FROM items");
$itemsBD->execute();
$result = $itemsBD->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($items);

closeConnect($connectDB);