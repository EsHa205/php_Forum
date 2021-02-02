<html>
<head>
    <link rel="stylesheet" href="KategorieBearbeiten.css">
</head>
<body>
<?php
require_once 'header.php';
// Aufrufen des Formulars zum Erstellen von Rechten und Rechte prüfen
if (isset($_SESSION['recht_r_bearbeiten']) AND !isset($_POST['kontrolle'])) {
    ?>
    <br>
    <form action="" method="post">
        <label>Rechtegruppe
            <input type="text" id="rechtgruppe" name="rechtgruppe" maxlength="50" required autofocus>
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
        <input type="hidden" name="kontrolle" value="bestaetigen">
        <input type="submit" value="absenden">
    </form>
    <?php
// Erstellen der Rechtegruppe nach Absenden des Formulars
} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_SESSION['recht_r_bearbeiten'])) {
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

    // Tabelle rechte Datensatz einfügen
    $einfuegenRechte = $db->prepare("INSERT INTO rechte(Rechtegruppenname, 
                                            Beitrag_lesen, Beitrag_erstellen, Beitrag_bearbeiten, Beitrag_bearbeiten_alle, 
                                            Beitrag_loeschen, Beitrag_loeschen_alle, Thema_lesen, Thema_erstellen, 
                                            Thema_bearbeiten, Thema_bearbeiten_alle, Thema_loeschen, Thema_loeschen_alle, 
                                            Themen_schliessen, Themen_anpinnen, Kategorie_lesen, Kategorie_erstellen, 
                                            Kategorie_bearbeiten, Kategorie_loeschen, Benutzer_anzeigen, Benutzer_erstellen, 
                                            Benutzer_bearbeiten_alle, Benutzer_loeschen_alle, Rechte_bearbeiten)
                                            VALUES (:rechtgruppe, :b_lesen, :b_erstellen, :b_bearbeiten, :b_bearbeiten_a, 
                                                    :b_loeschen, :b_loeschen_a, :t_lesen, :t_erstellen, :t_bearbeiten, :t_bearbeiten_a, 
                                                    :t_loeschen, :t_loeschen_a, :t_schliessen, :t_anpinnen, :k_lesen, 
                                                    :k_erstellen, :k_bearbeiten, :k_loeschen, :be_anzeigen, :be_erstellen, 
                                                    :be_bearbeiten, :be_loeschen, :r_bearbeiten, :b_bearbeiten_a, :b_bearbeiten_a)");
    $einfuegenRechte->bindParam(':rechtgruppe', $rechtGruppenName, PDO::PARAM_STR);
    $einfuegenRechte->bindParam(':b_lesen', $bLesen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':b_erstellen', $bErstellen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':b_bearbeiten', $bBearbeiten, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':b_bearbeiten_a', $bBearbeitenA, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':b_loeschen', $bLoeschen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':b_loeschen_a', $bLoeschenA, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_lesen', $tLesen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_erstellen', $tErstellen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_bearbeiten', $tBearbeiten, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_bearbeiten_a', $tBearbeitenA, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_loeschen', $tLoeschen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_loeschen_a', $tLoeschenA, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_schliessen', $tSchliessen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':t_anpinnen', $tAnpinnen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':k_lesen', $kLesen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':k_erstellen', $kErstellen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':k_bearbeiten', $kBearbeiten, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':k_loeschen', $kLoeschen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':be_anzeigen', $beAnzeigen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':be_erstellen', $beErstellen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':be_bearbeiten', $beBearbeiten, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':be_loeschen', $beLoeschen, PDO::PARAM_INT);
    $einfuegenRechte->bindParam(':r_bearbeiten', $rBearbeiten, PDO::PARAM_INT);
    $einfuegenRechte->execute();
    $db->commit();

    echo 'Die Rechtegruppe wurde erstellt!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'RechteErstellen.php\'" type="button">Weiteren Rechtegruppe erstellen</button><br><br>';
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