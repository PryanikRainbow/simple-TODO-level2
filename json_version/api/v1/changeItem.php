<?php

const FILE_PATH_COUNTER = 'TODO_counter.txt';
const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(readFileContent(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);
$requestBody['text'] =  trim($requestBody['text']);

header("Access-Control-Allow-Origin: http://todo_simple_public.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: PUT");
header('Access-Control-Allow-Credentials: true');

header('Content-Type: application/json');

if (isset($requestBody['id'], $requestBody['text'], $requestBody['checked'])
&& !empty($requestBody['text']) && isValidID($requestBody['id'], $items)) {
    $id = $requestBody['id'];

    $itemIndex = array_search($id, array_column($items['items'], 'id'));
    if ($itemIndex !== false) {
        $items['items'][$itemIndex]['text'] = $requestBody['text'];
        $items['items'][$itemIndex]['checked'] = (bool) $requestBody['checked'];

        writeToFile(FILE_PATH_ITEMS, json_encode($items, JSON_PRETTY_PRINT));

        echo json_encode(['ok' => true]);
    }
} elseif (!isValidID($requestBody['id'], $items)) {
    echo json_encode(['error' => 'not found']);
} else {
    echo json_encode(['error' => 'Bad Request']);
}

function readFileContent($filePath)
{
    $outputString = '';

    $file = fopen($filePath, 'r');
    if (flock($file, LOCK_SH)) {
        while (!feof($file)) {
            $outputString .= fgets($file);
        }
        flock($file, LOCK_UN);
    }
    fclose($file);

    return $outputString;
}

function writeToFile($filePath, $content)
{
    $file = fopen($filePath, 'w');
    if (flock($file, LOCK_EX)) {
        fwrite($file, $content);
        flock($file, LOCK_UN);
    }
    fclose($file);
}

function isValidID($id, $items)
{
    $listID = array_column($items['items'], 'id');
    return in_array((int)$id, $listID, true);
}
