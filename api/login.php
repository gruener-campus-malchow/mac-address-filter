<?php

require "main.php";

$sql_login = $db_conn->prepare("SELECT pin, id, admin FROM users WHERE (email = ?)");
$sql_login->bind_param("s", $email);

$email = $_POST["email"];
$sql_login->execute();
$sql_login->bind_result($pin,$userId, $admin);
$sql_login->fetch();

$_SESSION["admin"] = false;

if ($_POST["pin"] == $pin) {
    $_SESSION["userId"] = $userId;
    if ($admin === 1) {
        $_SESSION["admin"] = true;
    }
    echo json_encode(array("logged_in"=>true));
} else {
    echo json_encode(array("logged_in"=>false));
}

$sql_login->close();
$db_conn->close();