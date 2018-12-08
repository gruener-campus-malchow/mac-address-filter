<?php

require "main.php";

$sql_login = $db_conn->prepare("SELECT pin, id FROM users WHERE (email = ?)");
$sql_login->bind_param("s", $email);

$email = $_POST["email"];
$sql_login->execute();
$sql_login->bind_result($pin,$userId);
$sql_login->fetch();

if ($_POST["pin"] == $pin) {
    echo json_encode(array("logged_in"=>true));
    $_SESSION["userId"] = $userId;
} else {
    echo json_encode(array("logged_in"=>false));
}

$sql_login->close();
$db_conn->close();