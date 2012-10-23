<?
$MESS ['SEC_SESSION_ADMIN_SESSID_NOTE'] = "<p>Wenn diese Funktion aktiviert ist, wird die Sitzungs-ID innerhalb des vorgegebenen Zeitraums erneuert. Der Server wird dabei zusätzlich belastet, Session-Diebstahl ist jedoch zwecklos.</p><p><i>Empfohlen für die hohe Sicherheitsstufe.</o></p>";
$MESS ['SEC_SESSION_ADMIN_DB_NOTE'] = "<p>Die meisten Angriffe auf Web-Anwendungen haben als Ziel, an die Session-Daten eines Users zu gelangen. Der <b>Session-Schutz</b> macht die Übernahme von Sitzungsdaten, vor allem die der Administratoren unmöglich.</p><p><b>Session-Schutz</b> erweitert die standardisierten Einstellungen im System und beinhaltet:<ul style=\"font-size:100%\"><li>Austausch der Sitzungs-ID mehrmals pro Sitzung</li><li>Session-Daten werden in der Modul-Datenbanktabelle gespeichert</li></ul><p>Session-Daten werden in der Modul-Datenbanktabelle gespeichert, so können diese Daten durch den Einsatz von anderen Skripten auf dem gleichen Server nicht gelesen werden. Konfigurationsfehler des virtuellen Hostings oder Fehler beim Verteilen der Zugriffsrechte auf temporäre Dateien und andere Konfigurationsfehler haben keine Auswirkung auf die Sicherheit der Session-Daten.Außerdem wird die Belastung zwischen dem File-Server und der Datenbank verteilt.</p><p><i>Empfohlen für die hohe Sicherheitsstufe</i></p>";
$MESS ['SEC_SESSION_ADMIN_DB_WARNING'] = "Achtung! Beim Modus-Wechsel werden alle Sessions gelöscht. Alle User müssen sich neu anmelden.";
$MESS ['SEC_SESSION_ADMIN_SESSID_TAB_TITLE'] = "Parameter für den Wechsel der Sitzungs-ID konfigurieren";
$MESS ['SEC_SESSION_ADMIN_SAVEDB_TAB_TITLE'] = "Session-Daten in der Datenbank speichern";
$MESS ['SEC_SESSION_ADMIN_SESSID_BUTTON_OFF'] = "ID Wechsel deaktivieren";
$MESS ['SEC_SESSION_ADMIN_DB_BUTTON_OFF'] = "Das Speichern der Session-Daten in der Datenbank deaktivieren";
$MESS ['SEC_SESSION_ADMIN_SESSID_BUTTON_ON'] = "ID-Wechsel aktivieren";
$MESS ['SEC_SESSION_ADMIN_SESSID_TAB'] = "ID Wechsel";
$MESS ['SEC_SESSION_ADMIN_DB_OFF'] = "Session-Daten werden nicht in der Modul-Datenbank gespeichert.";
$MESS ['SEC_SESSION_ADMIN_DB_ON'] = "Session-Daten werden in der Modul-Datenbank gespeichert.";
$MESS ['SEC_SESSION_ADMIN_SESSID_OFF'] = "Der Wechsel der Sitzungs-ID ist deaktiviert.";
$MESS ['SEC_SESSION_ADMIN_SESSID_ON'] = "Der Wechsel der Sitzungs-ID ist aktiviert.";
$MESS ['SEC_SESSION_ADMIN_SESSID_WARNING'] = "Die Sitzungs-ID ist nicht kompatibel. Die Sitzungs-ID, die von der Funktion session_id zurückgegeben wird, darf 32 Zeichen nicht überschreiten, nur aus lateinischen Buchstaben und Ziffern bestehen.";
$MESS ['SEC_SESSION_ADMIN_SESSID_TTL'] = "ID-Lebensdauer in Sekunden";
$MESS ['SEC_SESSION_ADMIN_TITLE'] = "Sitzungsschutz";
$MESS ['SEC_SESSION_ADMIN_SAVEDB_TAB'] = "In der Datenbank speichern";
$MESS ['SEC_SESSION_ADMIN_DB_BUTTON_ON'] = "Session-Daten in der Datenbank speichern";
?>