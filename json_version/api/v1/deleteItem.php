<?php

const FILE_PATH_COUNTER = 'TODO_counter.txt';
const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(file_get_contents(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json');

if (isset($requestBody['id']) && isValidID($requestBody)) {
    $id = $requestBody['id'];

    $itemIndex = array_search($id, array_column($items['items'], 'id'));

    array_splice($items['items'], $itemIndex, 1);
    for ($i = $itemIndex; $i < count($items['items']); $i++) {
        $items['items'][$i]['id'] = $items['items'][$i]['id'] - 1;
    }

    $numItem = (int)file_get_contents(FILE_PATH_COUNTER);
    file_put_contents(FILE_PATH_COUNTER, --$numItem);
    file_put_contents(FILE_PATH_ITEMS, json_encode($items));

    echo json_encode(['ok' => true]);
} elseif (!isValidID($requestBody)) {
    echo json_encode(['error' => 'Not Found']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

function isValidID($requestBody)
{
    return (int)$requestBody['id'] <= (int)file_get_contents(FILE_PATH_COUNTER) && (int)$requestBody['id'] >= 0;
}

// Очищення JSON-файлу
// file_put_contents(FILE_PATH_ITEMS, json_encode(array('items' => [])));

// // Скидання лічильника
// file_put_contents(FILE_PATH_COUNTER, '0');

// header('Content-Type: application/json');
// echo json_encode(array('ok' => true));
