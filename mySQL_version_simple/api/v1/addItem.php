<?php

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');

if (isset($requestBody['text']) && !empty($requestBody['text'])) {
    $itemsBD = $connectDB->prepare("INSERT INTO items (title, text, checked) VALUES (?, ?, false)");
    $itemsBD->execute([$requestBody['title'], $requestBody['text']]);
    echo json_encode(['id' => $connectDB->lastInsertId()]);
} elseif(empty($requestBody['text'])) {
    echo json_encode(['ok' => 'No Content']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

$connectDB = null;

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}
