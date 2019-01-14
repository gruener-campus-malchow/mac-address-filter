<?php

$ini = parse_ini_file("config.ini");

$db_conn = new mysqli($ini["db_host"], $ini["db_user"], $ini["db_password"], $ini["db_name"]);

if ($db_conn->connect_error) {
    die($db_conn->connect_error);
};

session_start();