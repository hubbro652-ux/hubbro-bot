<?php

$host = "sql305.byetcluster.com";
$user = "if0_42248008";
$pass = "Shashika1092736";
$db   = "if0_42248008_Hubpro";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

?>