<?php
$servername = "localhost";
$username = "group9";
$password = "o1eMiss2024";
$db = "group9";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PPO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>