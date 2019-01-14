<?php

require "main.php";

$sql_createUserTable = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(45) NOT NULL UNIQUE,
    admin BOOL NOT NULL DEFAULT FALSE
)";

if ($db_conn->query($sql_createUserTable) === TRUE) {
    echo "User-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der User-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
}

$sql_macAddressTable = "CREATE TABLE IF NOT EXISTS macs (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    mac VARCHAR(17) NOT NULL,
    deviceName VARCHAR(60),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    token VARCHAR(60),
    verified BOOL DEFAULT FALSE,
    FOREIGN KEY (userId) REFERENCES users(id)
)";

if ($db_conn->query($sql_macAddressTable) === TRUE) {
    echo "MAC-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der MAC-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
}

$db_conn->close();
session_destroy();