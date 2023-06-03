<?php

//JUST FOR ME

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');

if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $idDeleted = $requestBody['id'];

    $itemsDB = $connectDB->prepare("DELETE FROM users WHERE userID = ?");
    $itemsDB->execute([$idDeleted]);

    $updatedIndexes = "UPDATE users SET userID = userID - 1 WHERE userID > ?";
    $itemsDB = $connectDB->prepare($updatedIndexes);
    $itemsDB->execute([$idDeleted]);

    $itemsDB = $connectDB->query("ALTER TABLE users AUTO_INCREMENT = 1");

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
    $itemsDB = $connectDB->prepare("SELECT * FROM users WHERE userID = ? LIMIT 1");
    $itemsDB->execute([$requestBody['id']]);
    $count = $itemsDB->fetchColumn();
    return ($count > 0);
}
