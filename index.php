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
            $provider ='@'.explode('@',$email)[1];
            if (in_array($provider, $ini["email_suffix"])) {
                $emailOK = true;
            } else {
                $emailErr .= "Kein gültiger Provider. Bitte bei der Schule melden.";
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
        if (checkForMaxMacs($email) === true) {
            $insertUser = $db_conn->prepare("INSERT IGNORE INTO " . $p . "_users (email) VALUES (?)");
            $insertUser->bind_param("s", $email);
            $insertUser->execute();

            $selectUserId = $db_conn->prepare("SELECT id FROM " . $p . "_users WHERE (email = ?)");
            $selectUserId->bind_param("s", $email);
            $selectUserId->execute();
            $selectUserId->bind_result($userId);
            $selectUserId->fetch();
            $selectUserId->close();

            $insertMac = $db_conn->prepare("INSERT INTO " . $p . "_macs (userId, mac, deviceName, token) VALUES (?,?,?,?)");
            try {
                $token = bin2hex(random_bytes(20));
            } catch (Exception $e) {
                $successMsg = "Unser Zufallsgenerator hat nicht funktioniert. Probieren Sie es bitte erneut. Falls es weiterhin nicht funktionieren sollte, wenden Sie Sich bitte an den Fachbereich Informatik";
            }
            $insertMac->bind_param("isss", $userId, $mac, $name, $token);
            $insertMac->execute();
            if (sendMail($email, $token) === true) {
                $successMsg = "Bitte bestätigen Sie die Registrierung mithilfe der E-Mail, die Ihnen soeben zugeschickt wurde.";
                logger("registration", "MAC " . $mac . " erfolgreich registriert");
            } else {
                $successMsg = "Etwas ging beim Senden der Bestätigungsmail schief. Probieren Sie es bitte erneut. Falls es weiterhin nicht funktionieren sollte, wenden Sie Sich bitte an den Fachbereich Informatik";
            }
        } else {
            $successMsg = "Sie haben bereits die maximale Anzahl an Geräten registriert. Falls Sie ein höheres Limit benötigen, wenden Sie Sich bitte an den Fachbereich Informatik.";
        }
    }
}

function testInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sendMail($email, $token)
{
    global $ini;
    if ($ini["email_mode"] === "smtp") {
        global $sendSMTPMail;
    } elseif ($ini["email_mode"] === "sendmail") {
        global $emailFrom;
    }

    $mailText = "Sehr geehrte/r Nutzer/in. \n\nJemand hat gerade mit ihrer E-Mail-Adresse ein Gerät im WLAN-Sicherheitsfilter des GCM registriert. "
        . "Falls Sie das waren, klicken Sie bitte auf folgenden Link, um die Registrierung abzuschließen:\n\n"
        . $ini["domain"]
        . "/verify.php?token="
        . $token .
        "\n\nFalls Sie Sich daran nicht erinnern können, ignorieren Sie diese E-Mail einfach. \n\n"
        . "Mit freundlichen Grüßen, \n\nIhr CIS & FBI \n(CampusInformationSsystem & Fachbereich Informatik)";
    $mailBetreff = "WLAN-Sicherheitsfilter - Registrierung bestätigen";

    if ($ini["email_mode"] === "smtp") {
        $sendSMTPMail->Subject = $mailBetreff;
        $sendSMTPMail->addAddress($email);
        $sendSMTPMail->Body = $mailText;
        try {
            $sendSMTPMail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            logger("mail", "Fehler beim Senden einer Bestätigungsmail an " . $email . ": " . $e);
            return false;
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

function checkForMaxMacs($email)
{
    global $db_conn;
    global $p;
    $sql_getMacAmount = $db_conn->prepare("SELECT " . $p . "_users.maxMacs, COUNT(" . $p . "_macs.id) FROM " . $p . "_users INNER JOIN " . $p . "_macs ON " . $p . "_macs.userId = " . $p . "_users.id WHERE (" . $p . "_users.email = ? and " . $p . "_macs.verified = 1)");
    $sql_getMacAmount->bind_param("s", $email);
    $sql_getMacAmount->execute();
    $sql_getMacAmount->bind_result($maxMacs, $macCount);
    $sql_getMacAmount->fetch();
    if ($macCount < $maxMacs or $maxMacs === null) {
        return true;
    } else {
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>WLAN-Sicherheitsfilter</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://gcm.schule/screen_CIS.css">
</head>

<body>
        <div class="headder">
            <div class="image">
                <a href="https://gcm.schule" class="invisible_link">
                    <img class="logo" src="https://gcm.schule/logo_gcm_progressbar.gif" alt="logo_gcm">
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
            Dieser Service wurde an unserer Schule von <a class="std" href="https://github.com/sn0wmanmj" target="_blank">Moritz Jannasch</a> entwickelt und von Alexander Baldauf weiterentwickelt und auf Sicherheitsaspekte hin untersucht.  
            </p>
            <p>
    Es können nur Kolleginnen und Kollegen ihre Geräte registrieren, die eine Dienstmailadresse haben. Über diesen Mail-Account laufen unsere Sicherheitsbestätigungen, damit nur Mitarbeiterinnen und Mitarbeiter des Campus ihre Geräte freischalten können. Für die Benutzung muss man außerdem die Zugangsdaten des WLANs (Wifi) kennen.
            </p>
        </div>
<?php
if($ini["message"] != "NONE")
{
    echo ' <div class="warning">'.$ini["message"].'</div>';
}
?>
        <div class="textfield">

                <h3><p>Neues Gerät registrieren</p></h3>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <p>          
                    <label class="eingabefeld">
                    E-Mail-Adresse:
                    <input type="text" name="email" value="<?php echo $email?>">
                    <span class="error"><?php echo $emailErr;?></span>
                    </label>
                </p>
                <p>
                    <label class="eingabefeld" data-tooltip="I am a tooltip">
                    Geräte-Adresse:
                    <input type="text" name="mac" maxlength="17" value="<?php echo $mac?>">
                        <div class="infoschalter">Hilfe einblenden
                            <div class="infotext">
                                
		                    <h1>Geräte-Adresse</h1>
		                    <p>
			                    Die Geräte-Adresse ist eine zwölfstellige Hexadezimal-Nummer, die durch Doppelpunkte in sechs Blöcke getrennt wird. Sie gehört zur Hardware, also der WLAN-Karte oder der LAN-Karte. Sie wird eigentlich <a class="std" href="https://de.wikipedia.org/wiki/MAC-Adresse" target="_blank">MAC-Adresse</a> oder auch physikalische Adresse genannt. Mittels dieser kann das Netwerkgerät des Computers mit dem Netzwerk bekannt gemacht werden, ähnlich dem Nummernschild eines Autos. So können unbekannte Computer vom Netzwerk des GCM blockiert werden. Deshalb heißt das System WLAN-Sicherheitsfilter.<br>
Meist sieht diese beispielsweise so aus: e1:34:74:e2:d1:d9, manchmal auch so: e1-34-74-e2-d1-d9
		                    </p>
		                    <h2>Windows</h2>
		                    <p>
			                    In den Details der Netzwerkeinstellungen findet man die Adresse in den Details unter physikalische Adresse.
		                    </p>

		                    <h2>Android</h2>
		                    <p>
			                    Bitte suchen unter: Einstellungen => System => Über das Telefon => Status => WLAN-MAC-Adresse
		                    </p>

		                    <h2>iOS</h2>
		                    <p>
			                    In den Details der Netzwerkeinstellungen findet man die Daten unter physikalische Adresse.
		                    </p>
		                    <h2>Linux (Debian & Co)</h2>
		                    <p>
                                Am Schnellsten erfährt man die Mac-Adresse im Terminal mit dem Befehl "ifconfig" und findet sie unter "ether..."
		                    </p>

                            </div>
                    </div>
                    <span class="error"><?php echo $macErr;?></span>
                    </label>
                </p>
                <p>
                    <label class="eingabefeld">
                    Geräte-Beschreibung damit man seine Geräte auseinander halten kann (z.B. Laptop, Fon, Tablet, o.ä.):
                    <input type="text" name="name" value="<?php echo $name?>">
                    <span class="error"><?php echo $nameErr;?></span>
                    </label>
                </p>
                <p>
                    <input type="submit">
                </p>
            </form>
            <p><?php echo $successMsg;?></p>

        </div>
    </div>

</body>

</html>
