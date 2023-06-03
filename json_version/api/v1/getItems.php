<?php

const FILE_PATH_ITEMS = 'items.json';

// header("Access-Control-Allow-Origin: http://todo_simple_public.local");
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("Access-Control-Allow-Methods: GET");
// header('Access-Control-Allow-Credentials: true');

// header('Content-Type: application/json');
readfile(FILE_PATH_ITEMS);

//v3
// get a TODO list
// $items = json_decode(file_get_contents(FILE_PATH_ITEMS), true);

// header('Content-Type: application/json');
// $response = array("items" => $items);
// echo json_encode($items, true);

//v2
// $response = file_get_contents(FILE_PATH_ITEMS);
// echo ($response);
