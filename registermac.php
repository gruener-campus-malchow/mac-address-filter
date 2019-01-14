<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>WLAN-Sicherheitsfilter</title>
</head>

<body>
    <h1>WLAN-Sicherheitsfilter</h1>
    <h2>Neues Gerät registrieren</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label>
            E-Mail-Adresse:
            <input type="text" name="email">
        </label>
        <label>
            Geräte-Adresse:
            <input type="text" name="mac">
        </label>
        <input type="submit">
    </form>

</body>

</html>

<?php

$email = $mac = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = testInput($_POST["email"]);
    $mac = testInput(($_POST["mac"]));
    echo $email;
    echo $mac;
}

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}