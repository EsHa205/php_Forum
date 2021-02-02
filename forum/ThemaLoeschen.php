<html>
<head>
    <link rel="stylesheet" href="ThemaLoeschen.css">
</head>
<body>

<?php
require_once 'header.php';
if (isset($_POST['themenid']) AND isset($_SESSION['id']) AND !isset($_POST['kontrolle'])) { // Aufrufen des Formulars
    $zuLoeschendesThemaID = $_POST['themenid'];

    $sql = "SELECT ErstellerID, KatID
                    FROM themen
                    WHERE ThemenID = $zuLoeschendesThemaID";
    $erstellerSuchen = $db->query($sql);
    $row = $erstellerSuchen->fetch(PDO::FETCH_ASSOC);

    $katID = $row['KATID'];

    if (isset($_SESSION['recht_t_loeschen']) OR // Rechte zum Löschen von Themen prüfen
        (isset($_SESSION['recht_t_loeschen_a']) AND $_SESSION['id'] == $row['ERSTELLERID'])) { // Recht zum Löschen des eigenen Themas prüfen
        ?>
        <form action="" method="post">
            <label>Möchten Sie das Thema wirklich löschen?</label>
            <br>
            <input type="hidden" name="kontrolle" value="bestaetigen">
            <input type="hidden" name="themenid" value="<?php echo $zuLoeschendesThemaID?>">
            <input type="hidden" name="katid" value="<?php echo $katID?>">
            <input type="submit" value="löschen">
            <button onclick="location.href='Thema.php?=<?php echo $zuLoeschendesThemaID?>" type="button">Abbrechen</button>
        </form>
        <?php
    } else { // Wenn die Berechtigungen fehlen
        echo "Sie haben leider keine Berechtigung zum Löschen von Themen!";
    }

} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle'] == 'bestaetigen') { // Löschen des Themas nach Bestätigung aus Formular
    // Löschen des Themas nach Bestätigung
    $zuLoeschendesThemaID = $_POST['themenid'];
    $katID = $_POST['katid'];

    $db->beginTransaction(); // Transaktionsmodus
    // Letzter Beitrag des Themas suchen
    $sql = "SELECT Letzter_Beitrag
            FROM themen
            WHERE ThemenID = $zuLoeschendesThemaID";
    $letztenBeitragSuchenAusThema = $db->query($sql);
    $erg = $letztenBeitragSuchenAusThema->fetch(PDO::FETCH_ASSOC);
    $letzterBeitragAusThema = $erg['LETZTER_BEITRAG'];

    // Thema löschen
    $loescheThema = $db->prepare("DELETE FROM themen 
                                            WHERE ThemenID = $zuLoeschendesThemaID");
    $loescheThema->execute();

    // Alle Beiträge des Themas löschen
    $loescheBeitraege = $db->prepare("DELETE FROM beitraege
                                                WHERE ThemenID = $zuLoeschendesThemaID");
    $loescheBeitraege->execute();

    // Prüfen, ob Beiträge Bearbeitungen hatten
    $sql = "SELECT bearbeitungen.BearbID
            FROM bearbeitungen
            LEFT JOIN beitraege ON bearbeitungen.BearbID = beitraege.BearbID
            WHERE ThemenID = $zuLoeschendesThemaID";
    $bearbeitungenSuchen = $db->query($sql);
    while ($erg = $bearbeitungenSuchen->fetch(PDO::FETCH_ASSOC)) {
        $bearbID = $erg['BEARBID'];
        $loescheBearbeitung = $db->prepare("DELETE FROM bearbeitungen 
                                            WHERE BearbID = $bearbID");
        $loescheBearbeitung->execute();
    }

    // Prüfen, ob Beitrag letzter Beitrag einer Kategorie war
    $sql = "SELECT KatID, Ebene
            FROM kategorien
            WHERE Letzter_Beitrag = $letzterBeitragAusThema
            ORDER BY Ebene desc";
    $katLetzterBeitrag = $db->query($sql);
    $aktuellLetzterBeitragID = 0;
    while ($erg = $katLetzterBeitrag->fetch(PDO::FETCH_ASSOC)) { // Alle Kategorie anpassen
        $katID = $erg['KATID'];
        $sql = "SELECT BeitragsID
                FROM beitraege
                LEFT JOIN themen t ON beitraege.ThemenID = t.ThemenID
                WHERE (t.KatID = $katID AND beitraege.ThemenID != $zuLoeschendesThemaID) OR BeitragsID = $aktuellLetzterBeitragID
                ORDER BY Erstellungsdatum desc";
        $neuerLetzterBeitragSuchen = $db->query($sql);
        if ($erg = $neuerLetzterBeitragSuchen->fetch(PDO::FETCH_ASSOC)) {
            $aktuellLetzterBeitragID = $erg['BEITRAGSID'];
            $updateKategorie = $db->prepare("UPDATE kategorien 
                                            SET Letzter_Beitrag = $aktuellLetzterBeitragID
                                            WHERE KatID = $katID");
            $updateKategorie->execute();
        }
    }
    $db->commit();

    echo "Das Thema wurde gelöscht!";
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'Kategorie.php?='.$katID.'" type="button">Zur Kategorie</button>';

} else { // Wenn Benutzer nicht eingeloggt ist
        echo '<meta http-equiv="refresh" content="0; URL=login.php">';
}
?>
</body>
</html>


