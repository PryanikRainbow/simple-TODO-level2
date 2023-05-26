<?php

include('connect.php');

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: OPTIONS, PUT, POST");
header('Access-Control-Allow-Credentials: true');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $id = $requestBody['id'];
    $text = $requestBody['text'];
    $checked = $requestBody['checked'];

    $itemsDB = $connectDB->prepare("UPDATE items SET text = ?, checked = ? WHERE id = ?");
    $itemsDB->execute([$text, $checked, $id]);

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody, $connectDB)) {
    echo json_encode(['error' => 'Not Found']);
    http_response_code(404);
} else {
    echo json_encode(['error' => 'Bad Request']);
    http_response_code(400);
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
