<?php

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header('Content-Type: application/json');
if (isset($requestBody['id']) && isValidID($requestBody, $connectDB)) {
    $idDeleted = $requestBody['id'];

    $itemsDB = $connectDB->prepare("DELETE FROM items WHERE id = ?");
    $itemsDB->bind_param("i", $idDeleted);
    $itemsDB->execute();

    $updatedIndexes = "UPDATE items SET id = id - 1 WHERE id > ?";
    $itemsDB = $connectDB->prepare($updatedIndexes);
    $itemsDB->bind_param("i", $idDeleted);
    $itemsDB->execute();

    $itemsDB = $connectDB->query("ALTER TABLE items AUTO_INCREMENT = 1");

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody, $connectDB)) {
    // http_response_code(204);
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
