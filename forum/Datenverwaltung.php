<html>
<head>
    <link rel="stylesheet" href="Datenverwaltung.css">
</head>
<body>
<?php
require_once 'header.php';
if (!isset($_SESSION['name'])) { // Prüfen, ob Benutzer eingeloggt ist
    echo "Sie haben keinen Zugriff auf diese Seite!";
} else { // Wenn Benutzer eingeloggt ist ...
    if (isset($_SESSION['recht_k_erstellen'])) {
        echo '<br><button onclick="location.href=\'KategorieErstellen.php\'" type="button">Kategorie erstellen</button><br><br>';
    }
    if (isset($_SESSION['recht_k_loeschen'])) {
        echo '<button onclick="location.href=\'KategorieLoeschen.php\'" type="button">Kategorie löschen</button><br><br>';
    }
    if (isset($_SESSION['recht_be_erstellen'])) {
        echo '<button onclick="location.href=\'BenutzerErstellen.php\'" type="button">Benutzer erstellen</button><br><br>';
    }
    if (isset($_SESSION['recht_be_bearbeiten_a'])) {
        echo '<button onclick="location.href=\'BenutzerBearbeiten.php\'" type="button">Benutzer bearbeiten</button><br><br>';
    }
    if (isset($_SESSION['recht_be_loeschen_a'])) {
        echo '<button onclick="location.href=\'BenutzerLoeschen.php\'" type="button">Benutzer löschen</button><br><br>';
    }
    if (isset($_SESSION['recht_r_bearbeiten'])) {
        echo '<button onclick="location.href=\'RechteErstellen.php\'" type="button">Rechtegruppe erstellen</button><br><br>';
    }
    if (isset($_SESSION['recht_r_bearbeiten'])) {
        echo '<button onclick="location.href=\'RechteBearbeiten.php\'" type="button">Rechtegruppe bearbeiten</button><br><br>';
    }
}
?>
</body>
</html>


