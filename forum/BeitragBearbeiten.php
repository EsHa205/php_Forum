<html>
<head>
    <link rel="stylesheet" href="BeitragBearbeiten.css">
</head>
<body>
<?php

require_once 'header.php';
if (isset($_POST['beitragsid']) AND isset($_SESSION['id']) AND !isset($_POST['kontrolle'])) { // Aufrufen des Formulars
    $bearbeiterID = $_SESSION['id'];
    $beitragsID = $_POST['beitragsid'];
    $sql = "SELECT Ueberschrift, Beitrag, ErstellerID, ThemenID, BearbID
                FROM beitraege 
                WHERE BeitragsID = $beitragsID";
    $beitragHolen = $db->query($sql);
    $erg = $beitragHolen->fetch(PDO::FETCH_ASSOC);
    $ueberschrift = $erg['UEBERSCHRIFT'];
    $beitrag = $erg['BEITRAG'];
    $erstellerID = $erg['ERSTELLERID'];
    $themenID = $erg['THEMENID'];
    $bearbID = $erg['BEARBID'];

    $sql = "SELECT Thema_geschlossen
            FROM themen
            WHERE ThemenID = $themenID";
    $themaStatusGeschlossen = $db->query($sql);
    $erg = $themaStatusGeschlossen->fetch(PDO::FETCH_ASSOC);
    if ($erg['THEMA_GESCHLOSSEN'] == 1) {
        if (isset($_SESSION['recht_b_bearbeiten_a']) OR // Alle Beiträge bearbeiten mit entsprechendem Recht
            (isset($_SESSION['recht_b_bearbeiten']) AND $bearbeiterID == $erstellerID)) {  // oder eigenen Beitrag bearbeiten
            echo '<form action="" method="post">
                <label>Überschrift
                    <input type="text" id="ueberschrift" name="ueberschrift" value="'.$ueberschrift.'" maxlength="50" required autofocus>                  
                </label><br>
            
                <label>Beitrag
                    <textarea id="beitrag" name="beitrag" required rows="10" cols="60">'.$beitrag.'</textarea>
                </label><br>
                
                <label>Grund
                    <textarea id="grund" name="grund"  required rows="10" cols="60"></textarea>
                </label><br>
                
                <input type="hidden" name="kontrolle" value="absenden">
                <input type="hidden" name="bearbeiterid" value="'.$bearbeiterID.'">
                <input type="hidden" name="beitragsid" value="'.$beitragsID.'">
                <input type="hidden" name="themenid" value="'.$themenID.'">
                <input type="hidden" name="bearbid" value="'.$bearbID.'">
                <input type="submit" value="absenden">
            </form>';
        } else {
            echo "Sie haben keine Berechtigung zum Bearbeiten des Beitrags";
            echo '<br><button onclick="location.href=\'Login.php\'" type="button">Zum Login</button><br><br>';
        }
    } else {
        echo "Das Thema ist bereits geschlossen. Sie können keine Beiträge bearbeiten.";
    }

} elseif (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') { // Bearbeiten des Beitrags nach Absenden des Formulars
    $ueberschrift = trim($_POST['ueberschrift']);
    $beitrag = trim($_POST['beitrag']);
    $bearbeiterID = $_POST['bearbeiterid'];
    $grund = $_POST['grund'];
    $beitragsID = $_POST['beitragsid'];
    $themenID = $_POST['themenid'];
    $bearbID = $_POST['bearbid'];

    $db->beginTransaction(); // Transaktionsmodus

    if (!empty($bearbID)) {
        // Tabelle bearbeitungen Datensatz updaten
        $updateBearbeitung = $db->prepare("UPDATE bearbeitungen 
                                            SET Grund = \"$grund\", BenutzerID = $bearbeiterID  
                                            WHERE BearbID = $bearbID");
        $updateBearbeitung->execute();
    } else {
        // Tabelle bearbeitungen Datensatz einfügen
        $einfuegenBearbDB = $db->prepare("INSERT INTO bearbeitungen (Grund, BenutzerID) 
                                                VALUE (:grund, :bearbeiter)");
        $einfuegenBearbDB->bindValue(':grund', $grund, PDO::PARAM_STR);
        $einfuegenBearbDB->bindValue(':bearbeiter', $bearbeiterID, PDO::PARAM_INT);
        $einfuegenBearbDB->execute();
        $bearbID = $db->lastInsertId();
    }

    // Tabelle beitraege Datensatz bearbeiten
    $updateBeitrag = $db->prepare("UPDATE beitraege 
                                            SET Ueberschrift = \"$ueberschrift\", Beitrag = \"$beitrag\", BearbID = $bearbID  
                                            WHERE BeitragsID = $beitragsID");
    $updateBeitrag->execute();
    $db->commit();

    echo 'Der Beitrag wurde bearbeitet!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'thema.php?themenid='.$themenID.'\'" type="button">Zum Thema</button><br><br>';
} elseif (!isset($_SESSION['id'])) { // Wenn Benutzer nicht eingeloggt
    echo "Sie sind nicht eingeloggt.";
    echo '<br><button onclick="location.href=\'Login.php\'" type="button">Zum Login</button><br><br>';
} else { // Kein Beitrag übergeben
    echo '<meta http-equiv="refresh" content="0; URL=Startseite.php">';
}
?>
</body>
