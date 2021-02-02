<html>
<head>
    <link rel="stylesheet" href="ThemaErstellen.css">
</head>
<body>
<?php
require_once 'header.php';
// Wenn Benutzer nicht eingeloggt ist
if (!isset($_SESSION['id'])) {
    echo '<meta http-equiv="refresh" content="0; URL=login.php">';
// Wenn Benutzer nicht das Recht zum Erstellen von Themen hat
} elseif (!isset($_SESSION['recht_t_erstellen'])) {
    echo "Sie haben leider keine Berechtigung zum Erstellen von Themen!";
// Wenn der Benutzer eingeloggt ist und as Recht zum Erstelllen von Themen hat
} else {
    if (!isset($_POST['ueberschrift']) or !isset($_POST['beitrag'])
        or empty($_POST['ueberschrift']) or empty($_POST['beitrag'])) {
        $katID = $_POST['katID'];
        $erstellerID = $_SESSION['id'];

        if (!isset($_POST['katID'])) {
            echo '<meta http-equiv="refresh" content="0; URL=startseite.php">';
        } else {
            echo '<form action="" method="post">
                <label>Überschrift
                    <input type="text" id="ueberschrift" name="ueberschrift" maxlength="50" required autofocus>                  
                </label><br>
            
                <label>Beitrag
                    <textarea id="beitrag" name="beitrag" required rows="10" cols="60"></textarea>
                </label><br>
                
                <label>Thema geschlossen
                    <input type="checkbox" id="geschlossen" name="geschlossen" value="true">
                </label><br>
                
                <label>Thema angepinnt
                    <input type="checkbox" id="angepinnt" name="angepinnt" value="true">
                </label><br>
                <input type="hidden" name="kontrolle" value="absenden">
                <input type="hidden" name="katid" value="' . $katID . '">
                <input type="hidden" name="erstellerid" value="' . $erstellerID . '">
                <input type="submit" value="absenden">
            </form>';
        }
    }
    if (isset($_POST['kontrolle']) and $_POST['kontrolle'] == 'absenden') {
        $ueberschrift = trim($_POST['ueberschrift']);
        $beitrag = trim($_POST['beitrag']);
        $katID = $_POST['katid'];
        $erstellerID = $_POST['erstellerid'];
        $geschlossen = 0;
        $angepinnt = 0;

        if (isset($_POST['geschlossen'])) {
            $geschlossen = 1;
        }

        if (isset($_POST['angepinnt'])) {
            $angepinnt = 1;
        }

        $db->beginTransaction(); // Transaktionsmodus
        // Tabelle themen Datensatz einfügen
        $einfuegenThema = $db->prepare("INSERT INTO themen (Thema_geschlossen, Thema_angepinnt, ErstellerID, KatID)
                                                VALUES (:geschlossen, :angepinnt, :ersteller, :katid)");
        $einfuegenThema->bindValue(':geschlossen', $geschlossen, PDO::PARAM_INT);
        $einfuegenThema->bindValue(':angepinnt', $angepinnt, PDO::PARAM_INT);
        $einfuegenThema->bindValue(':ersteller', $erstellerID, PDO::PARAM_INT);
        $einfuegenThema->bindValue(':katid', $katID, PDO::PARAM_INT);
        $einfuegenThema->execute();

        $themenID = $db->lastInsertId();

        // Tabelle beitraege Datensatz einfügen
        $einfuegenDB = $db->prepare("INSERT INTO beitraege (Ueberschrift, Beitrag, ErstellerID, ThemenID) 
                                            VALUES (:ueberschrift, :beitrag, :ersteller, :themenid)");
        $einfuegenDB->bindValue(':ueberschrift', $ueberschrift, PDO::PARAM_STR);
        $einfuegenDB->bindValue(':beitrag', $beitrag, PDO::PARAM_STR);
        $einfuegenDB->bindValue(':ersteller', $erstellerID, PDO::PARAM_INT);
        $einfuegenDB->bindValue(':themenid', $themenID, PDO::PARAM_INT);
        $einfuegenDB->execute();

        $beitragsID = $db->lastInsertId();

        // Tabelle themen Datensatz updaten
        $updateThemen = $db->prepare("UPDATE themen 
                                            SET Erster_Beitrag = $beitragsID, Letzter_Beitrag = $beitragsID,
                                                ErstellerID = $erstellerID
                                            WHERE ThemenID = $themenID");
        $updateThemen->execute();

        // Tabelle kategorien Datensätze updaten
        $updateKategorien = $db->prepare("UPDATE kategorien 
                                                SET Anzahl_Themen = IFNULL(Anzahl_Themen, 0) + 1, 
                                                    Anzahl_Beitraege = IFNULL(Anzahl_Beitraege, 0) + 1, 
                                                    Letzter_Beitrag = $beitragsID, 
                                                    ErstellerID = $erstellerID
                                                WHERE KatID = $katID");
        $updateKategorien->execute();

        $sql = "SELECT themen.KatID, kategorien.Ebene
                FROM themen 
                LEFT JOIN kategorien ON themen.KatID = kategorien.KatID 
                WHERE ThemenID = $themenID";
        $thema = $db->query($sql);
        $erg = $thema->fetch(PDO::FETCH_ASSOC);
        $katID = $erg['KATID'];
        $katEbene = $erg['EBENE'];

        $aktuelleKatID = $katID;
        for ($i = $katEbene - 1; $i >= 0; $i--) {
            $Anfrage = $db->query("SELECT Uebergeord_KatID 
                                        FROM kategorien 
                                        WHERE KatID = $aktuelleKatID");
            $ergebnis = $Anfrage->fetch(PDO::FETCH_ASSOC);
            $aktuelleKatID = $ergebnis['UEBERGEORD_KATID'];
            $updateKategorien = $db->prepare("UPDATE kategorien 
                                                    SET Anzahl_Beitraege = IFNULL(Anzahl_Beitraege, 0) + 1, 
                                                        Letzter_Beitrag = $beitragsID,
                                                        ErstellerID = $erstellerID
                                                    WHERE KatID = $aktuelleKatID");
            $updateKategorien->execute();
        }
        $db->commit();
        echo 'Das Thema wurde erstellt!';
        echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
        echo '<button onclick="location.href=\'thema.php?themenid='.$themenID.'\'" type="button">Zum Thema</button><br><br>';
    }
}
?>
</body>
