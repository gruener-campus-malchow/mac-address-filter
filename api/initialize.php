<?php

require "main.php";

$sql_createUserTable = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL,
    email VARCHAR(45) NOT NULL,
    pin INT(4) NOT NULL
)";

if ($db_conn->query($sql_createUserTable) === TRUE) {
    echo "User-Tabelle erfolgreich erstellt";
} else {
    echo "Erstellen der Tabelle fehlgeschlagen: " . $db_conn->error;
}

$db_conn->close();

?>