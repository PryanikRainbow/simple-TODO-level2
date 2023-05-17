<?php

const FILE_PATH_COUNTER = 'TODO_counter.txt';
const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(file_get_contents(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);
$requestBody['text'] =  trim($requestBody['text']);

header('Content-Type: application/json');

if (isset($requestBody['id'], $requestBody['text'], $requestBody['checked'])
 && !empty($requestBody['text']) && isValidID($requestBody)) {
    $id = $requestBody['id'];

    $itemIndex = array_search($id, array_column($items['items'], 'id'));
    if ($itemIndex !== false) {
        $items['items'][$itemIndex]['text'] = $requestBody['text'];
        $items['items'][$itemIndex]['checked'] = (bool) $requestBody['checked'];

        file_put_contents(FILE_PATH_ITEMS, json_encode($items));

        echo json_encode(['ok' => true]);
    }

} elseif(!isValidID($requestBody)) {
    echo json_encode(['error' => 'not found']);
} else {
    //не працює
    echo json_encode(['error' => 'Bad Request']);
}

function isValidID($requestBody)
{
    return (int)$requestBody['id'] <= (int)file_get_contents(FILE_PATH_COUNTER) && (int)$requestBody['id'] > 0;
}
