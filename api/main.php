<?php

$_SERVER[""];

// Datenbank-Konfiguration
$db_host = "";
$db_user = "";
$db_password = "";
$db_name = "gcmmac";

// Email-Konfiguration
$email_from = "From: GCM-Mac <example@example.com>";

$export_password = "supersecret";

session_start();

$db_conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($db_conn->connect_error) {
    die($db_conn->connect_error);
};