<?php

require "main.php";

$sql_insertMac = $db_conn->prepare("INSERT INTO macs (userId, mac, deviceName) VALUES (?,?,?)");
$sql_insertMac->bind_param("iss",$userId,$mac, $deviceName);

$mac = $_POST["mac"];
$userId = $_SESSION["userId"];
$deviceName = $_POST["deviceName"];

if (!filter_var($mac, FILTER_VALIDATE_MAC) === false) {
    $sql_insertMac->execute();
    echo(json_encode(array("success"=>true)));
} else {
    echo(json_encode(array("success"=>false)));
}

$sql_insertMac->close();
$db_conn->close();