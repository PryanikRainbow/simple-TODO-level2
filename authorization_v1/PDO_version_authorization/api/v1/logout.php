<?php

session_start();
session_unset();

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

echo json_encode(['ok' => true]);
