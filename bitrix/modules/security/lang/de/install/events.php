<?
$MESS ['VIRUS_DETECTED_NAME'] = "Virus gefunden";
$MESS ['VIRUS_DETECTED_DESC'] = "#EMAIL# - E-Mail-Adresse des Siteadministrators (aus den Einstellungen des Hauptmoduls)";
$MESS ['VIRUS_DETECTED_SUBJECT'] = "#SITE_NAME#: Virus gefunden";
$MESS ['VIRUS_DETECTED_MESSAGE'] = "Informationsmitteilung von der Seite  #SITE_NAME#.

----------------------------------------

Sie erhalten diese Nachricht, weil das proaktive Schutzsystem des Servers #SERVER_NAME# einen potentiell gef�hrlichen Code, der �hnlichkeit mit einem Virus hat, entdeckt hat.

1. Dieser potentiell gef�hrliche Code wurde aus dem HTML-Code geschnitten.
2. �berpr�fen Sie das Ereignisprotokoll und vergewissern Sie sich, dass der Code wirklich sch�dlich ist und nicht nur ein einfacher Counter- oder Framework-Code.
 (link: http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=de&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS)
3. Falls der Code nicht sch�dlich ist, f�gen Sie ihn der &#8220;Ausnahme-Liste&#8221; auf der Antivirus-Einstellungen-Seite hinzu.
(link: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=de&tabControl_active_tab=exceptions)
4. Falls der Code doch ein Virus ist, dann f�hren Sie nachfolgende Schritte aus:
a) �ndern Sie das Administrator-Login-Passwort und gegebenenfalls auch die Passw�rter anderer Benutzer mit dementsprechenden Berechtigungen
b) �ndern sie das Login-Passwort f�r den FTP-Zugang und den SSH-Zugang
c) Testen Sie die Computer von Administratoren, die Zugang zur Seite durch SSH oder FTP haben, auf Viren und l�schen Sie gegebenenfalls eventuelle Funde.
d) Schalten Sie die \"Passwort speichern\" Funktion bei Programmen aus, die einen Zugang �ber SSH oder FTP zu Ihrer Seite herstellen k�nnen.
e) L�schen Sie den bedrohlichen Code aus den infizierten Dateien. Das k�nnen Sie zum Beispiel durch Neuinstallation dieser Dateien aus dem letzten Backup erreichen.

---------------------------------------------------------------------
Diese Nachricht wurde automatisch generiert.";
?>