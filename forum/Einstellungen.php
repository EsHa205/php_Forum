<html>
<head>
    <link rel="stylesheet" href="Einstellungen.css">
</head>
<body>
<?php
require_once 'header.php';
if (!isset($_SESSION['name'])) { // Prüfen, ob Benutzer eingeloggt ist, sonst weiterleitung zum Login
    echo '<meta http-equiv="refresh" content="0; URL=login.php">';
} else { // Wenn Benutzer eingeloggt ist
    $name = $_SESSION['name'];
    echo '<br><button onclick="location.href=\'BenutzerAnzeigen.php?name='.$name.'\'" type="button">Benutzer anzeigen</button><br><br>';
    echo '<button onclick="location.href=\'BenutzerBearbeiten.php\'" type="button">Benutzer bearbeiten</button><br><br>';
    if ($name != "admin") {
        echo '<button onclick="location.href=\'BenutzerLoeschen.php\'" type="button">Benutzer löschen</button><br><br>';
    }
}
?>
</body>
</html>


