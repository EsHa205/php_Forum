<html>
<head>
<link rel="stylesheet" href="BeitragErstellen.css">
</head>
<body>
<?php

require_once 'header.php';
// Prüfen, ob Benutzer eingeloggt ist
if (!isset($_SESSION['id'])) {
    echo '<meta http-equiv="refresh" content="0; URL=login.php">';

// Rechte prüfen
} elseif (isset($_SESSION['recht_b_erstellen']) AND isset($_SESSION['id'])) {
    if (!isset($_POST['ueberschrift']) OR !isset($_POST['beitrag']) OR empty($_POST['ueberschrift'])
        OR empty($_POST['beitrag'])) { // Aufrufen des Formulars
        $themenID = $_POST['themenid'];
        $erstellerID = $_SESSION['id'];
        if (isset($_POST['beitragsid'])) {
            $beitragsID = $_POST['beitragsid'];
        }

        if ((!isset($_POST['themenid']) AND !isset($_POST['beitragsid'])) OR !isset($_POST['themenid'])) { // Prüfen, ob notwendige Daten vorhanden sind.
            echo '<meta http-equiv="refresh" content="0; URL=startseite.php">';
        } elseif (!isset($_POST['beitragsid'])) { // Wenn nicht auf ein bestimmtes Thema geantwortet werden soll
            echo '<form action="" method="post">
                <label>Überschrift
                    <input type="text" id="ueberschrift" name="ueberschrift" maxlength="50" required autofocus>                  
                </label><br>
            
                <label>Beitrag
                    <textarea id="beitrag" name="beitrag" required rows="10" cols="60"></textarea>
                </label><br>
                <input type="hidden" name="kontrolle" value="absenden">
                <input type="hidden" name="themenid" value="'.$themenID.'">
                <input type="hidden" name="erstellerid" value="'.$erstellerID.'">
                <input type="submit" value="absenden">
            </form>';
        } else { // Wenn auf ein bestimmtes Thema geantwortet werden soll
            // Daten zum Beitrag ermitteln
            $sql = "SELECT Ueberschrift, Erstellungsdatum, benutzer.Benutzername
                FROM beitraege 
                LEFT JOIN benutzer ON beitraege.ErstellerID = benutzer.BenutzerID 
                WHERE BeitragsID = $beitragsID";
            $beitrag = $db->query($sql);
            $erg = $beitrag->fetch(PDO::FETCH_ASSOC);
            echo '<form action="" method="post">
                <label>Überschrift
                    <input type="text" id="ueberschrift" name="ueberschrift" maxlength="50" required autofocus>                  
                </label><br>
            
                <label>Beitrag
                    <textarea id="beitrag" name="beitrag" required rows="10" cols="60">Antwort auf den Beitrag &raquo'.$erg['UEBERSCHRIFT'].'&laquo von '.$erg['BENUTZERNAME'].' am '.$erg['ERSTELLUNGSDATUM'].': </textarea>
                </label><br>
                <input type="hidden" name="kontrolle" value="absenden">
                <input type="hidden" name="themenid" value="'.$themenID.'">
                <input type="hidden" name="beitragsid" value="'.$beitragsID.'">
                <input type="hidden" name="erstellerid" value="'.$erstellerID.'">
                <input type="submit" value="absenden">
            </form>';
        }
    }
    // Eintragen der Daten in die Datenbank nach Absenden des Formulars
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') {
        $ueberschrift = trim($_POST['ueberschrift']);
        $beitrag = trim($_POST['beitrag']);
        $themenID = $_POST['themenid'];
        $erstellerID = $_POST['erstellerid'];
        $sql = "SELECT themen.KatID, kategorien.Ebene
                FROM themen 
                LEFT JOIN kategorien ON themen.KatID = kategorien.KatID 
                WHERE ThemenID = $themenID";
        $thema = $db->query($sql);
        $erg = $thema->fetch(PDO::FETCH_ASSOC);
        $katID = $erg['KATID'];
        $katEbene = $erg['EBENE'];

        $db->beginTransaction(); // Transaktionsmodus
        // Tabelle beitraege Datensatz einfügen
        $einfuegenDB = $db->prepare("INSERT INTO beitraege (Ueberschrift, Beitrag, ErstellerID, ThemenID) 
                                            VALUES (:ueberschrift, :beitrag, :ersteller, :themenid)");
        $einfuegenDB->bindValue(':ueberschrift', $ueberschrift, PDO::PARAM_STR);
        $einfuegenDB->bindValue(':beitrag', $beitrag, PDO::PARAM_STR);
        $einfuegenDB->bindValue(':ersteller', $erstellerID, PDO::PARAM_INT);
        $einfuegenDB->bindValue(':themenid', $themenID, PDO::PARAM_INT);
        $einfuegenDB->execute();

        $lastID = $db->lastInsertId();
        // Tabelle themen Datensatz updaten
        $updateThemen = $db->prepare("UPDATE themen 
                                            SET Anzahl_Antworten = IFNULL(Anzahl_Antworten, 0) + 1, Letzter_Beitrag = $lastID 
                                            WHERE ThemenID = $themenID");
        $updateThemen->execute();

        // Tabelle kategorien Datensätze updaten
        $updateKategorien = $db->prepare("UPDATE kategorien 
                                                SET Anzahl_Beitraege = IFNULL(Anzahl_Beitraege, 0) + 1, Letzter_Beitrag = $lastID 
                                                WHERE KatID = $katID");
        $updateKategorien->execute();
        // Übergeordnete Kategorien updaten
        $aktuelleKatID = $katID;
        for ($i = $katEbene - 1; $i >=0; $i--) {
            $Anfrage = $db->query("SELECT Uebergeord_KatID 
                                        FROM kategorien 
                                        WHERE KatID = $aktuelleKatID");
            $ergebnis = $Anfrage->fetch(PDO::FETCH_ASSOC);
            $aktuelleKatID = $ergebnis['UEBERGEORD_KATID'];
            $updateKategorien = $db->prepare("UPDATE kategorien 
                                                    SET Anzahl_Beitraege = IFNULL(Anzahl_Beitraege, 0) + 1, Letzter_Beitrag = $lastID 
                                                    WHERE KatID = $aktuelleKatID");
            $updateKategorien->execute();
        }
        $db->commit();
        echo "Der Beitrag wurde gespeichert. Sie werden in 3 Sekunden zum Thema weitergeleitet!";
        echo '<meta http-equiv="refresh" content="3; URL=thema.php?themenid='.$themenID.'">';
    }
} else {
    echo "Sie haben leider keine Berechtigung zum Erstellen von Beiträgen!";
}

?>
</body>
