<html>
<head>
<link rel="stylesheet" href="Thema.css">
</head>
<body>
<?php
require_once 'header.php';
if (isset($_GET['themenid'])) {
    $themenID = $_GET['themenid'];
    $sql = "SELECT Thema_Geschlossen, themen.KatID, Ebene
            FROM themen
            LEFT JOIN kategorien k on themen.KatID = k.KatID
            WHERE themen.ThemenID = $themenID";
    $themaGeschl = $db->query($sql);
    $erg = $themaGeschl->fetch(PDO::FETCH_ASSOC);

    echo '<br><button onclick="location.href=\'Kategorie.php?katid=' . $erg['KATID'] . '&ebene=' . $erg['EBENE'] . '\'" type="button">Zurück zur Kategorie</button><br>';
    // Wenn Thema öffentlich oder Benutzer Rechte hat
    if ($erg['THEMA_GESCHLOSSEN'] == 0 OR isset($_SESSION['recht_t_lesen'])) {
        // Ansichtenzähler hochsetzen
        $db->beginTransaction(); // Transaktionsmodus
        $bearbeiteThema = $db->prepare("UPDATE themen
                                                SET Anzahl_Ansichten = IFNULL(Anzahl_Ansichten, 0) + 1                                  
                                                WHERE ThemenID = $themenID");
        $bearbeiteThema->execute();
        $db->commit();
        // Beiträge anzeigen
        $sql = "SELECT beitraege.ThemenID, Ueberschrift, Beitrag, Erstellungsdatum, Benutzername, BeitragsID, BearbID, BenutzerID, Thema_geschlossen
                FROM beitraege 
                LEFT JOIN benutzer ON beitraege.ErstellerID = benutzer.BenutzerID
                LEFT JOIN themen t on beitraege.ThemenID = t.ThemenID
                WHERE beitraege.ThemenID = $themenID";
        $beitraege = $db->query($sql);
        $benutzerID = 0;
        if (isset($_SESSION['id'])) {
            $benutzerID = $_SESSION['id'];
        }
        $zaehlerBeitraege = 0;
        $geschlossen = 0;

        if ($beitraege->rowCount() > 0) {
            ?>
            <table>
                <colgroup id="row1">
                    <col id="row2">
                    <col id="row3">
                </colgroup>
                <tbody>
                <?php
                while ($row = $beitraege->fetch(PDO::FETCH_ASSOC)) {
                    $zaehlerBeitraege += 1;
                    if ($row['THEMA_GESCHLOSSEN'] == 1) {
                        $geschlossen = 1;
                    }
                    echo '<tr><td><b>Beitragsersteller</b></td>
                        <td><b>'.$row['UEBERSCHRIFT'].'</b></td>
                        <td><b>'.$row['ERSTELLUNGSDATUM'].'</b></td></tr>
                       <tr><td><a href="BenutzerAnzeigen.php?name='.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</a></td>
                        <td>'.$row['BEITRAG'].'';
                    if ($row['BEARBID'] != null) { // Prüfen, ob es Bearbeitungen zum Beitrag gibt.
                        $bearbID = $row['BEARBID'];
                        $sql = "SELECT Datum, Grund, Benutzername
                            FROM bearbeitungen 
                            LEFT JOIN benutzer ON bearbeitungen.BenutzerID = benutzer.BenutzerID 
                            WHERE BearbID = $bearbID";
                        $bearbeitung = $db->query($sql);
                        $bearb = $bearbeitung->fetch(PDO::FETCH_ASSOC);
                        echo '<br><br><i> Dieser Beitrag wurde von '.$bearb['BENUTZERNAME'].' am '.$bearb['DATUM'].' bearbeitet. <br>
                            Grund: '.$bearb['GRUND'].'</i>';
                    }
                    echo '</td>
                        <td>';
                    if (isset($_SESSION['recht_b_erstellen']) AND $geschlossen == 0) {
                        echo '<form action="BeitragErstellen.php" method="post">
                                <input type="hidden" name="themenid" value="' . $row['THEMENID'] . '">
                                <input type="hidden" name="beitragsid" value="' . $row['BEITRAGSID'] . '">
                                <input type="Submit" value="Auf diesen Beitrag antworten">
                            </form>';
                    }
                    if ((($row['BENUTZERID'] == $benutzerID AND isset($_SESSION['recht_t_bearbeiten'])) OR isset($_SESSION['recht_t_bearbeiten_a'])) AND $zaehlerBeitraege == 1) {
                        echo '<form action="ThemaBearbeiten.php" method="post">                                
                            <input type="hidden" name="beitragsid" value="'.$row['BEITRAGSID'].'">
                            <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                            <input type="Submit" value="Thema bearbeiten">
                        </form>';
                    }
                    if ((($row['BENUTZERID'] == $benutzerID AND isset($_SESSION['recht_b_bearbeiten'])) OR isset($_SESSION['recht_b_bearbeiten_a'])) AND $zaehlerBeitraege != 1  AND $geschlossen == 0) {
                        echo '<form action="BeitragBearbeiten.php" method="post">                                
                            <input type="hidden" name="beitragsid" value="'.$row['BEITRAGSID'].'">
                            <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                            <input type="Submit" value="Beitrag bearbeiten">
                        </form>';
                    }
                    if ((($row['BENUTZERID'] == $benutzerID AND isset($_SESSION['recht_t_loeschen'])) OR isset($_SESSION['recht_t_loeschen_a'])) AND $zaehlerBeitraege == 1  AND $geschlossen == 0) {
                        echo '<form action="ThemaLoeschen.php" method="post">                                
                                <input type="hidden" name="beitragsid" value="'.$row['BEITRAGSID'].'">
                                <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                                <input type="Submit" value="Thema löschen">
                            </form>';
                    }
                    if ((($row['BENUTZERID'] == $benutzerID AND isset($_SESSION['recht_b_loeschen'])) OR isset($_SESSION['recht_b_loeschen_a'])) AND $zaehlerBeitraege != 1  AND $geschlossen == 0) {
                        echo '<form action="BeitragLoeschen.php" method="post">                                
                                <input type="hidden" name="beitragsid" value="'.$row['BEITRAGSID'].'">
                                <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                                <input type="Submit" value="Beitrag löschen">
                            </form>';
                    }
                    echo '</td></tr>';
                }
                ?>
                </tbody>
            </table>

            <?php
            if ($geschlossen == 0 AND isset($_SESSION['recht_b_erstellen'])) {
                echo '<form action="BeitragErstellen.php" method="post">
                    <input type="hidden" name="themenid" value="'.$themenID.'">
                    <input type="Submit" value="Auf dieses Thema antworten">
                </form>';
            }
            if (isset($_SESSION['id']) AND isset($_SESSION['recht_t_bearbeiten_a'])) {
                echo '<form action="ThemaBearbeiten.php" method="post">
                    <input type="hidden" name="themenid" value="'.$themenID.'">
                    <input type="Submit" value="Thema bearbeiten">
                </form>';
            }
            if (isset($_SESSION['id']) AND isset($_SESSION['recht_t_loeschen'])) {
                echo '<form action="ThemaLoeschen.php" method="post">
                    <input type="hidden" name="themenid" value="'.$themenID.'">
                    <input type="Submit" value="Thema loeschen">
                </form>';
            }
        }
    } else {
        echo "Sie haben leider keine Berechtigung zum Lesen von nicht-öffentlichen Themen";
    }
} else {
    echo "Es wurden kein Thema mit dieser ID gefunden!";
}
?>
</body>
</html>


