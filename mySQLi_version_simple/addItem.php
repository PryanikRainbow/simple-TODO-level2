<?php

include('connect.php');

//$requestBody = json_decode('{"text":"Add1"}', true);
$requestBody = json_decode(file_get_contents('input.json'), true);
$connectDB = getDBConnect();

header('Content-Type: application/json');

if (isset($requestBody['text']) && !empty($requestBody['text'])) {

    $text = $connectDB->real_escape_string($requestBody['text']);

    $itemsBD = $connectDB->prepare("INSERT INTO items (text, checked) VALUES (?, false)");
    $itemsBD->bind_param("s", $text);
    $itemsBD->execute();

    echo json_encode(['id' => $connectDB->insert_id]);
} elseif(empty($requestBody['text'])) {
    echo json_encode(['ok' => 'No Content']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

// function getRequestBody()
// {
//     return json_decode(file_get_contents('php://input'), true);
// }

closeConnect($connectDB);
