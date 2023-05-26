<?php

session_start();

include('connect.php');

$requestBody = getRequestBody();
$connectDB = getDBConnect();

header("Access-Control-Allow-Origin: http://todo_public_v1.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if (isset($requestBody['login']) && isset($requestBody['pass'])
    && !empty($requestBody['login']) && !empty($requestBody['pass'])) {

    tryToLogIn($connectDB, $requestBody['login'], $requestBody['pass']);
} else {
    echo json_encode(['error' => 'Invalid data']);
    http_response_code(400);
}

$connectDB = null;

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}

function tryToLogIn($connectDB, $login, $pass)
{
    $query = $connectDB->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
    $query->execute([$login]);
    $userData = $query->fetch();

    if (!$userData) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        $connectDB = null;
        die();
    }

    if (password_verify($pass, $userData['password'])) {
        $_SESSION['login'] = $login;
        $_SESSION['userID'] = $userData['userID'];
        echo json_encode(['ok' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid password']);
        $connectDB = null;
        die();
    }
}
