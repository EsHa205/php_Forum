<html>
<head>
    <link rel="stylesheet" href="KategorieLoeschen.css">
</head>
<body>

<?php
require_once 'header.php';
if (isset($_SESSION['recht_k_loeschen']) AND isset($_SESSION['name'])) { // Rechte prüfen
    if (!isset($_POST['kontrolle'])) { // Aufrufen des Formulars
        $sql = "SELECT KatID, Name
            FROM kategorien
            ORDER BY Ebene, Uebergeord_KatID";
        $kategorien = $db->query($sql);
        ?>
        <form action="" method="post">
            <label>Zu löschende Kategorie auswählen<br>
                <select id="katid" name="katid" size="5">
                    <?php
                    $vorherigeEbene = 0;
                    echo '<optgroup label="Oberkategorien">';
                    while ($row = $kategorien->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['EBENE'] != $vorherigeEbene ) {
                            $vorherigeEbene = $row['EBENE'];
                            echo '</optgroup>';
                            echo '<optgroup label="Ebene '.$row['EBENE'].'">';
                        }
                        echo '<option value="'.$row['KATID'].'">'.$row['NAME'].'</option>';
                    };
                    echo '</optgroup>';?>
                </select>
            </label>
            <br>
            <input type="hidden" name="kontrolle" value="absenden">
            <input type="submit" value="löschen">
        </form>
        <?php
    }
    // Einholen einer Bestätigung nach Auswählen der zu löschenden Kategorie
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden' AND isset($_POST['katid'])) {
        $katID = $_POST['katid'];
        $sql = "SELECT Name
            FROM kategorien
            WHERE KatID = $katID";
        $kategorien = $db->query($sql);
        $row = $kategorien->fetch(PDO::FETCH_ASSOC);
        $katName = $row['NAME'];
        ?>

        <form action="" method="post">
            <?php
            echo '<label>Möchten Sie die Kategorie "'.$katName.'" wirklich löschen?</label>'
            ?>
            <br>
            <input type="hidden" name="kontrolle" value="bestaetigen">
            <input type="hidden" name="katid" value="<?php echo $katID?>">
            <input type="hidden" name="katname" value="<?php echo $katName?>">
            <input type="submit" value="löschen">
            <button onclick="location.href='KategorieLoeschen.php'" type="button">Abbrechen</button>
        </form>
        <?php
    }
    // Löschen der Kategorie nach Bestätigung
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_POST['katid'])) {
        $katID = $_POST['katid'];
        $katName = $_POST['katname'];
        $db->beginTransaction(); // Transaktionsmodus
        $loeschenKategorie = $db->prepare("DELETE FROM kategorien 
                                            WHERE KatID = $katID");
        $loeschenKategorie->execute();
        //$katIDneu = $db->lastInsertId();
        $db->commit();

        echo 'Die Kategorie "'.$katName.'" wurde gelöscht.!';
        echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
        echo '<button onclick="location.href=\'KategorieLoeschen.php\'" type="button">Weitere Kategorie löschen</button><br><br>';
        echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';
    }
} else {
    echo "Sie haben leider keine Berechtigung zum Löschen von Kategorien!";
}
?>


</body>
</html>


