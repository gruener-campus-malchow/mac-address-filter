<?php

require "main.php";

$sql_register = $db_conn->prepare("INSERT INTO users (name, email, pin) VALUES (?, ?, ?)");
$sql_register->bind_param("ssi", $name, $email, $pin);

$name = $_POST["name"];
$email = $_POST["email"];
$pin = mt_rand(1000,9999);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false and strpos($email, "@gruener-campus-malchow.de") != false) {
    $sql_register->execute();
    $email_betreff = "Ihr Zugang zum GCM-WLAN";
    $email_text = "Sehr geehrte/r " . $name . ". Das ist ihre PIN: " . $pin;
    mail($email, $email_betreff, $email_text, $email_from);
    echo json_encode(array("email_sent"=>true));
} else {
    echo json_encode(array("email_sent"=>false));
}

$sql_register->close();
$db_conn->close();
session_destroy();