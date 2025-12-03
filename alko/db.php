<?php
$host = "localhost";
$user = "alko_user";     // ← tietokannan käyttäjänimi
$pass = "vahva_salasana"; // ← tietokannan salasana
$db   = "alko";          // ← tietokannan nimi

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    die("Tietokantavirhe: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
