<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();

// --- SALASANA ---
define('UPDATE_PASSWORD', 'TÄMÄ_ON_SENSOROITU');

if (isset($_POST['password'])) {
    if ($_POST['password'] === UPDATE_PASSWORD) {
        $_SESSION['authorized'] = true;
    } else {
        $error = "Väärä salasana!";
    }
}

if (!isset($_SESSION['authorized'])) {
    ?>
    <form method="post">
        <input type="password" name="password" placeholder="Salasana" required>
        <button type="submit">Kirjaudu</button>
    </form>
    <?php
    exit;
}

// --- COMPOSER AUTOLOAD ---
require_once __DIR__ . '/vendor/autoload.php';

// --- SIMPLEXLSX ---
require_once __DIR__ . '/vendor/shuchkin/simplexlsx/src/SimpleXLSX.php';

// --- YHTEYS TIETOKANTAAN ---
require 'db.php';

// --- LADATAAN XLSX ---
$remote_xlsx_url = "https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx";
$tempFile = tempnam(sys_get_temp_dir(), 'alko_');
file_put_contents($tempFile, file_get_contents($remote_xlsx_url));

// --- PARSINTA ---
if ($xlsx = SimpleXLSX::parse($tempFile)) {

    $firstRow = $xlsx->rows()[0]; 
    $lastUpdateText = $firstRow[0] ?? 'Ei päivämäärää';
    $lastUpdateText = preg_replace('/^Alkon hinnasto\s*/i', '', $lastUpdateText);

    file_put_contents(__DIR__ . '/hinnasto_paivitysaika.txt', $lastUpdateText);

    $mysqli->query("UPDATE settings SET value='" . $mysqli->real_escape_string($lastUpdateText) . "' WHERE name='last_update'");
    
    $dbColumns = [
        'Numero',
        'Nimi',
        'Valmistaja',
        'Pullokoko',
        'Hinta',
        'Litrahinta',
        'Tyyppi',
        'Valmistusmaa',
        'Vuosikerta',
        'Alkoholi',
        'Energia_kcal_100ml'
    ];

    $mysqli->query("TRUNCATE TABLE alko");

    $columnsWithTicks = array_map(fn($col) => "`$col`", $dbColumns);
    $placeholders = implode(',', array_fill(0, count($dbColumns), '?'));
    $stmt = $mysqli->prepare("INSERT INTO alko (" . implode(',', $columnsWithTicks) . ") VALUES ($placeholders)");

    $types = str_repeat('s', count($dbColumns));

    foreach ($xlsx->rows() as $i => $row) {
        if ($i === 0) continue;
        $data = [];
        foreach ($dbColumns as $j => $col) {
            $data[] = $row[$j] ?? null;
        }
        $stmt->bind_param($types, ...$data);
        $stmt->execute();
    }

    echo "Hinnasto päivitetty onnistuneesti!<br>";
    echo "Alkon hinnasto viimeksi päivitetty: " . htmlspecialchars($lastUpdateText);

    header("Refresh:3; url=index.php");
    exit;
} else {
    echo SimpleXLSX::parseError();
}

unlink($tempFile);
