<html>
<head>
    <link rel="stylesheet" href="KategorieBearbeiten.css">
</head>
<body>
<?php
require_once 'header.php';
// Aufrufen des Formulars zum Bearbeiten eines Themas
if (isset($_SESSION['recht_t_bearbeiten']) AND !isset($_POST['kontrolle']) AND isset($_POST['themenid'])) { // Rechte prüfen
    $themenID = $_POST['themenid'];
    $sql = "SELECT Erster_Beitrag, b.Ueberschrift, b.Beitrag, Thema_geschlossen, Thema_angepinnt
            FROM themen
            LEFT JOIN beitraege b on themen.Erster_Beitrag = b.BeitragsID
            WHERE themen.ThemenID = $themenID";
    $themaHolen = $db->query($sql);
    $erg = $themaHolen->fetch(PDO::FETCH_ASSOC);
    ?>
    <form action="" method="post">
        <label>Überschrift
            <input type="text" id="ueberschrift" name="ueberschrift" value="<?php echo $erg['UEBERSCHRIFT']?>" maxlength="50" required autofocus>
        </label>
        <br>
        <label>Beitrag
            <textarea id="beitrag" name="beitrag" required rows="10" cols="60"><?php echo $erg['BEITRAG']?></textarea>
        </label>
        <br>
        <label>Thema geschlossen
            <input type="checkbox" id="geschlossen" name="geschlossen" value="true">
        </label>
        <br>
        <label>Thema angepinnt
            <input type="checkbox" id="angepinnt" name="angepinnt" value="true">
        </label>
        <br>
        <label>Grund
            <textarea id="grund" name="grund"  required rows="10" cols="60"></textarea>
        </label>
        <br>
        <input type="hidden" name="kontrolle" value="bestaetigen">
        <input type="hidden" name="bearbeiterid" value="<?php echo $_SESSION['id']?>">
        <input type="hidden" name="beitragsid" value="<?php echo $erg['ERSTER_BEITRAG']?>">
        <input type="hidden" name="themenid" value="<?php echo $themenID?>">
        <input type="submit" value="absenden">
    </form>
    <?php
// Bearbeiten des Themas nach Absenden des Formulars
} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_SESSION['recht_t_bearbeiten'])) {
    $ueberschrift = $_POST['ueberschrift'];
    $beitrag = $_POST['beitrag'];
    $geschlossen = 0;
    $angepinnt = 0;
    $grund = $_POST['grund'];
    $bearbeiterID = $_POST['bearbeiterid'];
    $beitragsID = $_POST['beitragsid'];
    $themenID = $_POST['themenid'];

    if (isset($_POST['geschlossen'])) {
        $geschlossen = 1;
    }

    if (isset($_POST['angepinnt'])) {
        $angepinnt = 1;
    }

    $db->beginTransaction(); // Transaktionsmodus

    // Tabelle bearbeitungen Datensatz einfügen
    $einfuegenBearbDB = $db->prepare("INSERT INTO bearbeitungen (Grund, BenutzerID) 
                                                VALUE (:grund, :bearbeiter)");
    $einfuegenBearbDB->bindValue(':grund', $grund, PDO::PARAM_STR);
    $einfuegenBearbDB->bindValue(':bearbeiter', $bearbeiterID, PDO::PARAM_INT);
    $einfuegenBearbDB->execute();
    $bearbID = $db->lastInsertId();

    // Tabelle beitraege Datensatz updaten
    $bearbeiteBeitrag = $db->prepare("UPDATE beitraege
                                                SET Ueberschrift = \"$ueberschrift\", Beitrag = \"$beitrag\", BearbID = $bearbID                                 
                                                WHERE BeitragsID = $beitragsID");
    $bearbeiteBeitrag->execute();

    // Tabelle themen Datensatz updaten
    $bearbeiteThema = $db->prepare("UPDATE themen
                                                SET Thema_geschlossen = $geschlossen, Thema_angepinnt = $angepinnt                                  
                                                WHERE ThemenID = $themenID");
    $bearbeiteThema->execute();

    $db->commit();

    echo 'Das Thema wurde bearbeitet!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'thema.php?themenid='.$themenID.'\'" type="button">Zum Thema</button><br><br>';

} elseif (!isset($_SESSION['recht_t_bearbeiten'])) { // Wenn Benutzer keine Rechte hat
    echo "Sie haben leider keine Berechtigung zum Bearbeiten von Themen!";
} elseif (!isset($_SESSION['id'])) { // Wenn Benutzer nicht eingeloggt
    echo "Sie sind nicht eingeloggt.";
    echo '<br><button onclick="location.href=\'Login.php\'" type="button">Zum Login</button><br><br>';
} else { // Kein Thema übergeben
    echo '<meta http-equiv="refresh" content="0; URL=Startseite.php">';
}
?>
</body>
</html>