<?php

require("main.php");

$sql_register = $db_conn->prepare("INSERT INTO users (name, email, pin) VALUES (?, ?, ?)");
$sql_register->bind_param("ssi", $name, $email, $pin);

$name = $_POST["name"];
$email = $_POST["email"];
$pin = mt_rand(1000,9999);

$sql_register->execute();

$email_betreff = "Ihr Zugang zum GCM-WLAN";
$email_text = "Sehr geehrte/r " . $name . ". Das ist ihre PIN: " . $pin;

mail($email, $email_betreff, $email_text, $email_from);

$sql_register->close();
$db_conn->close();

?>