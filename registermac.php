<?php

$email = $mac = "";
$emailErr = $macErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "E-Mail erforderlich";
    } else {
        $email = testInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Keine gültige E-Mail-Adresse";
        }
    }
    if (empty($_POST["mac"])) {
        $macErr = "Geräte-Adresse erforderlich";
    } else {
        $mac = testInput($_POST["mac"]);
        if (!filter_var($mac, FILTER_VALIDATE_MAC)) {
            $emailErr = "Keine gültige MAC-Adresse";
        }
    }
    echo $email;
    echo $mac;
}

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
            <input type="text" name="email">
            <span class="error"><?php echo $emailErr;?></span>
        </label><br>
        <label>
            Geräte-Adresse:
            <input type="text" name="mac">
            <span class="error"><?php echo $macErr;?></span>
        </label><br>
        <input type="submit">
    </form>

</body>

</html>
