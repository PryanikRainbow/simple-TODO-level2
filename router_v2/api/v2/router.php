<?php

session_start();

header("Access-Control-Allow-Origin: http://todo-public-v2.local");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, PUT, GET, DELETE, OPTIONS");
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'connect.php';
$connectDB = getDBConnect();

$action = $_GET['action'];
$requestBody = getRequestBody();

switch ($action) {
    case 'register':
        register($connectDB, $requestBody);
        break;
    case 'login':
        login($requestBody, $connectDB);
        break;
    case 'logout':
        logout();
        break;
    case 'getItems':
        getItems($connectDB);
        break;
    case 'addItem':
        addItem($requestBody, $connectDB);
        break;
    case 'deleteItem':
        deleteItem($requestBody, $connectDB);
        break;
    case 'changeItem':
        changeItem($requestBody, $connectDB);
        break;
}
$connectDB = null;

function login($requestBody, $connectDB)
{
    if (isset($requestBody['login']) && isset($requestBody['pass'])
    && !empty($requestBody['login']) && !empty($requestBody['pass'])) {

        tryToLogIn($requestBody['login'], $requestBody['pass'], $connectDB);
    } else {
        echo json_encode(['error' => 'Invalid data']);
        http_response_code(400);
    }
}

function tryToLogIn($login, $pass, $connectDB)
{
    $query = $connectDB->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
    $query->execute([$login]);
    $userData = $query->fetch();

    if (!$userData) {
        echo json_encode(['error' => 'User not found']);
        http_response_code(404);
        $connectDB = null;
        die();
    }

    if (password_verify($pass, $userData['password'])) {
        $_SESSION['login'] = $login;
        $_SESSION['userID'] = $userData['userID'];

        // session_set_cookie_params([
        //     'samesite' => 'None',
        //     'secure' => true,
        // ]);
        echo json_encode(['ok' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid password']);
    }
}

function register($connectDB, $requestBody)
{
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

    } elseif(!isAvailableLogin($connectDB, $requestBody['login'])) {
        echo json_encode(['error' => 'Login is already taken']);
        http_response_code(409);
    } else {
        echo json_encode(['error' => 'Bad Request']);
        http_response_code(400);
    }
}

function isAvailableLogin($connectDB, $login)
{
    $usersDB = $connectDB->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
    $usersDB->execute([$login]);
    $count = $usersDB->rowCount();

    return ($count === 0);
}

function logout()
{
    session_unset();
    echo json_encode(['ok' => true]);
}

function getItems($connectDB)
{
    $itemsDB = $connectDB->prepare("SELECT * FROM items WHERE userID = :userID");
    $itemsDB->execute(['userID' => $_SESSION['userID']]);
    $result = $itemsDB->fetchAll(PDO::FETCH_ASSOC);
    $itemsDBArray = ['items' => $result];

    header('Content-Type: application/json');
    echo json_encode($itemsDBArray);
}

function addItem($requestBody, $connectDB)
{
    if (isset($requestBody['text']) && !empty($requestBody['text'])) {
        $itemsBD = $connectDB->prepare("INSERT INTO items (userID, text, checked) VALUES (?, ?, 0)");
        $itemsBD->execute([$_SESSION['userID'], $requestBody['text']]);
        echo json_encode(['id' => $connectDB->lastInsertId()]);
    } else {
        echo json_encode(['error' => 'Bad Request']);
        http_response_code(400);
    }
}

function changeItem($requestBody, $connectDB)
{
    if (isset($requestBody['id']) && isset($requestBody['checked']) && isValidID($requestBody['id'], $connectDB)) {
        $id = $requestBody['id'];
        $text = $requestBody['text'];
        $checked = $requestBody['checked'];

        $itemsDB = $connectDB->prepare("UPDATE items SET text = ?, checked = ? WHERE id = ?");
        $itemsDB->execute([$text, $checked, $id]);

        echo json_encode(['ok' => true]);
    } elseif (!isValidID($requestBody, $connectDB)) {
        echo json_encode(['error' => 'Not Found']);
        http_response_code(404);
    } else {
        echo json_encode(['error' => 'Bad Request']);
        http_response_code(400);
    }
}

function deleteItem($requestBody, $connectDB)
{
    if (isset($requestBody['id']) && isValidID($requestBody['id'], $connectDB)) {
        $idDeleted = $requestBody['id'];

        $itemsDB = $connectDB->prepare("DELETE FROM items WHERE id = ?");
        $itemsDB->execute([$idDeleted]);
        echo json_encode(['ok' => true]);
    } elseif (!isValidID($requestBody['id'], $connectDB)) {
        echo json_encode(['error' => 'Not Found']);
        http_response_code(404);
    } else {
        echo json_encode(['error' => 'Bad Request']);
        http_response_code(400);
    }
}

function isValidID($id, $connectDB)
{
    $itemsDB = $connectDB->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
    $itemsDB->execute([$id]);
    $count = $itemsDB->rowCount();
    return ($count > 0);
}

function getRequestBody()
{
    return json_decode(file_get_contents('php://input'), true);
}
