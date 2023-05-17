<?php

const FILE_PATH_COUNTER = 'TODO_counter.txt';
const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(file_get_contents(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);

$numItem = (int)file_get_contents(FILE_PATH_COUNTER);

header('Content-Type: application/json');

if (!empty($requestBody['text'])) {

    $items['items'][] = ['id' => ++$numItem, 'text' => $requestBody['text'], 'checked' => false];

    file_put_contents(FILE_PATH_ITEMS, json_encode($items, JSON_PRETTY_PRINT));
    file_put_contents(FILE_PATH_COUNTER, $numItem);

    echo json_encode(['id' => $numItem]);
} elseif(empty($requestBody['text'])) {
    // http_response_code(204);
    echo json_encode(['ok' => 'No Content']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}
