<?php
error_reporting(E_ALL); // Alle Fehler werden angezeigt. TODO: Für das Live-System auf 0 stellen.

//echo date("H:i:s");
$server= 'localhost';
$datenbank= 'dba20_assignment';
$kennung= 'root';
$passwort= '';

$db = new PDO("mysql:host=$server;dbname=$datenbank", $kennung, $passwort); // Aufbauen einer Verbindung zur MySQL-Datenbank
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); // Werfen von Exceptions bei Fehlern
$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_UPPER); // Alle Spaltennamen als Großbuchstaben
session_start(['cookie_lifetime' => 86400]);

// $_SESSION['rechte'] = 4;
?>