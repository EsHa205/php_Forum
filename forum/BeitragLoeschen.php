<html>
<head>
    <link rel="stylesheet" href="BeitragLoeschen.css">
</head>
<body>

<?php
require_once 'header.php';
if (isset($_POST['beitragsid']) AND isset($_SESSION['id']) AND !isset($_POST['kontrolle'])) { // Aufrufen des Formulars
    $zuLoeschendeBeitragsID = $_POST['beitragsid'];
    $themenID = $_POST['themenid'];

    $sql = "SELECT ErstellerID
                    FROM beitraege
                    WHERE BeitragsID = $zuLoeschendeBeitragsID";
    $erstellerSuchen = $db->query($sql);
    $row = $erstellerSuchen->fetch(PDO::FETCH_ASSOC);

    if (isset($_SESSION['recht_b_loeschen_a']) OR // Rechte zum Löschen aller Beiträge prüfen
        (isset($_SESSION['recht_b_loeschen']) AND $_SESSION['id'] == $row['ERSTELLERID'])) { // Recht zum Löschen des eigenen Beitrags prüfen
        ?>
        <form action="" method="post">
            <label>Möchten Sie den Beitrag wirklich löschen?</label>
            <br>
            <input type="hidden" name="kontrolle" value="bestaetigen">
            <input type="hidden" name="beitragsid" value="<?php echo $zuLoeschendeBeitragsID?>">
            <input type="hidden" name="themenid" value="<?php echo $themenID?>">
            <input type="submit" value="löschen">
            <button onclick="location.href='Thema.php?=<?php echo $themenID?>" type="button">Abbrechen</button>
        </form>
        <?php
    } else { // Wenn die Berechtigungen fehlen
        echo "Sie haben leider keine Berechtigung zum Löschen von Beiträgen!";
    }

} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle'] == 'bestaetigen') { // Löschen des Beitrags nach Bestätigung aus Formular
    // Löschen des Beitrags nach Bestätigung
    $zuLoeschendeBeitragsID = $_POST['beitragsid'];
    $themenID = $_POST['themenid'];

    $db->beginTransaction(); // Transaktionsmodus
    // Beitrag löschen
    $loescheBeitrag = $db->prepare("DELETE FROM beitraege 
                                            WHERE BeitragsID = $zuLoeschendeBeitragsID");
    $loescheBeitrag->execute();

    // Prüfen, ob Beitrag letzter Beitrag eines Themas war
    $sql = "SELECT ThemenID
            FROM themen
            WHERE Letzter_Beitrag = $zuLoeschendeBeitragsID";
    $themaLetzterBeitragSuchen = $db->query($sql);
    if ($erg = $themaLetzterBeitragSuchen->fetch(PDO::FETCH_ASSOC)) { // Wenn Thema gefunden, dann anpassen
        // Alle Beiträge aus dem Thema suchen und nach Erstellungsdatum absteigend sortieren
        $sql = "SELECT beitragsID
                FROM beitraege
                WHERE ThemenID = $themenID AND BeitragsID != $zuLoeschendeBeitragsID
                ORDER BY Erstellungsdatum desc ";
        $neuerLetzterBeitragSuchen = $db->query($sql);
        $erg = $neuerLetzterBeitragSuchen->fetch(PDO::FETCH_ASSOC);
        $neuerLetzterBeitragID = $erg['BEITRAGSID'];
        $updateThema = $db->prepare("UPDATE themen 
                                            SET Letzter_Beitrag = $neuerLetzterBeitragID
                                            WHERE ThemenID = $themenID");
        $updateThema->execute();
    }

    // Prüfen, ob Beitrag letzter Beitrag einer Kategorie war
    $sql = "SELECT KatID, Ebene
            FROM kategorien
            WHERE Letzter_Beitrag = $zuLoeschendeBeitragsID
            ORDER BY Ebene desc";
    $katLetzterBeitrag = $db->query($sql);
    $aktuellLetzterBeitragID = 0;
    while ($erg = $katLetzterBeitrag->fetch(PDO::FETCH_ASSOC)) { // Alle Kategorie anpassen
        $katID = $erg['KATID'];
        $sql = "SELECT BeitragsID
                FROM beitraege
                LEFT JOIN themen t ON beitraege.ThemenID = t.ThemenID
                WHERE (t.KatID = $katID AND BeitragsID != $zuLoeschendeBeitragsID) OR BeitragsID = $aktuellLetzterBeitragID
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

    // Prüfen, ob Beitrag Bearbeitungen hatte
    $sql = "SELECT bearbeitungen.BearbID
            FROM bearbeitungen
            LEFT JOIN beitraege ON bearbeitungen.BearbID = beitraege.BearbID
            WHERE BeitragsID = $zuLoeschendeBeitragsID";
    $bearbeitungSuchen = $db->query($sql);
    if ($erg = $bearbeitungSuchen->fetch(PDO::FETCH_ASSOC)) {
        $bearbID = $erg['BEARBID'];
        $loescheBearbeitung = $db->prepare("DELETE FROM bearbeitungen 
                                            WHERE BearbID = $bearbID");
        $loescheBearbeitung->execute();
    }
    $db->commit();

    echo "Der Beitrag wurde gelöscht!";
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'Thema.php?='.$themenID.'" type="button">Zum Thema</button>';

} else {// Wenn Benutzer nicht eingeloggt ist
        echo '<meta http-equiv="refresh" content="0; URL=login.php">';
}
?>


</body>
</html>


