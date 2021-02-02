<!DOCTYPE html>
<html lang="en">
<body>
<header>
    <button onclick="history.go(-1);">Zurück</button>
    <button onclick="location.href='Startseite.php'" type="button">Startseite</button>
    <?php
    require_once 'db.php';
    if (!isset($_SESSION['id'])) { // Wenn Benutzer nicht eingeloggt ist
        echo '<button onclick="location.href=\'Login.php\'" type="button">Login</button>';
        echo '<button onclick="location.href=\'Registrieren.php\'" type="button">Registrieren</button>';
    } else if (isset($_SESSION['id'])) { // Wenn Benutzer eingeloggt ist
        echo '<button onclick="location.href=\'Logout.php\'" type="button">Logout</button>';
        echo '<button onclick="location.href=\'Einstellungen.php\'" type="button">Einstellungen</button>';
        if (isset($_SESSION['recht_k_erstellen']) OR isset($_SESSION['recht_k_loeschen']) OR
            isset($_SESSION['recht_be_erstellen']) OR isset($_SESSION['recht_be_bearbeiten_a']) OR
            isset($_SESSION['recht_be_loeschen_a']) OR isset($_SESSION['recht_r_bearbeiten'])) {
            echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Datenverwaltung</button>';
        }
    }
    ?>
</header>
</body>
</html>