<html>
<head>
<link rel="stylesheet" href="Startseite.css">
</head>
<body>
<?php
require_once 'header.php';
// Wenn Benutzer nicht öffentliche Kategorien sehen darf
if (isset($_SESSION['recht_k_lesen'])) {
    $sql = "SELECT kategorien.KatID, kategorien.Ebene, kategorien.Name, kategorien.Anzahl_Themen, kategorien.Anzahl_Beitraege, 
        b.Ueberschrift, b.Erstellungsdatum, b.ThemenID, be.Benutzername
        FROM kategorien 
        LEFT JOIN beitraege b ON kategorien.letzter_beitrag = b.BeitragsID
        LEFT JOIN benutzer be on b.ErstellerID = be.BenutzerID
        WHERE kategorien.ebene = 0
        ORDER BY kategorien.Rang";
    $categories = $db->query($sql);
} else { // Wenn Benutzer nur öffentliche Kategorien sehen darf
    $sql = "SELECT kategorien.KatID, kategorien.Ebene, kategorien.Name, kategorien.Anzahl_Themen, kategorien.Anzahl_Beitraege, 
        b.Ueberschrift, b.Erstellungsdatum, b.ThemenID, be.Benutzername
        FROM kategorien 
        LEFT JOIN beitraege b ON kategorien.letzter_beitrag = b.BeitragsID
        LEFT JOIN benutzer be on b.ErstellerID = be.BenutzerID
        WHERE kategorien.ebene = 0 AND kategorien.Oeffentlich = 1
        ORDER BY kategorien.Rang";
    $categories = $db->query($sql);
}
?>
<table>
    <colgroup id="AlleSpalten">
        <col id="Kategorie">
        <col id="AnzahlThemen">
        <col id="AnzahlBeitraege">
        <col id="LetzterBeitrag">
        <col id="Beitragsersteller">
        <col id="Erstellungsdatum">
    </colgroup>
    <thead>
        <td>Kategorie</td>
        <td>Themen</td>
        <td>Beiträge</td>
        <td>Letzter Beitrag</td>
        <td>Beitragsersteller</td>
        <td>Erstellt am</td>
    </thead>
    <tbody>
        <?php
        while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr><td><a href="Kategorie.php?katid='.$row['KATID'].'&ebene='.$row['EBENE'].'">'.$row['NAME'].'</a></td>
                        <td>'.$row['ANZAHL_THEMEN'].'</td>
                        <td>'.$row['ANZAHL_BEITRAEGE'].'</td>
                        <td><a href="Thema.php?themenid='.$row['THEMENID'].'">'.$row['UEBERSCHRIFT'].'</a></td>
                        <td><a href="BenutzerAnzeigen.php?name='.$row['BENUTZERNAME'].'">'.$row['BENUTZERNAME'].'</a></td>
                        <td>'.$row['ERSTELLUNGSDATUM'].'</td></tr>';
        }
        ?>
    </tbody>
</table>
</body>
</html>

