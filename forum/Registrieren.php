<html>
<head>
    <link rel="stylesheet" href="Registrieren.css">
</head>
<body>
<?php
require_once 'header.php';
// Aufrufen des Formulars zum Registrieren
if (!isset($_POST['kontrolle'])) {
    ?>
    <form action="" method="post">
        <label>Anmeldename
            <input type="text" id="login" name="login" maxlength="20" required autofocus>
        </label><br>

        <label>Passwort
            <input type="password" id="pw" name="pw" maxlength="30" required>
        </label><br>

        <label>Name
            <input type="text" id="name" name="name" maxlength="50" required>
        </label><br>

        <label>Vorname
            <input type="text" id="vorname" name="vorname" maxlength="50" required>
        </label><br>

        <label>E-Mail Adresse
            <input type="text" id="email" name="email" maxlength="100" required>
        </label><br>

        <input type="hidden" name="kontrolle" value="absenden">
        <input type="submit" value="absenden">
    </form>
    <?php
}
// Eintragen der Daten in die Datenbank nach der Registrierung
if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') {
    $login = trim($_POST['login']);
    $pepper = "j34o!0mr#4J(";
    $pw = password_hash($_POST['pw'].$pepper, PASSWORD_DEFAULT) ;
    $name = $_POST['name'];
    $vorname = $_POST['vorname'];
    $email = $_POST['email'];
    $rechte = 4;

    $sql = "SELECT Benutzername
            FROM login
            WHERE Benutzername = \"$login\"";
    $benutzernamePruefen = $db->query($sql);
    if ($row = $benutzernamePruefen->fetch(PDO::FETCH_ASSOC)) {
        echo 'Der Benutzername '. $name .' existiert bereits';
        echo "<br><button onclick=\"history.go(-1);\">Zurück</button>";
    } else {
        $db->beginTransaction(); // Transaktionsmodus
        // Tabelle login Datensatz einfügen
        $einfuegenLogin = $db->prepare("INSERT INTO login (Benutzername, Passwort) VALUES (:login, :pw)");
        $einfuegenLogin->bindValue(':login', $login, PDO::PARAM_STR);
        $einfuegenLogin->bindValue(':pw', $pw, PDO::PARAM_STR);
        $einfuegenLogin->execute();

        $lastloginID = $db->lastInsertId();

        // Tabelle benutzer Datensatz erstellen
        $einfuegenBenutzer = $db->prepare("INSERT INTO benutzer (Nachname, Vorname, E_Mail, Benutzername, RechtID)
                                                VALUES (:name, :vorname, :email, :login, :rechte)");
        $einfuegenBenutzer->bindValue(':name', $name, PDO::PARAM_STR);
        $einfuegenBenutzer->bindValue(':vorname', $vorname, PDO::PARAM_STR);
        $einfuegenBenutzer->bindValue(':email', $email, PDO::PARAM_STR);
        $einfuegenBenutzer->bindValue(':login', $login, PDO::PARAM_STR);
        $einfuegenBenutzer->bindValue(':rechte', $rechte, PDO::PARAM_INT);
        $einfuegenBenutzer->execute();

        $db->commit();
        echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
        echo '<button onclick="location.href=\'benutzerAnzeigen.php?name='.$name.'\'" type="button">Benutzerdaten anzeigen</button><br><br>';
        echo '<button onclick="location.href=\'Einstellungen.php\'" type="button">Einstellungen</button><br><br>';
    }
}
?>
</body>
