<?php

session_start();

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Credentials: true');

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if (isset($requestBody['text']) && !empty($requestBody['text'])) {
    $itemsBD = $connectDB->prepare("INSERT INTO items (userID, text, checked) VALUES (?, ?, 0)");
    $itemsBD->execute([$_SESSION['userID'], $requestBody['text']]);
    echo json_encode(['id' => $connectDB->lastInsertId()]);
} else {
    echo json_encode(['error' => 'Bad Request']);
    http_response_code(400);
}

$connectDB = null;

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}
