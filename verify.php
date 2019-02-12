<?php

require "main.php";

$urlToken = $_GET["token"];

$verify = $db_conn->prepare("UPDATE ".$p."_macs SET verified=1 WHERE (token = ?)");
$verify->bind_param("s", $urlToken);
if ($verify->execute()) {
    sendMail($urlToken);
    echo "Geräte-Adresse erfolgreich verifiziert";
} else {
    echo "Etwas ist schiefgegangen";
}

function sendMail($token) {
    global $ini;
    global $db_conn;
    global $p;
    $selectData = $db_conn->prepare("SELECT ".$p."_users.email, ".$p."_users.maxMacs, ".$p."_macs.mac, ".$p."_macs.deviceName, ".$p."_users.id FROM ".$p."_users
        INNER JOIN ".$p."_macs ON ".$p."_macs.userId = ".$p."_users.id
        WHERE (".$p."_macs.token = ? AND ".$p."_macs.verified = 1)");
    $selectData->bind_param("s", $token);
    $selectData->execute();
    $selectData->bind_result($email, $maxMacs, $mac, $deviceName, $userId);
    $selectData->fetch();
    $selectData->close();

    $selectMacCount = $db_conn->prepare("SELECT COUNT(id) FROM ".$p."_macs WHERE (userId=? AND verified=1)");
    $selectMacCount->bind_param("i", $userId);
    $selectMacCount->execute();
    $selectMacCount->bind_result($registeredMacsCount);
    $selectMacCount->fetch();
    $selectMacCount->close();

    $selectMacArray = $db_conn->prepare("SELECT deviceName, mac, token FROM ".$p."_macs WHERE (userId = ? AND verified = 1) ORDER BY created_at ASC");
    $selectMacArray->bind_param("i", $userId);
    $selectMacArray->execute();
    $registeredMacs = $selectMacArray->get_result()->fetch_all(MYSQLI_ASSOC);

    $mailText = "Sehr geehrte/r Nutzer/in. \n\nSie wurden oder haben beim Sicherheitsfilter des Grünen Campus Malchow folgendes Gerät registriert: \n\n"
        .$deviceName." - ".$mac."\n\n"
        ."Dadurch sollten Sie sich in allen Gebäuden nicht nur mit dem WLAN verbinden können, sondern freundlicherweise von der Firewall nicht blockiert werden. Sie können von nun an u.a. Drucker nutzen und sogar Internetzugang haben.\n\n"
        ."Hier noch eine hübsche Auflistung der bereits registrierten Geräte mit Link zum Löschen:\n\n";

    foreach ($registeredMacs as $m) {
        $mailText .= $m["deviceName"] . " - " . $m["mac"] . " - " . $ini["domain"] . "/delete.php?token=" . $m["token"] . "\n\n";
    }

    $mailText .= "Die derzeitige Anzahl registrierter Geräte liegt bei ".$registeredMacsCount." von maximal ".$maxMacs.".\n\n"
        ."Es kann eine kleine Weile dauern, bis alle Gebäude des Campus Ihre Geräte in ihre lokale Liste übernommen haben. Wir bitten hierbei um Geduld.\n\n"
        ."Da in dieser Mail viele nützliche Links/URLs vorhanden sind, lohnt es sich, diese aufzuheben.\n\n\n"
        ."Mit freundlichen Grüßen,\n\nIhr CIS & FBI\n(CampusInformationSsystem & Fachbereich Informatik)";
    $mailBetreff = "WLAN-Sicherheitsfilter - Registrierung bestätigt";
    if (mail($email, $mailBetreff, $mailText, $ini["email_from"]) === true) {
        return true;
    }
}