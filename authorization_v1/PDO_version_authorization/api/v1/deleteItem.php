<?php

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header('Access-Control-Allow-Credentials: true');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody['id'], $connectDB)) {
    $idDeleted = $requestBody['id'];

    $itemsDB = $connectDB->prepare("DELETE FROM items WHERE id = ?");
    $itemsDB->execute([$idDeleted]);
    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody['id'], $connectDB)) {
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

function isValidID($id, $connectDB)
{
    $itemsDB = $connectDB->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
    $itemsDB->execute([$id]);
    $count = $itemsDB->fetchColumn();
    return ($count > 0);
}
