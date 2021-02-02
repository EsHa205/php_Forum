<html>
<head>
    <link rel="stylesheet" href="BenutzerLoeschen.css">
</head>
<body>
<?php
require_once 'header.php';
if (isset($_SESSION['recht_be_loeschen_a']) AND isset($_SESSION['name'])) { // Rechte prüfen
    if (!isset($_POST['kontrolle'])) { // Aufrufen des Formulars
        $sql = "SELECT Benutzername
                FROM benutzer
                WHERE Benutzername != 'admin'
                ORDER BY Benutzername";
        $benutzer = $db->query($sql);
        ?>
        <form action="" method="post">
            <label>Zu löschenden Benutzer auswählen<br>
                <select id="name" name="name" size="5">
                    <?php
                    while ($row = $benutzer->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="'.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</option>';
                    }; ?>
                </select>
            </label>
            <br>
            <input type="hidden" name="kontrolle" value="absenden">
            <input type="submit" value="löschen">
        </form>
        <?php
    }
    // Einholen einer Bestätigung nach Auswählen des zu löschenden Benutzers
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden' AND isset($_POST['name'])) {
        $name = $_POST['name'];
        if ($name == "admin") {
            echo "Sie können den Administrator nicht löschen";
        } else {
            $sql = "SELECT Benutzername
                FROM benutzer
                WHERE Benutzername = \"$name\"";
            $benutzer = $db->query($sql);
            $row = $benutzer->fetch(PDO::FETCH_ASSOC)
            ?>

            <form action="" method="post">
                <?php
                echo '<label>Möchten Sie den Benutzer "'.$row['BENUTZERNAME'].'" wirklich löschen?</label>'
                ?>
                <br>
                <input type="hidden" name="kontrolle" value="bestaetigen">
                <input type="hidden" name="name" value="<?php echo $name?>">
                <input type="submit" value="löschen">
                <button onclick="location.href='BenutzerLoeschen.php'" type="button">Abbrechen</button>
            </form>
            <?php
        }
    }
    // Löschen des Benutzers nach Bestätigung
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_POST['name'])) {
        $name = $_POST['name'];
        $db->beginTransaction(); // Transaktionsmodus
        $loeschenBenutzer = $db->prepare("DELETE FROM benutzer 
                                                    WHERE Benutzername = \"$name\"");
        $loeschenBenutzer->execute();

        $loeschenLogin = $db->prepare("DELETE FROM login 
                                                    WHERE Benutzername = \"$name\"");
        $loeschenBenutzer->execute();

        $db->commit();

        echo 'Der Benutzer "'.$name.'" wurde gelöscht.!';
        echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
        echo '<button onclick="location.href=\'BenutzerLoeschen.php\'" type="button">Weiteren Benutzer löschen</button><br><br>';
        echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';
    }

} elseif (isset($_SESSION['name'])) { // Löschen des eigenen Benutzers
    if (!isset($_POST['kontrolle'])){ // Einholen einer Bestätigung zum löschen des eigenen Benutzers
        $name = $_SESSION['name'];
        ?>
        <form action="" method="post">
            <?php
            echo '<label>Möchten Sie den Ihren Account wirklich löschen?</label>'
            ?>
            <br>
            <input type="hidden" name="kontrolle" value="bestaetigen">
            <input type="hidden" name="name" value="<?php echo $name?>">
            <input type="submit" value="löschen">
            <button onclick="location.href='Einstellungen.php'" type="button">Abbrechen</button>
        </form>
        <?php
    }
    // Löschen des eigenen Benutzers nach Bestätigung
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_POST['name'])) {
        $name = $_POST['name'];
        $db->beginTransaction(); // Transaktionsmodus
        $loeschenBenutzer = $db->prepare("DELETE FROM benutzer
                                                    WHERE Benutzername = \"$name\"");
        $loeschenBenutzer->execute();

        $loeschenLogin = $db->prepare("DELETE FROM login 
                                                WHERE Benutzername = \"$name\"");
        $loeschenLogin->execute();

        $db->commit();
        require_once 'Logout.php';

        echo "Der Benutzer wurde gelöscht. Sie werden in 3 Sekunden zur Startseite weitergeleitet!";
        echo '<meta http-equiv="refresh" content="3; URL=Startseite.php">';
    }
} else { // Wenn Benutzer nicht eingeloggt ist
    echo "Sie haben leider keine Berechtigung zum Löschen von Benutzern!";
}
?>
</body>
</html>