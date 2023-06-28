<?php

const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(file_get_contents(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);

header("Access-Control-Allow-Origin: http://todo-public-json.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: DELETE");
header('Access-Control-Allow-Credentials: true');

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (isset($requestBody['id']) && isValidID($requestBody['id'], $items)) {
    $id = $requestBody['id'];

    $itemIndex = array_search($id, array_column($items['items'], 'id'));

    array_splice($items['items'], $itemIndex, 1);

    file_put_contents(FILE_PATH_ITEMS, json_encode($items));

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody['id'], $items)) {
    echo json_encode(['error' => 'Not Found']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

function isValidID($id, $items)
{
    $listID = array_column($items['items'], 'id');
    return in_array((int)$id, $listID, true);
}
