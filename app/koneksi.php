<?php

try {
    $host = "localhost";
    $dbname = "inventori_db";
    $username = "root";
    $password = "";
    
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>