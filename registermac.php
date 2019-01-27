<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require "main.php";

$email = $mac = $name = "";
$emailErr = $macErr = $nameErr = "";
$emailOK = $macOK = $nameOK = false;
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "E-Mail erforderlich";
    } else {
        $email = testInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Keine gültige E-Mail-Adresse";
        } else {
            if (\strpos($email, $ini["email_suffix"]) !== false) {
                $emailOK = true;
            } else {
                $emailErr = "Keine gültige E-Mail-Adresse";
            }
        }
    }

    if (empty($_POST["mac"])) {
        $macErr = "Geräte-Adresse erforderlich";
    } else {
        $mac = testInput($_POST["mac"]);
        if (!filter_var($mac, FILTER_VALIDATE_MAC)) {
            $macErr = "Keine gültige MAC-Adresse";
        } else {
            $macOK = true;
        }
    }

    if (empty($_POST["name"])) {
        $nameErr = "Gerätebeschreibung erforderlich";
    } else {
        $name = testInput($_POST["name"]);
        $nameOK = true;
    }

    if ($emailOK === true and $macOK === true and $nameOK === true) {
        $insertUser = $db_conn->prepare("INSERT IGNORE INTO users (email) VALUES (?)");
        $insertUser->bind_param("s", $email);
        $insertUser->execute();

        $selectUserId = $db_conn->prepare("SELECT id FROM users WHERE (email = ?)");
        $selectUserId->bind_param("s", $email);
        $selectUserId->execute();
        $selectUserId->bind_result($userId);
        $selectUserId->fetch();
        $selectUserId->close();

        $insertMac = $db_conn->prepare("INSERT INTO macs (userId, mac, deviceName, token) VALUES (?,?,?,?)");
        $token = bin2hex(random_bytes(20));
        $insertMac->bind_param("isss", $userId, $mac, $name, $token);
        $insertMac->execute();
        if (sendMail($email, $token) === true) {
            $successMsg = "Bitte bestätigen Sie die Registrierung mithilfe der E-Mail, die Ihnen soeben zugeschickt wurde.";
        }
    }
}

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sendMail($email, $token) {
    $ini = parse_ini_file("config.ini");
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

?>

<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>WLAN-Sicherheitsfilter</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body>
    <h1>WLAN-Sicherheitsfilter</h1>
    <h2>Neues Gerät registrieren</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label>
            E-Mail-Adresse:
            <input type="text" name="email" value="<?php echo $email?>">
            <span class="error"><?php echo $emailErr;?></span>
        </label><br>
        <label>
            Geräte-Adresse:
            <input type="text" name="mac" maxlength="17" value="<?php echo $mac?>">
            <span class="error"><?php echo $macErr;?></span>
        </label><br>
        <label>
            Geräte-Beschreibung:
            <input type="text" name="name" value="<?php echo $name?>">
            <span class="error"><?php echo $nameErr;?></span>
        </label><br>
        <input type="submit">
    </form>
    <p><?php echo $successMsg;?></p>

</body>

</html>
