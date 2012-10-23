<?
$MESS ['VIRUS_DETECTED_NAME'] = "Virus gefunden";
$MESS ['VIRUS_DETECTED_DESC'] = "#EMAIL# - E-Mail-Adresse des Siteadministrators (aus den Einstellungen des Hauptmoduls)";
$MESS ['VIRUS_DETECTED_SUBJECT'] = "#SITE_NAME#: Virus gefunden";
$MESS ['VIRUS_DETECTED_MESSAGE'] = "Informationsmitteilung von der Seite  #SITE_NAME#.

----------------------------------------

Sie erhalten diese Nachricht, weil das proaktive Schutzsystem des Servers #SERVER_NAME# einen potentiell gefährlichen Code, der Ähnlichkeit mit einem Virus hat, entdeckt hat.

1. Dieser potentiell gefährliche Code wurde aus dem HTML-Code geschnitten.
2. Überprüfen Sie das Ereignisprotokoll und vergewissern Sie sich, dass der Code wirklich schädlich ist und nicht nur ein einfacher Counter- oder Framework-Code.
 (link: http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=de&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS)
3. Falls der Code nicht schädlich ist, fügen Sie ihn der &#8220;Ausnahme-Liste&#8221; auf der Antivirus-Einstellungen-Seite hinzu.
(link: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=de&tabControl_active_tab=exceptions)
4. Falls der Code doch ein Virus ist, dann führen Sie nachfolgende Schritte aus:
a) Ändern Sie das Administrator-Login-Passwort und gegebenenfalls auch die Passwörter anderer Benutzer mit dementsprechenden Berechtigungen
b) Ändern sie das Login-Passwort für den FTP-Zugang und den SSH-Zugang
c) Testen Sie die Computer von Administratoren, die Zugang zur Seite durch SSH oder FTP haben, auf Viren und löschen Sie gegebenenfalls eventuelle Funde.
d) Schalten Sie die \"Passwort speichern\" Funktion bei Programmen aus, die einen Zugang über SSH oder FTP zu Ihrer Seite herstellen können.
e) Löschen Sie den bedrohlichen Code aus den infizierten Dateien. Das können Sie zum Beispiel durch Neuinstallation dieser Dateien aus dem letzten Backup erreichen.

---------------------------------------------------------------------
Diese Nachricht wurde automatisch generiert.";
?>