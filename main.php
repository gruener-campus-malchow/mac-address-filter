<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "mail/Exception.php";
require "mail/PHPMailer.php";
require "mail/SMTP.php";

require "config.php";
$ini = $config;

$p = $ini["db_prefix"];

$db_conn = new mysqli($ini["db_host"], $ini["db_user"], $ini["db_password"], $ini["db_name"]);

if ($db_conn->connect_error) {
    die($db_conn->connect_error);
}

function logger($category, $text) {
    global $ini;
    global $db_conn;
    global $p;
    if ($ini["loglevel"] === "DEBUG") {
        $sql_insertLogs = $db_conn->prepare("INSERT INTO ".$p."_logs (category, text) VALUES (?, ?)");
        $sql_insertLogs->bind_param("ss", $category, $text);
        $sql_insertLogs->execute();
    }
    if ($ini["loglevel"] === "INFO" and $category === "registration") {
        $sql_insertLogs = $db_conn->prepare("INSERT INTO ".$p."_logs (category, text) VALUES (?, ?)");
        $sql_insertLogs->bind_param("ss", $category, $text);
        $sql_insertLogs->execute();
    }
}

$sendSMTPMail = new PHPMailer(true);
$sendSMTPMail->isSMTP();
$sendSMTPMail->Host = $ini["smtp_host"];
$sendSMTPMail->SMTPAuth = true;
$sendSMTPMail->Username = $ini["smtp_username"];
$sendSMTPMail->Password = $ini["smtp_password"];
$sendSMTPMail->SMTPSecure = $ini["smtp_security"];
$sendSMTPMail->Port = $ini["smtp_port"];
try {
    $sendSMTPMail->setFrom($ini["email_from"], $ini["email_name"]);
} catch (Exception $e) {
    logger("mail", $e);
}