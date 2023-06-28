<?php

const SERVER_NAME = '127.0.0.1';
const USER_NAME = 'anya';
const PASSWORD = '12345678';
const DB =  'TODO_simple_mysqli';

function getDBConnect()
{
    try {
        return new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB);
    } catch (mysqli_sql_exception $e) {
        echo  $e->getMessage();
    }
}

function closeConnect($connect)
{
    $connect -> close();
}
