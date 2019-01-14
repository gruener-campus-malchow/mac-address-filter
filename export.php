<?php

require "main.php";

$sql_exportMacs = $db_conn->prepare("SELECT id, mac, deviceName FROM macs");
$sql_exportMacs->execute();

$macs = $sql_exportMacs->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_GET["key"] === $ini["export_password"]) {
    foreach ($macs as $mac) {
        echo json_encode($mac["id"]) . ",NONE," . $mac["mac"] . ",on," . $mac["deviceName"] . "\n";
    }
} else {
    echo "Falscher Key...";
}