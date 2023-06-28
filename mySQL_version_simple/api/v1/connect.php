<?php

const SERVER_NAME = '127.0.0.1';
const USER_NAME = 'root';
const PASSWORD = '';

const DB_NAME =  'TODO_list_simple';
const ITEMS_DB = 'items';

//переробити на tryCatch
function getDBConnect()
{
    try {
        // подключаемся к серверу
        $connect = new PDO("mysql:host=" . SERVER_NAME . ";charset=utf8mb4", USER_NAME, PASSWORD);

        $TODOListDB = "CREATE DATABASE IF NOT EXISTS "
        . DB_NAME . " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $connect->exec("use " . DB_NAME);

        $itemsDB = "CREATE TABLE IF NOT EXISTS " . ITEMS_DB . " (
        `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(60) NULL DEFAULT '',
        `text` TEXT NOT NULL,
        `checked` BOOLEAN DEFAULT FALSE
    )";
        $connect->exec($itemsDB);
        return $connect;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
