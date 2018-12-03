<?php

// Datenbank-Konfiguration
$db_host = "";
$db_user = "";
$db_password = "";
$db_name = "";

// Email-Konfiguration
$email_from = "From: GCM-Mac <Email hier einsetzen>";

$db_conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($db_conn->connect_error) {
    die($db_conn->connect_error);
}

?>