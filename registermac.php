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