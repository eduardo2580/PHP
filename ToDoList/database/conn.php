<?php

$hostname = '';
$database = '';
$username = '';
$password = '';

try {
    $pdo = new PDO("pgsql:host=localhost;dbname=to_do", $username, $password);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
