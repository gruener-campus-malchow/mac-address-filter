<?php

require "main.php";

$sql_createUserTable = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL,
    email VARCHAR(45) NOT NULL,
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
    delete_key VARCHAR(50),
    FOREIGN KEY (userId) REFERENCES users(id)
)";

if ($db_conn->query($sql_macAddressTable) === TRUE) {
    echo "MAC-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der MAC-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
}

$db_conn->close();
session_destroy();