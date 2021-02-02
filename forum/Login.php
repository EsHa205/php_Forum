<html>
<head>
    <link rel="stylesheet" href="Login.css">
</head>
<body>
<button onclick="history.go(-1);">Zurück</button>
<?php
require_once 'db.php';
if (!isset($_POST['kontrolle'])) { // Aufrufen des Formulars
?>
    <form action="" method="post">
        <label>Anmeldename
            <input type="text" id="login" name="login" maxlength="20" required autofocus>
        </label><br>

        <label>Passwort
            <input type="password" id="pw" name="pw" maxlength="30" required>
        </label><br>

        <input type="hidden" name="kontrolle" value="login">
        <input type="submit" value="login">
    </form>
<?php
}
// Einloggen nach Absenden des Formulars
if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='login') {
    $login = trim($_POST['login']);
    $pw = $_POST['pw'];
    $loginHolen = $db->prepare("SELECT Benutzername, Passwort
                                FROM login
                                WHERE Benutzername = \"$login\"");
    $loginHolen->execute();

    if (!$row = $loginHolen->fetch(PDO::FETCH_ASSOC)) { // Prüfen, ob Benutzername in der Datenbank vorhanden ist
        echo 'Der Username existiert nicht!';
        echo "<button onclick=\"history.go(-1);\">Zurück</button>";
    } else {
        $dbpw = $row['PASSWORT'];
        $pepper = "j34o!0mr#4J(";
        if (password_verify($pw.$pepper, $dbpw)) {
            $rechteHolen = $db->prepare("SELECT BenutzerID, rechte.*
                                                FROM benutzer
                                                LEFT JOIN rechte ON benutzer.RechtID = rechte.RechtID
                                                WHERE Benutzername = \"$login\"");
            $rechteHolen->execute();
            $row = $rechteHolen->fetch(PDO::FETCH_ASSOC);
            session_destroy();
            session_start();
            // Session Variablen setzen
            $_SESSION['name'] = $login;
            $_SESSION['id'] = $row['BENUTZERID'];
            if ($row['BEITRAG_LESEN'] != 0) { $_SESSION['recht_b_lesen'] = 1; }
            if ($row['BEITRAG_ERSTELLEN'] != 0) { $_SESSION['recht_b_erstellen'] = 1; }
            if ($row['BEITRAG_BEARBEITEN'] != 0) { $_SESSION['recht_b_bearbeiten'] = 1; }
            if ($row['BEITRAG_BEARBEITEN_ALLE'] != 0) { $_SESSION['recht_b_bearbeiten_a']  = 1; }
            if ($row['BEITRAG_LOESCHEN'] != 0) { $_SESSION['recht_b_loeschen'] = 1; }
            if ($row['BEITRAG_LOESCHEN_ALLE'] != 0) { $_SESSION['recht_b_loeschen_a'] = 1; }

            if ($row['THEMA_LESEN'] != 0) { $_SESSION['recht_t_lesen'] = 1; }
            if ($row['THEMA_ERSTELLEN'] != 0) { $_SESSION['recht_t_erstellen'] = 1; }
            if ($row['THEMA_BEARBEITEN'] != 0) { $_SESSION['recht_t_bearbeiten'] = 1; }
            if ($row['THEMA_BEARBEITEN_ALLE'] != 0) { $_SESSION['recht_t_bearbeiten_a'] = 1; }
            if ($row['THEMA_LOESCHEN'] != 0) { $_SESSION['recht_t_loeschen'] = 1; }
            if ($row['THEMA_LOESCHEN_ALLE'] != 0) { $_SESSION['recht_t_loeschen_a'] = 1; }
            if ($row['THEMEN_SCHLIESSEN'] != 0) { $_SESSION['recht_t_schließen'] = 1; }
            if ($row['THEMEN_ANPINNEN'] != 0) { $_SESSION['recht_t_anpinnen'] = 1; }

            if ($row['KATEGORIE_LESEN'] != 0) { $_SESSION['recht_k_lesen'] = 1; }
            if ($row['KATEGORIE_ERSTELLEN'] != 0) { $_SESSION['recht_k_erstellen'] = 1; }
            if ($row['KATEGORIE_BEARBEITEN'] != 0) { $_SESSION['recht_k_bearbeiten'] = 1; }
            if ($row['KATEGORIE_LOESCHEN'] != 0) { $_SESSION['recht_k_loeschen'] = 1; }

            if ($row['BENUTZER_ANZEIGEN'] != 0) { $_SESSION['recht_be_anzeigen'] = 1; }
            if ($row['BENUTZER_ERSTELLEN'] != 0) { $_SESSION['recht_be_erstellen'] = 1; }
            if ($row['BENUTZER_BEARBEITEN_ALLE'] != 0) { $_SESSION['recht_be_bearbeiten_a'] = 1; }
            if ($row['BENUTZER_LOESCHEN_ALLE'] != 0) { $_SESSION['recht_be_loeschen_a'] = 1; }

            if ($row['RECHTE_BEARBEITEN'] != 0) { $_SESSION['recht_r_bearbeiten'] = 1; }

            echo "Sie wurden erfolgreich eingeloggt. Sie werden in einer Sekunde zur Startseite weitergeleitet!";
            echo '<meta http-equiv="refresh" content="1; URL=Startseite.php">';
            echo'<br><button onclick="location.href=\'Startseite.php\'" type="button">Startseite</button>';
        } else {
            echo 'Das eingegebene Passwort ist falsch!';
        }
    }
}
?>
</body>
</html>


