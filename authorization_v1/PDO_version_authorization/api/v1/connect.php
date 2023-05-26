<?php

const SERVER_NAME = '127.0.0.1';
const USER_NAME = 'root';
const PASSWORD = '';

const DB_NAME =  'TODO_v1';
const ITEMS_DB = 'items';
const USERS_DB = 'users';

function getDBConnect()
{
    try {
        $connect = new PDO("mysql:host=" . SERVER_NAME . ";charset=utf8mb4", USER_NAME, PASSWORD);
        $connect->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $connect->exec("use " . DB_NAME);

        createUsersDB($connect);
        createItemsDB($connect);

        return $connect;
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        http_response_code(500);
    }
}

function createUsersDB($connect)
{
    $usersDB = "CREATE TABLE IF NOT EXISTS " . USERS_DB . " (
        `userID` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `login` VARCHAR(30)  NOT NULL,
        `password` VARCHAR(80) NOT NULL
    )";
    $connect->exec($usersDB);
}

function createItemsDB($connect)
{
    $itemsDB = "CREATE TABLE IF NOT EXISTS " . ITEMS_DB . " (
        `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `text` TEXT NOT NULL,
        `checked` BOOLEAN DEFAULT FALSE,
        `userID` INT(4) UNSIGNED NOT NULL,
        FOREIGN KEY (`userID`) REFERENCES " . USERS_DB . "(`userID`)
    )";
    $connect->exec($itemsDB);
}
