<?php
$user = $_ENV["DB_USER"];
$password = $_ENV["DB_PASSWORD"];
$db_name = $_ENV["DB_NAME"];
$host = $_ENV["DB_HOST"];

$mysqli = new mysqli($host, $user, $password, $db_name);
