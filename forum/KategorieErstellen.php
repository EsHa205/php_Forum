<html>
<head>
    <link rel="stylesheet" href="KategorieErstellen.css">
</head>
<body>

<?php
require_once 'header.php';
if (isset($_SESSION['recht_k_erstellen'])) { // Rechte prüfen
    if (!isset($_POST['kontrolle'])) { // Aufrufen des Formulars
        $sql = "SELECT KatID, Ebene, Name, Uebergeord_KatID 
            FROM kategorien
            ORDER BY Ebene, Uebergeord_KatID";
        $kategorien = $db->query($sql);
        ?>
        <form action="" method="post">
            <label>Überschrift
                <input type="text" id="ueberschrift" name="ueberschrift" maxlength="50" required autofocus>
            </label>
            <br>
            <br>
            <label>Übergeordnete Kategorie<br>
                <select id="kategorie" name="kategorie" size="5">
                    <option value="">Keine übergeordnete Kategorie</option>
                    <?php
                    $vorherigeEbene = 0;
                    //$vorherigeUeberKat = 0;
                    echo '<optgroup label="Oberkategorien">';
                    while ($row = $kategorien->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['EBENE'] != $vorherigeEbene /*OR $vorherigeUeberKat != $row['UEBERGEORD_KATID']*/) {
                            $vorherigeEbene = $row['EBENE'];
                            //$vorherigeUeberKat = $row['UEBERGEORD_KATID'];
                            //$vorherigerName = "Oberkategorien";
                            echo '</optgroup>';
                            echo '<optgroup label="Ebene '.$row['EBENE'].'">';
                            //echo '<optgroup label="Unterkategorien von '.$row['NAME'].'">';
                        }
                        echo '<option value="'.$row['KATID'].'">'.$row['NAME'].'</option>';
                    };
                    echo '</optgroup>';?>
                </select>
            </label>
            <br>
            <br>
            <label>Rang
                <input type="number" id="rang" min=1 name="rang" required autofocus>
            </label>
            <br>
            <br>
            <label>Kategorie öffentlich (Öffentlich nur möglich, wenn übergeordnete Kategorie öffentlich ist.)
                <input type="checkbox" id="oeffentlich" name="oeffentlich" value="true">
            </label><br>
            <input type="hidden" name="kontrolle" value="absenden">
            <input type="submit" value="erstellen">
        </form>
        <?php
    }
    // Erstellung der Kategorie nach Absendung des Formulars
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') {
        $erstellerID = $_SESSION['id'];
        $ueberschrift = trim($_POST['ueberschrift']);
        $oeffentlich = 0;
        $rang = $_POST['rang'];
        $ebene = 0;

        if (isset($_POST['kategorie'])) {
            $katID = $_POST['kategorie'];
        }

        if (isset($_POST['oeffentlich'])) {
            $oeffentlich = 1;
        }

        if (empty($katID)) { // Wenn keine übergeordnete Kategorie ausgewählt wurde
            $db->beginTransaction(); // Transaktionsmodus
            $einfuegenKategorie = $db->prepare("INSERT INTO kategorien(Name, Oeffentlich, Rang, Ebene, ErstellerID)
                                            VALUES (:name, :oeffentlich, :rang, :ebene, :ersteller)");
            $einfuegenKategorie->bindParam(':name', $ueberschrift, PDO::PARAM_STR);
            $einfuegenKategorie->bindParam(':oeffentlich', $oeffentlich, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':rang', $rang, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':ebene', $ebene, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':ersteller', $erstellerID, PDO::PARAM_INT);
            $einfuegenKategorie->execute();
        } else { // Wenn eine übergeordnete Kategorie ausgewählt wurde
            $sql = "SELECT KatID, Ebene, Oeffentlich
            FROM kategorien
            WHERE KatID = $katID";
            $kategorien = $db->query($sql);
            $row = $kategorien->fetch(PDO::FETCH_ASSOC);
            $ebene = $row['EBENE'] + 1;
            $ueberKatOeffentlich = $row['OEFFENTLICH'];
            if ($ueberKatOeffentlich == 0) {
                $oeffentlich = 0;
            }

            $db->beginTransaction(); // Transaktionsmodus
            $einfuegenKategorie = $db->prepare("INSERT INTO kategorien(Name, Oeffentlich, Rang, Ebene, Uebergeord_KatID, ErstellerID)
                                            VALUES (:name, :oeffentlich, :rang, :ebene, :ueberkat, :ersteller)");
            $einfuegenKategorie->bindParam(':name', $ueberschrift, PDO::PARAM_STR);
            $einfuegenKategorie->bindParam(':oeffentlich', $oeffentlich, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':rang', $rang, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':ebene', $ebene, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':ueberkat', $katID, PDO::PARAM_INT);
            $einfuegenKategorie->bindParam(':ersteller', $erstellerID, PDO::PARAM_INT);
            $einfuegenKategorie->execute();
        }
        $neueKat = $db->lastInsertId();
        $db->commit();

        echo 'Die Kategorie "'.$ueberschrift.'" wurde erstellt!';
        echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
        echo '<button onclick="location.href=\'kategorie.php?katid='.$neueKat.'&ebene='.$ebene.'\'" type="button">Zur neuen Kategorie</button><br><br>';
        echo '<button onclick="location.href=\'KategorieErstellen.php\'" type="button">Weitere Kategorie erstellen</button><br><br>';
        echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';
    }
} else {
    echo "Sie haben leider keine Berechtigung zum Erstellen von Kategorien!";
}
?>
</body>
</html>


