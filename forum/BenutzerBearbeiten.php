<html>
<head>
    <link rel="stylesheet" href="BenutzerBearbeiten.css">
</head>
<body>
<?php
require_once 'header.php';
// Aufrufen des Formulars zum Auswählen eines Benutzers
if (isset($_SESSION['recht_be_bearbeiten_a']) AND isset($_SESSION['name']) AND !isset($_POST['kontrolle'])) { // Rechte prüfen
    $sql = "SELECT Benutzername
            FROM benutzer                
            ORDER BY Benutzername";
    $benutzer = $db->query($sql);
    ?>
    <form action="" method="post">
        <label>Zu bearbeitenden Benutzer auswählen<br>
            <select id="name" name="name" size="5">
                <?php
                while ($row = $benutzer->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</option>';
                }; ?>
            </select>
        </label>
        <br>
        <input type="hidden" name="kontrolle" value="absenden">
        <input type="submit" value="bearbeiten">
    </form>
    <?php
}

// Eintragen der Änderungen nach Auswählen des zu bearbeitenden Benutzers oder des eigenen Benutzers
if ((isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden' AND isset($_POST['name'])) OR (isset($_SESSION['name']) AND !isset($_POST['kontrolle']) AND !isset($_SESSION['recht_be_bearbeiten_a']))) {
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } elseif (isset($_SESSION['name'])) { // Abfragen der Bearbeitungen des eigenen Benutzers
        $name = $_SESSION['name'];
    }

    $sql = "SELECT benutzer.Benutzername, Nachname, Vorname, E_Mail, l.Passwort
            FROM benutzer
            LEFT JOIN login l on benutzer.Benutzername = l.Benutzername
            WHERE benutzer.Benutzername = \"$name\"";
    $benutzer = $db->query($sql);
    $row = $benutzer->fetch(PDO::FETCH_ASSOC)
    ?>

    <form action="" method="post">
        <label>Benutzer <?php echo $row['BENUTZERNAME']?> bearbeiten:</label>

        <br>
        <label>Passwort
            <input type="password" id="pw" name="pw" maxlength="30">
        </label><br>

        <label>Nachname
            <input type="text" id="nachname" name="nachname" value="<?php echo $row['NACHNAME']?>" maxlength="50" required>
        </label><br>

        <label>Vorname
            <input type="text" id="vorname" name="vorname" value="<?php echo $row['VORNAME']?>" maxlength="50" required>
        </label><br>

        <label>E-Mail Adresse
            <input type="text" id="email" name="email" value="<?php echo $row['E_MAIL']?>" maxlength="100" required>
        </label><br>

        <input type="hidden" name="kontrolle" value="bestaetigen">
        <input type="hidden" name="name" value="<?php echo $name?>">
        <input type="submit" value="Bearbeitungen bestätigen">
        <button onclick="location.href='BenutzerBearbeiten.php'" type="button">Abbrechen</button>
    </form>
    <?php
} elseif (!isset($_SESSION['recht_be_bearbeiten_a']) AND !isset($_SESSION['name'])) { // Wenn Benutzer nicht eingeloggt ist
    echo "Sie haben leider keine Berechtigung zum Bearbeiten von Benutzern!";
}

// Bearbeiten des Benutzers nach Bestätigung
if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_POST['name'])) {
    $name = $_POST['name'];
    $db->beginTransaction(); // Transaktionsmodus
    if (!empty($_POST['nachname'])) {
        $nachname = $_POST['nachname'];
        $bearbeiteNachname = $db->prepare("UPDATE benutzer
                                                SET Nachname = \"$nachname\"                                           
                                                WHERE Benutzername = \"$name\"");
        $bearbeiteNachname->execute();
    }
    if (!empty($_POST['vorname'])) {
        $vorname = $_POST['vorname'];
        $bearbeiteVorname = $db->prepare("UPDATE benutzer
                                                SET Vorname = \"$vorname\"                                                   
                                                WHERE Benutzername = \"$name\"");
        $bearbeiteVorname->execute();
    }
    if (!empty($_POST['email'])) {
        $email = $_POST['email'];
        $bearbeiteEmail = $db->prepare("UPDATE benutzer
                                                SET E_Mail = \"$email\"                                                    
                                                WHERE Benutzername = \"$name\"");
        $bearbeiteEmail->execute();
    }
    if (!empty($_POST['pw'])) {
        $pepper = "j34o!0mr#4J(";
        $pw = password_hash($_POST['pw'].$pepper, PASSWORD_DEFAULT);
        $bearbeiteLogin = $db->prepare("UPDATE login
                                            SET Passwort = \"$pw\"
                                                WHERE Benutzername = \"$name\"");
        $bearbeiteLogin->execute();
    }
    $db->commit();

    echo 'Der Benutzer "'.$name.'" wurde geändert.!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'BenutzerAnzeigen.php?name=' . $name . '\'" type="button">Benutzer anzeigen</button><br><br>';
    if (isset($_SESSION['recht_be_bearbeiten_a'])) {
        echo '<button onclick="location.href=\'BenutzerBearbeiten.php\'" type="button">Weiteren Benutzer bearbeiten</button><br><br>';
        echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';
    }
}
?>
</body>
</html>