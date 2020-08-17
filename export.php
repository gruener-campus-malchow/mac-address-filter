<?php

require "main.php";

//$sql_exportMacs = $db_conn->prepare("SELECT id, mac, deviceName,email FROM " . $p . "_macs AS mac, " . $p . "_users AS user WHERE (verified=1) AND (mac.userId = user.id)");
$sql_exportMacs = $db_conn->prepare("SELECT mac.id, mac, deviceName,email FROM gcmmac_macs AS mac, gcmmac_users AS user
WHERE (verified=1) AND mac.userId = user.id");
$sql_exportMacs->execute();

$macs = $sql_exportMacs->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_GET["key"] === $ini["export_password"]) {
    foreach ($macs as $mac) {
        $mac["mac"] = str_replace('-',':',$mac["mac"]);
        $mac["mac"] = strtolower ( $mac["mac"] );
        echo json_encode($mac["id"]) . ",NONE," . $mac["mac"] . ",on," . $mac["email"].' uses '.$mac["deviceName"]. "\n";
    }
} else {
    echo "Falscher Key...";
}