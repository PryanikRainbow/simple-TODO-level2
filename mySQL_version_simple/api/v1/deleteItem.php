<?php

include('connect.php');
/*
TODO add chech equals text/title
*/

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $idDeleted = $requestBody['id'];

    $itemsDB = $connectDB->prepare("DELETE FROM items WHERE id = ?");
    $itemsDB->execute([$idDeleted]);

    $updatedIndexes = "UPDATE items SET id = id - 1 WHERE id > ?";
    $itemsDB = $connectDB->prepare($updatedIndexes);
    $itemsDB->execute([$idDeleted]);

    $itemsDB = $connectDB->query("ALTER TABLE items AUTO_INCREMENT = 1");
    // $itemsDB = $connectDB->query("ALTER TABLE items AUTO_INCREMENT = (SELECT MAX(id) FROM items) + 1");

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody, $connectDB)) {
    echo json_encode(['error' => 'Not Found']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

$connectDB = null;

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}

function isValidID($requestBody, $connectDB)
{
    $itemsDB = $connectDB->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
    $itemsDB->execute([$requestBody['id']]);
    $count = $itemsDB->fetchColumn();
    return ($count > 0);
}
