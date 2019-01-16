<?php

require "main.php";

$urlToken = $_GET["token"];

$deleteMac = $db_conn->prepare("DELETE FROM macs WHERE (token = ?)");
$deleteMac->bind_param("s", $urlToken);
if ($deleteMac->execute()) {
    echo "Geräte-Adresse erfolgreich registriert.";
} else {
    echo "Etwas ist schiefgegangen";
}