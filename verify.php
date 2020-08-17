<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);
?>

<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>WLAN-Sicherheitsfilter</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://cis.gruener-campus-malchow.de/screen_CIS.css">
</head>

<body>
        <div class="headder">
            <div class="image">
                <a href="https://cis.gruener-campus-malchow.de" class="invisible_link">
                    <img class="logo" src="https://cis.gruener-campus-malchow.de/logo_gcm_progressbar.gif" alt="logo_gcm">
                </a>
            </div>
            <div class="headding">
                Campus Informations System
            </div>
		    <h2>WLAN-Sicherheitsfilter</h2>
        </div>
    <div class ="outer_wrapper">
        <div class="textfield">
            <p>
            Dieser Service wurde an unserer Schule von <a class="std" href="https://github.com/sn0wmanmj" target="_blank">Moritz Jannasch</a> entwickelt und von Alexander Baldauf auf Sicherheitsaspekte hin untersucht.  
            </p>
            <p>
    Es können nur Kolleginnen und Kollegen ihre Geräte registrieren, die eine Dienstmailadresse haben. Über diesen Mail-Account laufen unsere Sicherheitsbestätigungen, damit nur Mitarbeiterinnen und Mitarbeiter des Campus ihre Geräte freischalten können.
            </p>
        </div>
       
        <div class="textfield">

                <h3><p>Feedback</p></h3>





<?php

require "main.php";

$urlToken = $_GET["token"];

$verify = $db_conn->prepare("UPDATE " . $p . "_macs SET verified=1 WHERE (token = ?)");
$verify->bind_param("s", $urlToken);
if ($verify->execute()) {
    sendMail($urlToken);
    echo "<p>Die Geräte-Adresse wurde erfolgreich verifiziert. Sie können nun das Netzwerk benutzen.</p>";
} else {
    echo "<p>Etwas ist schiefgegangen. Wiederholen Sie den Vorgang und lesen Sie bitte die Hilfe. Falls der Fehler weiterhin auftritt, kontaktieren Sie bitte den Administrator.</p>";
}

if($ini["message"] != "NONE")
{
    echo ' <div class="warning">'.$ini["message"].'</div>';
}


function sendMail($token)
{
    global $ini;
    global $db_conn;
    global $p;
    if ($ini["email_mode"] === "smtp") {
        global $sendSMTPMail;
    } elseif ($ini["email_mode"] === "sendmail") {
        global $emailFrom;
    }
    $selectData = $db_conn->prepare("SELECT " . $p . "_users.email, " . $p . "_users.maxMacs, " . $p . "_macs.mac, " . $p . "_macs.deviceName, " . $p . "_users.id FROM " . $p . "_users
        INNER JOIN " . $p . "_macs ON " . $p . "_macs.userId = " . $p . "_users.id
        WHERE (" . $p . "_macs.token = ? AND " . $p . "_macs.verified = 1)");
    $selectData->bind_param("s", $token);
    $selectData->execute();
    $selectData->bind_result($email, $maxMacs, $mac, $deviceName, $userId);
    $selectData->fetch();
    $selectData->close();

    $selectMacCount = $db_conn->prepare("SELECT COUNT(id) FROM " . $p . "_macs WHERE (userId=? AND verified=1)");
    $selectMacCount->bind_param("i", $userId);
    $selectMacCount->execute();
    $selectMacCount->bind_result($registeredMacsCount);
    $selectMacCount->fetch();
    $selectMacCount->close();

    $selectMacArray = $db_conn->prepare("SELECT deviceName, mac, token FROM " . $p . "_macs WHERE (userId = ? AND verified = 1) ORDER BY created_at ASC");
    $selectMacArray->bind_param("i", $userId);
    $selectMacArray->execute();
    $registeredMacs = $selectMacArray->get_result()->fetch_all(MYSQLI_ASSOC);

    $mailText = "Sehr geehrte/r Nutzer/in. \n\nSie wurden oder haben beim Sicherheitsfilter des Grünen Campus Malchow folgendes Gerät registriert: \n\n"
        . $deviceName . " - " . $mac . "\n\n"
        . "Dadurch sollten Sie sich in allen Gebäuden nicht nur mit dem WLAN verbinden können, sondern freundlicherweise von der Firewall nicht blockiert werden. Sie können von nun an u.a. Drucker nutzen und sogar Internetzugang haben.\n\n"
        . "Hier noch eine hübsche Auflistung der bereits registrierten Geräte mit Link zum Löschen:\n\n";

    foreach ($registeredMacs as $m) {
        $mailText .= $m["deviceName"] . " - " . $m["mac"] . " - " . $ini["domain"] . "/delete.php?token=" . $m["token"] . "\n\n";
    }

    $mailText .= "Die derzeitige Anzahl registrierter Geräte liegt bei " . $registeredMacsCount . " von maximal " . $maxMacs . ".\n\n"
        . "Es kann eine kleine Weile dauern, bis alle Gebäude des Campus Ihre Geräte in ihre lokale Liste übernommen haben. Wir bitten hierbei um Geduld.\n\n"
        . "Da in dieser Mail viele nützliche Links/URLs vorhanden sind, lohnt es sich, diese aufzuheben.\n\n\n"
        . "Mit freundlichen Grüßen,\n\nIhr CIS & FBI\n(CampusInformationSsystem & Fachbereich Informatik)";
    $mailBetreff = "WLAN-Sicherheitsfilter - Registrierung bestätigt";
    if ($ini["email_mode"] === "smtp") {
        $sendSMTPMail->Subject = $mailBetreff;
        $sendSMTPMail->addAddress($email);
        $sendSMTPMail->Body = $mailText;
        try {
            $sendSMTPMail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            logger("mail", $e);
        }
    } elseif ($ini["email_mode"] === "sendmail") {
        try {
            mail($email, $mailBetreff, $mailText, $emailFrom);
            return true;
        } catch (Exception $e) {
            logger("mail", "Fehler beim Senden einer Bestätigungsmail an " . $email . ": " . $e);
            return false;
        }
    }
}

?>

        </div>
    </div>


</body>

</html>

