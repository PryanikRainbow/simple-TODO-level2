<?php

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
&& !empty($requestBody['login']) && !empty($requestBody['pass'])
&& isAvailableLogin($connectDB, $requestBody['login'])) {
    try {
        $hashingPass = password_hash($requestBody['pass'], PASSWORD_DEFAULT);

        $usersBD = $connectDB->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
        $usersBD->execute([$requestBody['login'], $hashingPass]);
        echo json_encode(['ok' => true]);
        http_response_code(200);
    } catch (PDOException $e) {
        echo $e->getMessage();
        http_response_code(500);
    }

} elseif(empty($requestBody['login']) || empty($requestBody['pass'])) {
    echo json_encode(['error' => 'Bad Request']);
    http_response_code(400);

} elseif(!isAvailableLogin($connectDB, $requestBody['login'])) {
    echo json_encode(['error' => 'Login is already taken']);
    http_response_code(409);
} else {
    echo json_encode(['error' => 'Bad Request']);
    http_response_code(400);
}

$connectDB = null;

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}

function isAvailableLogin($connectDB, $login)
{
    $usersDB = $connectDB->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
    $usersDB->execute([$login]);
    $count = $usersDB->rowCount();

    return ($count === 0);
}
