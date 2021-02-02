<html>
<head>
<link rel="stylesheet" href="Kategorie.css">
</head>
<body>
<?php
require_once 'header.php';
if (isset($_GET['katid']) AND isset($_GET['ebene'])) {
    $katID = $_GET['katid'];
    $ebene = $_GET['ebene'];
    if ($ebene >= 1) {
        $sql = "SELECT k.Oeffentlich, k.Uebergeord_KatID, ka.Ebene
            FROM kategorien k
            LEFT JOIN kategorien ka ON k.Uebergeord_KatID = ka.KatID
            WHERE k.KatID = $katID";
    } else {
        $sql = "SELECT Oeffentlich
            FROM kategorien 
            WHERE KatID = $katID";
    }
    // Prüfen, ob Kategorie vorhanden und öffentlich zugänglich ist
    $kategoriePruefung = $db->query($sql);
    if ($row = $kategoriePruefung->fetch(PDO::FETCH_ASSOC)) { // Kategorie wurde gefunden
        if ($ebene >= 1) {
            echo '<br><button onclick="location.href=\'Kategorie.php?katid=' . $row['UEBERGEORD_KATID'] . '&ebene=' . $row['EBENE'] . '\'" type="button">Zurück zur Kategorie</button><br>';
        }
        $oeffentlich = $row['OEFFENTLICH'];
        if ($oeffentlich == 1 OR (isset($_SESSION['recht_k_lesen']) AND isset($_SESSION['id']))) { // Kategorie ist öffentlich oder Benutzer hat das Recht

            // Kategorien anzeigen
            if (isset($_SESSION['recht_k_lesen'])) {
                $sql = "SELECT kategorien.KatID, kategorien.Ebene, kategorien.Name, kategorien.Anzahl_Themen, kategorien.Anzahl_Beitraege, 
                        b.Ueberschrift, b.Erstellungsdatum, b.ThemenID, be.Benutzername
                        FROM kategorien 
                        LEFT JOIN beitraege b ON kategorien.letzter_beitrag = b.BeitragsID
                        LEFT JOIN benutzer be on b.ErstellerID = be.BenutzerID
                        WHERE kategorien.Uebergeord_KatID = $katID AND kategorien.Ebene = $ebene + 1
                        ORDER BY kategorien.Rang";
            } else {
                $sql = "SELECT kategorien.KatID, kategorien.Ebene, kategorien.Name, kategorien.Anzahl_Themen, kategorien.Anzahl_Beitraege, 
                        b.Ueberschrift, b.Erstellungsdatum, b.ThemenID, be.Benutzername
                        FROM kategorien 
                        LEFT JOIN beitraege b ON kategorien.letzter_beitrag = b.BeitragsID
                        LEFT JOIN benutzer be on b.ErstellerID = be.BenutzerID
                        WHERE kategorien.Uebergeord_KatID = $katID AND kategorien.Ebene = $ebene + 1 AND kategorien.Oeffentlich = 1
                        ORDER BY kategorien.Rang";
            }

            $kategorien = $db->query($sql);
            if ($kategorien->rowCount() > 0) {
                ?>
                <table>
                    <colgroup id="AlleSpalten">
                        <col id="Kategorie">
                        <col id="AnzahlThemen">
                        <col id="AnzahlBeitraege">
                        <col id="LetzterBeitrag">
                        <col id="Beitragsersteller">
                        <col id="Erstellungsdatum">
                        <col id="Admin">
                    </colgroup>
                    <thead>
                    <td>Kategorie</td>
                    <td>Themen</td>
                    <td>Beiträge</td>
                    <td>Letzter Beitrag</td>
                    <td>Beitragsersteller</td>
                    <td>Erstellt am</td>
                    <?php
                    if (isset($_SESSION['recht_k_bearbeiten']) AND isset($_SESSION['id'])) {
                        echo '<td>Admin</td>';
                    }
                    ?>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $kategorien->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr><td><a href="Kategorie.php?katid='.$row['KATID'].'&ebene='.$row['EBENE'].'">'.$row['NAME'].'</a></td>
                                <td>'.$row['ANZAHL_THEMEN'].'</td>
                                <td>'.$row['ANZAHL_BEITRAEGE'].'</td>
                                <td><a href="Thema.php?themenid='.$row['THEMENID'].'">'.$row['UEBERSCHRIFT'].'</a></td>
                                <td><a href="BenutzerAnzeigen.php?name='.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</a></td>
                                <td>'.$row['ERSTELLUNGSDATUM'].'</td>';
                        if (isset($_SESSION['recht_k_bearbeiten']) AND isset($_SESSION['id'])) {
                            echo '<td><form action="KategorieBearbeiten.php" method="post">                                
                                <input type="hidden" name="katid" value="'.$row['KATID'].'">
                                <input type="Submit" value="Kategorie bearbeiten">
                            </form></td></tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <br>
                <?php
            } else {
                echo "Keine Unterkategorien vorhanden!";
            }
            $sql = "Select themen.ThemenID
            From themen 
            WHERE themen.KatID = $katID";
            $themen = $db->query($sql);
            if ($themen->rowCount() > 0) {
                // Angepinnte Themen anzeigen
                ?>
                <table>
                    <thead>
                    <td>Thema</td>
                    <td>Themenersteller</td>
                    <td>Erstellt am</td>
                    <td>Antworten</td>
                    <td>Ansichten</td>
                    <td>Letzter Beitrag</td>
                    <td>Geantwortet am</td>
                    <?php
                    if (isset($_SESSION['recht_t_bearbeiten_a']) AND isset($_SESSION['id'])) {
                        echo '<td>Admin</td>';
                    }
                    ?>
                    </thead>
                    <tbody>
                    <?php
                    echo '<tr><td>Angepinnte Themen</td>
                                    <td> </a></td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>';
                    // Angepinnte Themen anzeigen
                    $sql = "Select themen.ThemenID, a.Ueberschrift AS 'ErsterBeitrag', 
                        a.Erstellungsdatum AS 'ErstelltAm', themen.Anzahl_Antworten, themen.Anzahl_Ansichten, 
                        b.Ueberschrift AS 'LetzterBeitrag', benutzer.Benutzername, b.Erstellungsdatum AS 'GeantwortetAm'
                        From themen 
                        LEFT JOIN beitraege a ON themen.Erster_Beitrag = a.BeitragsID 
                        LEFT JOIN beitraege b ON themen.Letzter_Beitrag = b.BeitragsID
                        LEFT JOIN benutzer ON themen.ErstellerID = benutzer.BenutzerID
                        WHERE themen.KatID = $katID AND themen.Thema_angepinnt = 1
                        ORDER BY b.Erstellungsdatum desc";
                    $themen = $db->query($sql);
                    while ($row = $themen->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr><td><a href="Thema.php?themenid='.$row['THEMENID'].'">'.$row['ERSTERBEITRAG'].'</a></td>
                                    <td><a href="BenutzerAnzeigen.php?name='.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</a></td>
                                    <td>'.$row['ERSTELLTAM'].'</td>
                                    <td>'.$row['ANZAHL_ANTWORTEN'].'</td>
                                    <td>'.$row['ANZAHL_ANSICHTEN'].'</td>
                                    <td>'.$row['LETZTERBEITRAG'].'</td>
                                    <td>'.$row['GEANTWORTETAM'].'</td>';
                        if (isset($_SESSION['recht_t_bearbeiten_a']) AND isset($_SESSION['id'])) {
                            echo '<td><form action="ThemaBearbeiten.php" method="post">                                
                                <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                                <input type="Submit" value="Thema bearbeiten">
                            </form></td></tr>';
                        }
                    }
                    echo '<tr><td>Normale Themen</td>
                                    <td> </a></td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>';
                    // Andere Themen anzeigen
                    $sql = "Select themen.ThemenID, a.Ueberschrift AS 'ErsterBeitrag', 
                        a.Erstellungsdatum AS 'ErstelltAm', themen.Anzahl_Antworten, themen.Anzahl_Ansichten, 
                        b.Ueberschrift AS 'LetzterBeitrag', benutzer.Benutzername, b.Erstellungsdatum AS 'GeantwortetAm'
                        From themen 
                        LEFT JOIN beitraege a ON themen.Erster_Beitrag = a.BeitragsID 
                        LEFT JOIN beitraege b ON themen.Letzter_Beitrag = b.BeitragsID
                        LEFT JOIN benutzer ON themen.ErstellerID = benutzer.BenutzerID
                        WHERE themen.KatID = $katID AND themen.Thema_angepinnt != 1
                        ORDER BY b.Erstellungsdatum desc";
                    $themen = $db->query($sql);
                    while ($row = $themen->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr><td><a href="Thema.php?themenid='.$row['THEMENID'].'">'.$row['ERSTERBEITRAG'].'</a></td>
                                    <td><a href="BenutzerAnzeigen.php?name='.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</a></td>
                                    <td>'.$row['ERSTELLTAM'].'</td>
                                    <td>'.$row['ANZAHL_ANTWORTEN'].'</td>
                                    <td>'.$row['ANZAHL_ANSICHTEN'].'</td>
                                    <td>'.$row['LETZTERBEITRAG'].'</td>
                                    <td>'.$row['GEANTWORTETAM'].'</td>';
                        if (isset($_SESSION['recht_t_bearbeiten_a']) AND isset($_SESSION['id'])) {
                            echo '<td><form action="ThemaBearbeiten.php" method="post">                                
                                <input type="hidden" name="themenid" value="'.$row['THEMENID'].'">
                                <input type="Submit" value="Thema bearbeiten">
                            </form></td></tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<br>Keine Themen vorhanden!";
            }
            if (isset($_SESSION['recht_t_erstellen'])) {
                echo '<form action="ThemaErstellen.php" method="post">
                    <input type="hidden" name="katID" value="'.$katID.'">
                    <input type="Submit" value="Neues Thema erstellen">
                </form>';
            }
        } else {
            echo "Sie haben nicht das Recht diese Kategorie anzuzeigen";
        }
    } else {
        echo "Es wurden keine Kategorien gefunden!";
    }
} else {
    echo "Ungültiger Link!";
}

?>
</body>
</html>


