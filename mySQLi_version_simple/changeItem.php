<?php

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $id = $requestBody['id'];
    $text = $connectDB->real_escape_string($requestBody['text']);
    $checked = (int)$requestBody['checked'];

    $itemsDB = $connectDB->prepare("UPDATE items SET  text = ?, checked = ? WHERE id = ?");
    $itemsDB->bind_param("sii", $text, $checked, $id);
    $itemsDB->execute();

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody, $connectDB)) {
    echo json_encode(['error' => 'Not Found']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

closeConnect($connectDB);

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}

function isValidID($requestBody, $connectDB)
{
    $itemsDB = $connectDB->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
    $itemsDB->bind_param("i", $requestBody['id']);
    $itemsDB->execute();
    $result = $itemsDB->get_result();

    return $result->num_rows > 0 ? true : false;
}
