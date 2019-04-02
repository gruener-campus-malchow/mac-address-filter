<?php

require "main.php";

$sql_createUserTable = "CREATE TABLE IF NOT EXISTS ".$p."_users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(45) NOT NULL UNIQUE,
    admin BOOL NOT NULL DEFAULT FALSE,
    maxMacs INT NOT NULL DEFAULT 3
)";

if ($db_conn->query($sql_createUserTable) === TRUE) {
    echo "User-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der User-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
    logger("db", $db_conn->error);
}

$sql_macAddressTable = "CREATE TABLE IF NOT EXISTS ".$p."_macs (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    mac VARCHAR(17) NOT NULL,
    deviceName VARCHAR(60),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    token VARCHAR(60),
    verified BOOL DEFAULT FALSE,
    FOREIGN KEY (userId) REFERENCES ".$p."_users(id)
)";

if ($db_conn->query($sql_macAddressTable) === TRUE) {
    echo "MAC-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der MAC-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
    logger("db", $db_conn->error);
}

$sql_logTable = "CREATE TABLE IF NOT EXISTS ".$p."_logs (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    category VARCHAR(20),
    text TEXT
)";

if ($db_conn->query($sql_logTable) === TRUE) {
    echo "Log-Tabelle erfolgreich erstellt<br>";
} else {
    echo "Erstellen der Log-Tabelle fehlgeschlagen: " . $db_conn->error . "<br>";
    logger("db", $db_conn->error);
}

$db_conn->close();