<?php
$pdo = new PDO("mysql:host=localhost;dbname=hotel", "root", "", [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>
