<?php
// Rechte und Login aus der Session löschen
session_start();
unset($_SESSION['name']);
unset($_SESSION['id']);
unset($_SESSION['recht_b_lesen']);
unset($_SESSION['recht_b_erstellen']);
unset($_SESSION['recht_b_bearbeiten']);
unset($_SESSION['recht_b_bearbeiten_a']);
unset($_SESSION['recht_b_loeschen']);
unset($_SESSION['recht_b_loeschen_a']);

unset($_SESSION['recht_t_lesen']);
unset($_SESSION['recht_t_erstellen']);
unset($_SESSION['recht_t_bearbeiten']);
unset($_SESSION['recht_t_bearbeiten_a']);
unset($_SESSION['recht_t_loeschen']);
unset($_SESSION['recht_t_loeschen_a']);
unset($_SESSION['recht_t_schließen']);
unset($_SESSION['recht_t_anpinnen']);

unset($_SESSION['recht_k_lesen']);
unset($_SESSION['recht_k_erstellen']);
unset($_SESSION['recht_k_bearbeiten']);
unset($_SESSION['recht_k_loeschen']);

unset($_SESSION['recht_be_anzeigen']);
unset($_SESSION['recht_be_erstellen']);
unset($_SESSION['recht_be_bearbeiten_a']);
unset($_SESSION['recht_be_loeschen_a']);

unset($_SESSION['recht_r_bearbeiten']);

echo "Sie wurden erfolgreich ausgeloggt. Sie werden in 3 Sekunden zur Startseite weitergeleitet!";
echo '<meta http-equiv="refresh" content="3; URL=Startseite.php">';
echo'<br><button onclick="location.href=\'Startseite.php\'" type="button">Startseite</button>';
?>


