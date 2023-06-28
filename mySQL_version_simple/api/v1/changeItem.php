<?php

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $id = $requestBody['id'];
    $title = $requestBody['title'];
    $text = $requestBody['text'];
    $checked = (int)$requestBody['checked'];

    $itemsDB = $connectDB->prepare("UPDATE items SET title = ?, text = ?, checked = ? WHERE id = ?");
    $itemsDB->execute([$title, $text, $checked, $id]);

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
    $count = $itemsDB->rowCount();
    return ($count > 0);

}
