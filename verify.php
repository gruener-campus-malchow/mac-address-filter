<?php

require "main.php";

$urlToken = $_GET["token"];

$verify = $db_conn->prepare("UPDATE macs SET verified=1 WHERE (token = ?)");
$verify->bind_param("s", $urlToken);
if ($verify->execute()) {
    echo "Geräte-Adresse erfolgreich verifiziert";
} else {
    echo "Etwas ist schiefgegangen";
}
