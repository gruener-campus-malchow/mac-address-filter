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

function sendMail($email, $token) {
    global $ini;
    $mailText = "Sehr geehrte/r Nutzer/in. \n\nJemand hat gerade mit ihrer E-Mail-Adresse ein Gerät im WLAN-Sicherheitsfilter des GCM registriert."
        ."Falls Sie das waren, klicken Sie bitte auf folgenden Link, um die Registrierung abzuschließen:\n\n"
        .$ini["domain"]
        ."/verify.php?token="
        .$token.
        "\n\nFalls Sie Sich daran nicht erinnern können, ignorieren Sie diese E-Mail einfach. \n\n"
        ."Mit freundlichen Grüßen, \n\nIhr CIS & FBI \n(CampusInformationSsystem & Fachbereich Informatik)";
    $mailBetreff = "WLAN-Sicherheitsfilter - Registrierung bestätigen";
    if (mail($email, $mailBetreff, $mailText, $ini["email_from"]) === true) {
        return true;
    }
}