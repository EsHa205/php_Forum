<html>
<head>
    <link rel="stylesheet" href="KategorieBearbeiten.css">
</head>
<body>
<?php
require_once 'header.php';
// Aufrufen des Formulars zum Bearbeiten von Rechten und Rechte prüfen
if (isset($_SESSION['recht_r_bearbeiten']) AND !isset($_POST['kontrolle']) AND !isset($_POST['rechtid'])) {
    $sql = "SELECT RechtID, Rechtegruppenname
            FROM rechte";
    $rechteHolen = $db->query($sql);
    ?>
    <form action="" method="post">
        <label>Zu bearbeitende Rechtegruppe auswählen<br>
            <select id="rechtid" name="rechtid" size="5">
                <?php
                while ($row = $rechteHolen->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="'.$row['RECHTID'].'">'.$row['RECHTEGRUPPENNAME'].'</option>';
                }?>
            </select>
        </label>
        <br>
        <input type="hidden" name="kontrolle" value="absenden">
        <input type="submit" value="Bearbeiten">
    </form>
    <?php
} elseif (isset($_SESSION['recht_r_bearbeiten']) AND isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden' AND isset($_POST['rechtid'])) {
    $rechtID = $_POST['rechtid'];
    $sql = "SELECT *
            FROM rechte
            WHERE RechtID = $rechtID";
    $rechtHolen = $db->query($sql);
    $erg = $rechtHolen->fetch(PDO::FETCH_ASSOC);
    ?>
    <form action="" method="post">
        <label>Rechtegruppe
            <input type="text" id="rechtgruppe" name="rechtgruppe" value="<?php echo $erg['RECHTEGRUPPENNAME']?>" maxlength="50" required autofocus>
        </label>
        <br>
        <label>Beiträge lesen
            <input type="checkbox" id="b_lesen" name="b_lesen" value="true">
        </label>
        <br>
        <label>Beiträge erstellen
            <input type="checkbox" id="b_erstellen" name="b_erstellen" value="true">
        </label>
        <br>
        <label>Eigene Beiträge bearbeiten
            <input type="checkbox" id="b_bearbeiten" name="b_bearbeiten" value="true">
        </label>
        <br>
        <label>Alle Beiträge bearbeiten
            <input type="checkbox" id="b_bearbeiten_a" name="b_bearbeiten_a" value="true">
        </label>
        <br>
        <label>Eigene Beiträge löschen
            <input type="checkbox" id="b_loeschen" name="b_loeschen" value="true">
        </label>
        <br>
        <label>Alle Beiträge löschen
            <input type="checkbox" id="b_loeschen_a" name="b_loeschen_a" value="true">
        </label>
        <br>


        <label>Themen lesen
            <input type="checkbox" id="t_lesen" name="t_lesen" value="true">
        </label>
        <br>
        <label>Themen erstellen
            <input type="checkbox" id="t_erstellen" name="t_erstellen" value="true">
        </label>
        <br>
        <label>Eigene Themen bearbeiten
            <input type="checkbox" id="t_bearbeiten" name="t_bearbeiten" value="true">
        </label>
        <br>
        <label>Alle Themen bearbeiten
            <input type="checkbox" id="t_bearbeiten_a" name="t_bearbeiten_a" value="true">
        </label>
        <br>
        <label>Eigene Themen löschen
            <input type="checkbox" id="t_loeschen" name="t_loeschen" value="true">
        </label>
        <br>
        <label>Alle Themen löschen
            <input type="checkbox" id="t_loeschen_a" name="t_loeschen_a" value="true">
        </label>
        <br>
        <label>Themen schließen
            <input type="checkbox" id="t_schliessen" name="t_schliessen" value="true">
        </label>
        <br>
        <label>Themen anpinnen
            <input type="checkbox" id="t_anpinnen" name="t_anpinnen" value="true">
        </label>
        <br>


        <label>Kategorien lesen
            <input type="checkbox" id="k_lesen" name="k_lesen" value="true">
        </label>
        <br>
        <label>Kategorien erstellen
            <input type="checkbox" id="k_erstellen" name="k_erstellen" value="true">
        </label>
        <br>
        <label>Kategorien bearbeiten
            <input type="checkbox" id="k_bearbeiten" name="k_bearbeiten" value="true">
        </label>
        <br>
        <label>Kategorien löschen
            <input type="checkbox" id="k_loeschen" name="k_loeschen" value="true">
        </label>
        <br>


        <label>Benutzer anzeigen
            <input type="checkbox" id="be_anzeigen" name="be_anzeigen" value="true">
        </label>
        <br>
        <label>Benutzer erstellen
            <input type="checkbox" id="be_erstellen" name="be_erstellen" value="true">
        </label>
        <br>
        <label>Alle Benutzer bearbeiten
            <input type="checkbox" id="be_bearbeiten" name="be_bearbeiten" value="true">
        </label>
        <br>
        <label>Alle Benutzer löschen
            <input type="checkbox" id="be_loeschen" name="be_loeschen" value="true">
        </label>
        <br>


        <label>Rechte bearbeiten
            <input type="checkbox" id="r_bearbeiten" name="r_bearbeiten" value="true">
        </label>
        <br>
        <input type="hidden" name="rechtid" value="<?php echo $rechtID?>">
        <input type="hidden" name="kontrolle" value="bestaetigen">
        <input type="submit" value="absenden">
    </form>
    <?php
// Bearbeiten des Themas nach Absenden des Formulars
} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_SESSION['recht_r_bearbeiten'])) {
    $rechtID = $_POST['rechtid'];
    $rechtGruppenName = $_POST['rechtgruppe'];
    $bLesen = $_POST['b_lesen'];
    $bErstellen = $_POST['b_erstellen'];
    $bBearbeiten = $_POST['b_bearbeiten'];
    $bBearbeitenA = $_POST['b_bearbeiten_a'];
    $bLoeschen = $_POST['b_loeschen'];
    $bLoeschenA = $_POST['b_loeschen_a'];
    $tLesen = $_POST['t_lesen'];
    $tErstellen = $_POST['t_erstellen'];
    $tBearbeiten = $_POST['t_bearbeiten'];
    $tBearbeitenA = $_POST['t_bearbeiten_a'];
    $tLoeschen = $_POST['t_loeschen'];
    $tLoeschenA = $_POST['t_loeschen_a'];
    $tSchliessen = $_POST['t_schliessen'];
    $tAnpinnen = $_POST['t_anpinnen'];
    $kLesen = $_POST['k_lesen'];
    $kErstellen = $_POST['k_erstellen'];
    $kBearbeiten = $_POST['k_bearbeiten'];
    $kLoeschen = $_POST['k_loeschen'];
    $beAnzeigen = $_POST['be_anzeigen'];
    $beErstellen = $_POST['be_erstellen'];
    $beBearbeiten = $_POST['be_bearbeiten'];
    $beLoeschen = $_POST['be_loeschen'];
    $rBearbeiten = $_POST['r_bearbeiten'];

    $db->beginTransaction(); // Transaktionsmodus

    // Tabelle rechte Datensatz updaten
    $updateRechte = $db->prepare("UPDATE rechte
                                                SET Rechtegruppenname = \"$rechtGruppenName\", Beitrag_lesen = $bLesen,
                                                Beitrag_erstellen = $bErstellen, Beitrag_bearbeiten = $bBearbeiten,
                                                Beitrag_bearbeiten_alle = $bBearbeitenA, Beitrag_loeschen = $bLoeschen,
                                                Beitrag_loeschen_alle = $bLoeschenA, Thema_lesen = $tLesen,
                                                Thema_erstellen = $tErstellen, Thema_bearbeiten = $tBearbeiten,
                                                Thema_bearbeiten_alle = $tBearbeitenA, Thema_loeschen = $tLoeschen,
                                                Thema_loeschen_alle = $tLoeschenA, Themen_schliessen = $tSchliessen,
                                                Themen_anpinnen = $tAnpinnen, Kategorie_lesen = $kLesen,
                                                Kategorie_erstellen = $kErstellen, Kategorie_bearbeiten = $kBearbeiten,
                                                Kategorie_loeschen = $kLoeschen, Benutzer_anzeigen = $beAnzeigen,
                                                Benutzer_erstellen = $beErstellen, Benutzer_bearbeiten_alle = $beBearbeiten,
                                                Benutzer_loeschen_alle = $beLoeschen, Rechte_bearbeiten = $rBearbeiten
                                                WHERE RechtID = $rechtID");
    $updateRechte->execute();
    $db->commit();

    echo 'Die Rechtegruppe wurde bearbeitet!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'RechteBearbeiten.php\'" type="button">Weiteren Rechtegruppe bearbeiten</button><br><br>';
    echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';

} elseif (!isset($_SESSION['recht_r_bearbeiten'])) { // Wenn Benutzer keine Rechte hat
    echo "Sie haben leider keine Berechtigung zum Bearbeiten von Rechten!";
} elseif (!isset($_SESSION['id'])) { // Wenn Benutzer nicht eingeloggt
    echo "Sie sind nicht eingeloggt.";
    echo '<br><button onclick="location.href=\'Login.php\'" type="button">Zum Login</button><br><br>';
} else { // Kein Thema übergeben
    echo '<meta http-equiv="refresh" content="0; URL=Startseite.php">';
}
?>
</body>
</html>