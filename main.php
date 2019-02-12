<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "mail/Exception.php";
require "mail/PHPMailer.php";
require "mail/SMTP.php";

$ini = parse_ini_file("config.ini");
$p = $ini["db_prefix"];

$db_conn = new mysqli($ini["db_host"], $ini["db_user"], $ini["db_password"], $ini["db_name"]);

if ($db_conn->connect_error) {
    die($db_conn->connect_error);
};

$sendSMTPMail = new PHPMailer(false);
$sendSMTPMail->isSMTP();
$sendSMTPMail->Host = $ini["smtp_host"];
$sendSMTPMail->SMTPAuth = true;
$sendSMTPMail->Username = $ini["smtp_username"];
$sendSMTPMail->Password = $ini["smtp_password"];
$sendSMTPMail->SMTPSecure = $ini["smtp_security"];
$sendSMTPMail->Port = $ini["smtp_port"];
$sendSMTPMail->setFrom($ini["email_from"], $ini["email_name"]);