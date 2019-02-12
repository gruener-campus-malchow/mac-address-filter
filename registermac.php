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
        $insertUser = $db_conn->prepare("INSERT IGNORE INTO ".$p."_users (email) VALUES (?)");
        $insertUser->bind_param("s", $email);
        $insertUser->execute();

        $selectUserId = $db_conn->prepare("SELECT id FROM ".$p."_users WHERE (email = ?)");
        $selectUserId->bind_param("s", $email);
        $selectUserId->execute();
        $selectUserId->bind_result($userId);
        $selectUserId->fetch();
        $selectUserId->close();

        $insertMac = $db_conn->prepare("INSERT INTO ".$p."_macs (userId, mac, deviceName, token) VALUES (?,?,?,?)");
        $token = bin2hex(random_bytes(20));
        $insertMac->bind_param("isss", $userId, $mac, $name, $token);
        $insertMac->execute();
        if (checkForMaxMacs($email) === true) {
            if (sendMail($email, $token) === true) {
                $successMsg = "Bitte bestätigen Sie die Registrierung mithilfe der E-Mail, die Ihnen soeben zugeschickt wurde.";
            }
        } else {
            $successMsg = "Sie haben bereits die maximale Anzahl an Geräten registriert. Falls Sie ein höheres Limit benötigen, wenden Sie Sich bitte an den Fachbereich Informatik.";
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
    global $ini;
    global $sendSMTPMail;
    $mailText = "Sehr geehrte/r Nutzer/in. \n\nJemand hat gerade mit ihrer E-Mail-Adresse ein Gerät im WLAN-Sicherheitsfilter des GCM registriert. "
        ."Falls Sie das waren, klicken Sie bitte auf folgenden Link, um die Registrierung abzuschließen:\n\n"
        .$ini["domain"]
        ."/verify.php?token="
        .$token.
        "\n\nFalls Sie Sich daran nicht erinnern können, ignorieren Sie diese E-Mail einfach. \n\n"
        ."Mit freundlichen Grüßen, \n\nIhr CIS & FBI \n(CampusInformationSsystem & Fachbereich Informatik)";
    $mailBetreff = "WLAN-Sicherheitsfilter - Registrierung bestätigen";

    $sendSMTPMail->Subject = $mailBetreff;
    $sendSMTPMail->addAddress($email);
    $sendSMTPMail->Body = $mailText;
    $sendSMTPMail->send();
    return true;
}

function checkForMaxMacs($email) {
    global $db_conn;
    global $p;
    $sql_getMacAmount = $db_conn->prepare("SELECT ".$p."_users.maxMacs, COUNT(".$p."_macs.id) FROM ".$p."_users INNER JOIN ".$p."_macs ON ".$p."_macs.userId = ".$p."_users.id WHERE (".$p."_users.email = ? and ".$p."_macs.verified = 1)");
    $sql_getMacAmount->bind_param("s", $email);
    $sql_getMacAmount->execute();
    $sql_getMacAmount->bind_result($maxMacs, $macCount);
    $sql_getMacAmount->fetch();
    if ($macCount < $maxMacs) {
        return true;
    } else {
        return false;
    }
}

?>

<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>WLAN-Sicherheitsfilter</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://cis.gruener-campus-malchow.de/screen_CIS.css">
</head>

<body>
    <div class="headding">
        <h1>WLAN-Sicherheitsfilter</h1>
    </div>
    <div class="textfield">
        <h2>Neues Gerät registrieren</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label class="eingabefeld">
                E-Mail-Adresse:
                <input type="text" name="email" value="<?php echo $email?>">
                <span class="error"><?php echo $emailErr;?></span>
            </label><br>
            <label class="eingabefeld">
                Geräte-Adresse:
                <input type="text" name="mac" maxlength="17" value="<?php echo $mac?>">
                <span class="error"><?php echo $macErr;?></span>
            </label><br>
            <label class="eingabefeld">
                Geräte-Beschreibung:
                <input type="text" name="name" value="<?php echo $name?>">
                <span class="error"><?php echo $nameErr;?></span>
            </label><br>
            <input type="submit">
        </form>
        <p><?php echo $successMsg;?></p>
    </div>

</body>

</html>
