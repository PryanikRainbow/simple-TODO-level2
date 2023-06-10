<?php

const FILE_PATH_COUNTER = 'TODO_counter.txt';
const FILE_PATH_ITEMS = 'items.json';

// get a TODO list
$items = json_decode(readFileContent(FILE_PATH_ITEMS), true);

$requestBody = json_decode(file_get_contents('php://input'), true);
$numItem = (int)readFileContent(FILE_PATH_COUNTER);

header("Access-Control-Allow-Origin: http://todo_simple_public.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if (!empty($requestBody['text'])) {
    $newItem = ['id' => ++$numItem, 'text' => $requestBody['text'], 'checked' => false];

    $items['items'][] = $newItem;
    writeToFile(FILE_PATH_ITEMS, json_encode($items, JSON_PRETTY_PRINT));
    writeToFile(FILE_PATH_COUNTER, $numItem);

    echo json_encode(['id' => $numItem]);
} elseif(empty($requestBody['text'])) {
    echo json_encode(['ok' => 'No Content']);
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
    fwrite($file, $content);
    fclose($file);
}
