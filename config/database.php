<?php
$host = 'sql306.infinityfree.com';
$dbname = 'if0_37507192_513_3';
$username = 'if0_37507192';
$password = '9LUIHuJR32SnGUi';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>