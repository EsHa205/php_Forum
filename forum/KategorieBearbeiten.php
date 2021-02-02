<html>
<head>
    <link rel="stylesheet" href="KategorieBearbeiten.css">
</head>
<body>
<?php

require_once 'header.php';
// Aufrufen des Formulars zum Auswählen einer Kategorie
if (isset($_SESSION['recht_k_bearbeiten']) AND !isset($_POST['kontrolle']) AND !isset($_POST['katid'])) { // Rechte prüfen
    $sql = "SELECT KatID, Name
            FROM kategorien
            ORDER BY Ebene, Uebergeord_KatID";
    $kategorien = $db->query($sql);
    ?>
    <form action="" method="post">
        <label>Zu bearbeitende Kategorie auswählen<br>
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
        <input type="submit" value="Bearbeiten">
    </form>
    <?php
} elseif (!isset($_SESSION['recht_k_bearbeiten'])) { // Wenn Benutzer keine Rechte hat
    echo "Sie haben leider keine Berechtigung zum Bearbeiten von Kategorien!";
}

// Eintragen der Änderungen nach Auswählen der zu bearbeitenden Kategorie oder einer übergebenen Kategorie
if (((isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') OR isset($_POST['katid'])) AND isset($_SESSION['recht_k_bearbeiten'])) {
    $katID = $_POST['katid'];

    $sql = "SELECT kategorien.Name, kategorien.Rang, kategorien.Oeffentlich, kategorien.Ebene, k.Name AS ÜbergeordneteKat
            FROM kategorien
            LEFT JOIN kategorien k on k.Uebergeord_KatID = kategorien.KatID
            WHERE kategorien.KatID = $katID";
    $kategorie = $db->query($sql);
    $row = $kategorie->fetch(PDO::FETCH_ASSOC)
    ?>

    <form action="" method="post">
        <label>Kategorie <?php echo $row['NAME']?> bearbeiten:</label>
        <br>
        <br>
        <label>Überschrift
            <input type="text" id="ueberschrift" name="ueberschrift" value="<?php echo $row['NAME']?>" maxlength="50" required autofocus>
        </label>
        <br>
        <br>
        <label>Übergeordnete Kategorie<br>
            <select id="kategorie" name="kategorie" size="5">
                <option value="">Keine übergeordnete Kategorie</option>
                <?php
                $vorherigeEbene = 0;
                echo '<optgroup label="Oberkategorien">';
                $sql = "SELECT KatID, Name
                        FROM kategorien
                        ORDER BY Ebene, Uebergeord_KatID";
                $kategorien = $db->query($sql);
                while ($row2 = $kategorien->fetch(PDO::FETCH_ASSOC)) {
                    if ($row2['EBENE'] != $vorherigeEbene) {
                        $vorherigeEbene = $row['EBENE'];
                        echo '</optgroup>';
                        echo '<optgroup label="Ebene '.$row2['EBENE'].'">';
                    }
                    echo '<option value="'.$row2['KATID'].'">'.$row2['NAME'].'</option>';
                };
                echo '</optgroup>';?>
            </select>
        </label>
        <br>
        <br>
        <label>Rang
            <input type="number" id="rang" min=1 value="<?php echo $row['RANG']?>" name="rang" required autofocus>
        </label>
        <br>
        <br>
        <label>Kategorie öffentlich (Öffentlich nur möglich, wenn übergeordnete Kategorie öffentlich ist.)
            <input type="checkbox" id="oeffentlich" name="oeffentlich" value="true">
        </label>
        <br>
        <input type="hidden" name="kontrolle" value="bestaetigen">
        <input type="hidden" name="katid" value="<?php echo $katID?>">
        <input type="hidden" name="ebene" value="<?php echo $row['EBENE']?>">
        <input type="submit" value="Bearbeitungen bestätigen">
        <button onclick="location.href='BenutzerBearbeiten.php'" type="button">Abbrechen</button>
    </form>
    <?php
}

// Bearbeiten der Kategorie nach Bestätigung
if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='bestaetigen' AND isset($_SESSION['recht_k_bearbeiten'])) {
    $katID = $_POST['katid'];
    $name = $_POST['ueberschrift'];
    $oeffentlich = 0;
    $rang = $_POST['rang'];
    $ebene = $_POST['ebene'];
    if (isset($_POST['oeffentlich'])) {
        $oeffentlich = 1;
    }

    $db->beginTransaction(); // Transaktionsmodus

    $bearbeiteKat = $db->prepare("UPDATE kategorien
                                                SET Name = \"$name\", Rang = $rang, Oeffentlich = $oeffentlich                                         
                                                WHERE KatID = $katID");
    $bearbeiteKat->execute();

    if (!empty($_POST['kategorie'])) {
        $ueberKat = $_POST['kategorie'];
        $bearbeiteUeberKat = $db->prepare("UPDATE kategorien
                                                SET Uebergeord_KatID = $ueberKat                                          
                                                WHERE KatID = $katID");
        $bearbeiteUeberKat->execute();
    }
    $db->commit();

    echo 'Die Kategorie "'.$name.'" wurde geändert.!';
    echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
    echo '<button onclick="location.href=\'Kategorie.php?katid=' . $katID . '&ebene=' . $ebene . ' \'" type="button">Kategorie anzeigen</button><br><br>';
    echo '<button onclick="location.href=\'KategorieBearbeiten.php\'" type="button">Weiteren Kategorie bearbeiten</button><br><br>';
    echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';

}
?>
</body>
</html>