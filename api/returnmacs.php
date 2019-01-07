<?php

require "main.php";

if ($_SESSION["admin"] = true) {
    $sql_returnMacs = $db_conn->prepare("SELECT macs.id, macs.mac, macs.deviceName, users.name, users.email
    FROM macs
    INNER JOIN users ON macs.userId = users.id
");
} else {
    $sql_returnMacs = $db_conn->prepare("SELECT mac, deviceName FROM macs WHERE (userid = ?)");
    $sql_returnMacs->bind_param("i", $userId);
    $userId = $_SESSION["userId"];
}

$sql_returnMacs->execute();

$macs = $sql_returnMacs->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($macs);