<html>
<head>
    <link rel="stylesheet" href="BenutzerAnzeigen.css">
</head>
<body>
<?php
require_once 'header.php';
if (!isset($_GET['name'])) {
    echo "Es wurde kein Benutzer übergeben!";
} elseif (!isset($_SESSION['id'])) {
    echo '<meta http-equiv="refresh" content="0; URL=login.php">';
} elseif ($_SESSION['name'] == $_GET['name'] OR isset($_SESSION['recht_be_anzeigen'])) {
    $name = $_GET['name'];
    $sql = "SELECT Nachname, Vorname, Registrierungsdatum, Benutzername, E_Mail, Rechtegruppenname
        FROM benutzer
        LEFT JOIN rechte r on benutzer.RechtID = r.RechtID
        WHERE Benutzername = \"$name\"";
    $benutzer = $db->query($sql);
    if ($row = $benutzer->fetch(PDO::FETCH_ASSOC)) {
        echo 'Benutzername: '.$row['BENUTZERNAME'].'<br>
       Name: '.$row['NACHNAME'].'<br>
       Vorname: '.$row['VORNAME'].'<br>
       E-Mail: '.$row['E_MAIL'].'<br>
       Registrierungsdatum: '.$row['REGISTRIERUNGSDATUM'].'<br>
       Gruppe: '.$row['RECHTEGRUPPENNAME'];
    } else {
        echo "Der Benutzer existiert nicht!";
    }

} elseif ($_SESSION['name'] != $_GET['name'] AND !isset($_SESSION['recht_be_anzeigen'])) {
    echo "Sie haben leider keine Berechtigung zum vollständigen Anzeigen von fremden Benutzern!<br>";
    $name = $_GET['name'];
    $sql = "SELECT Registrierungsdatum, Benutzername
        FROM benutzer
        WHERE Benutzername = \"$name\"";
    $benutzer = $db->query($sql);
    $row = $benutzer->fetch(PDO::FETCH_ASSOC);

    echo 'Benutzername: '.$row['BENUTZERNAME'].'<br>
          Registrierungsdatum: '.$row['REGISTRIERUNGSDATUM'];
}
    ?>
</body>
</html>