<html>
<head>
    <link rel="stylesheet" href="BenutzerErstellen.css">
</head>
<body>
<?php
require_once 'header.php';
if (!isset($_SESSION['recht_be_erstellen']) OR !isset($_SESSION['id'])) { // Rechte prüfen
    echo "Sie haben leider keine Berechtigung zum Erstellen von Benutzern!";
} else {  // Wenn Benutzer eingeloggt ist und die Rechte besitzt
    if (!isset($_POST['kontrolle'])) { // Aufrufen des Formulars
        $sql = "SELECT RechtID, Rechtegruppenname
            FROM rechte";
        $rechte = $db->query($sql);
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

            <label>Rechte<br>
                <select id="rechte" name="rechte" size="5">
                    <?php
                    while ($row = $rechte->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="'.$row['RECHTID'].'">'.$row['RECHTEGRUPPENNAME'].'</option>';
                    };
                    ?>
                </select>
            </label>

            <input type="hidden" name="kontrolle" value="absenden">
            <input type="submit" value="absenden">
        </form>
        <?php
    }
    // Erstellung des Benutzers nach Absenden des Formulars
    if (isset($_POST['kontrolle']) AND $_POST['kontrolle']=='absenden') {
        $login = trim($_POST['login']);
        $pepper = "j34o!0mr#4J(";
        $pw = password_hash($_POST['pw'].$pepper, PASSWORD_DEFAULT);
        $name = $_POST['name'];
        $vorname = $_POST['vorname'];
        $email = $_POST['email'];
        $rechte = 4;
        if (isset($_POST['rechte'])) {
            $rechte = $_POST['rechte'];
        }

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
            echo 'Der Benutzer "'.$name.'" wurde erstellt.!';
            echo '<br><button onclick="location.href=\'Startseite.php\'" type="button">Zur Startseite</button><br><br>';
            echo '<button onclick="location.href=\'benutzerAnzeigen.php?name='.$login.'\'" type="button">Zum neuen Benutzer</button><br><br>';
            echo '<button onclick="location.href=\'BenutzerErstellen.php\'" type="button">Weiteren Benutzer erstellen</button><br><br>';
            echo '<button onclick="location.href=\'Datenverwaltung.php\'" type="button">Zur Datenverwaltung</button><br><br>';
        }
    }
}
?>
</body>
</html>