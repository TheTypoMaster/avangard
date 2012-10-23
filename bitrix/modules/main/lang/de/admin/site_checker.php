<?
$MESS["SC_TITLE"] = "Seitenprüfung";
$MESS["SC_NOTE"] = "Die in <font color=\"#009900\"><b>Grün</b></font> angezeigten Werte entsprechen den Systemanforderungen.<br>
Die in <font color=\"#FF0000\"><b>Rot</b></font> angezeigten Werte entsprechen  <u><b>nicht</b></u> den Systemanforderungen.<br>
Auf die in <font color=\"#000000\"><b>Schwarz</b></font> angezeigten Werte sollte geachtet werden<br>
<br>
Wenn die Autorisierung nicht korrekt funktioniert, können Sie die Website auch ohne Authentifizierung prüfen. Erstellen Sie dafür eine leere Datei <b>#VAL#</b>. Danach können Sie die Seite <b>/bitrix/admin/site_checker.php</b> ohne Autorisierung öffnen.
<br><b>Achtung!</b> Löschen Sie immer die Datei <b>site_checker_debug</b> wenn die Website-Prüfung abgeschlossen ist.
";
$MESS["SC_SUBTITLE_REQUIED"] = "Erforderliche Systemparameter";
$MESS["SC_T_BACKTRACK_LIMIT"] = "Wert des Parameters pcre.backtrack_limit";
$MESS["SC_SUBTITLE_REQUIED_DESC"] = "Ihr System (Webserver) muss den Mindestanforderungen entsprechen.&nbsp;Werden Parameter/Werte rot angezeigt, müssen die Probleme behoben werden. Andernfalls ist eine funktionsfähige Installation nicht möglich. Dies stellt dann keinen Grund für eine Reklamation dar, da die Vorraussetzungen bekannt sind.";
$MESS["SC_SUBTITLE_DISK"] = "Festplattenzugriffs-Überprüfung";
$MESS["SC_SUBTITLE_DISK_DESC"] = "Die Skripte müssen Schreibzugriff auf alle Dateien haben. Dies ist für die ordnungsgemäße Funktion des Datei-Managers, des Uploads und des Update-Systems erforderlich.";
$MESS["SC_VER_ERR"] = "Die PHP-Version ist #CUR#, erforderlich ist jedoch #REQ# oder höher.";
$MESS["SC_MOD_XML"] = "XML Unterstützung";
$MESS["SC_MOD_PERL_REG"] = "Unterstützung für reguläre Ausdrücke (Perl kompatibel)";
$MESS["SC_MOD_GD"] = "GD Library";
$MESS["SC_MOD_GD_JPEG"] = "Unterstützung für JPEG in GD";
$MESS["SC_UPDATE_ACCESS"] = "Der Zugriff zum Update-Server";
$MESS["SC_UPDATE_ERROR"] = "Keine Verbindung zum Update-Server.";
$MESS["SC_TMP_FOLDER_PERMS"] = "Unzureichende Zugriffsberechtigung, um im temporären Ordner zu schreiben.";
$MESS["SC_NO_TMP_FOLDER"] = "Temporärer Ordner existiert nicht.";
$MESS["ERR_NO_MODS"] = "Die erforderlichen Erweiterungen sind nicht installiert:";
$MESS["SC_CYR_SYMBOLS_RU_ONLY"] = "a-zA-Z";
$MESS["ERR_NO_SSL"] = "Die SSL-Unterstützung ist für PHP nicht aktiviert";
$MESS["SC_SUBTITLE_SITE_MODULES"] = "Seitenmodule";
$MESS["SC_RUS_L1"] = "Nachricht von der Seite";
$MESS["SC_TIK_SEND_SUCCESS"] = "Die Nachricht wurde erfolgreich gesendet. Bitte prüfen Sie Ihr E-Mail-Postfach #EMAIL# ,um die Bestätigungsmail des Support-Teams zu lesen.";
$MESS["SC_TIK_TITLE"] = "E-Mail an den Technischen Support senden";
$MESS["SC_TIK_DESCR"] = "Problembeschreibung";
$MESS["SC_TIK_DESCR_DESCR"] = "Folge der Arbeitsschritte, die den Fehler verursachten, Fehlerbeschreibung,... ";
$MESS["SC_TIK_LAST_ERROR"] = "Letzte Fehlermeldung";
$MESS["SC_TIK_LAST_ERROR_ADD"] = "angehängt";
$MESS["SC_TIK_SEND_MESS"] = "Nachricht senden";
$MESS["SC_TIK_HELP_BACK"] = "Zurück...";
$MESS["SC_TAB_2"] = "Zugriffsüberprüfung";
$MESS["SC_TAB_4"] = "Module";
$MESS["SC_TAB_5"] = "Technischer Support";
$MESS["SC_ERROR"] = "Fehler";
$MESS["SC_CHECK_FILES"] = "Dateiberechtigungen überprüfen";
$MESS["SC_CHECK_FILES_WARNING"] = "Datei-Zugriffsüberprüfung verursacht hohe Last auf dem Server.";
$MESS["SC_CHECK_FILES_ATTENTION"] = "Achtung!";
$MESS["SC_TEST_CONFIG"] = "Konfigurationsprüfung";
$MESS["SC_TESTING"] = "Überprüfung läuft...";
$MESS["SC_FILES_CHECKED"] = "Geprüfte Dateien: <b>#NUM#</b><br>Aktueller Pfad: <i>#PATH#</i>";
$MESS["SC_FILES_OK"] = "Alle geprüften Dateien sind verfügbar zum Lesen und Schreiben.";
$MESS["SC_FILES_FAIL"] = "Nicht verfügbar zum Lesen und Schreiben (die ersten 10):";
$MESS["SC_SITE_CHARSET_FAIL"] = "Gemischte Zeichensätze: UTF-8 und nicht UTF-8";
$MESS["SC_PATH_FAIL_SET"] = "Der Pfad zum Website-Root muss leer sein, der aktuelle Pfad ist:";
$MESS["SC_NO_ROOT_ACCESS"] = "Kein Zugriff auf den Ordner ";
$MESS["SC_SOCKET_F"] = "Sockelsupport";
$MESS["SC_CHECK_FULL"] = "Komplettprüfung";
$MESS["SC_CHECK_UPLOAD"] = "Prüfung des Uploadordners";
$MESS["SC_CHECK_KERNEL"] = "Kernprüfung";
$MESS["SC_CHECK_FOLDER"] = "Ordnerprüfung";
$MESS["SC_CHECK_B"] = "Prüfung";
$MESS["SC_STOP_B"] = "Stop";
$MESS["SC_TEST_NAME"] = "Testname";
$MESS["SC_TEST_RES"] = "Ergebnis";
$MESS["SC_TEST_FAIL"] = "Üngültiger Serverantwort. Der Test kann nicht abgeschlossen werden.";
$MESS["SC_START_TEST_B"] = "Test starten";
$MESS["SC_STOP_TEST_B"] = "Stop";
$MESS["SC_COMMENT"] = "<b>Anmerkung:</b> Mögliche Probleme, die durch die Tests erkannt wurden, sind von der Serverkonfiguration verursacht. Bitte wenden Sie sich an Ihren Hostinganbieter um die Probleme zu beheben.";
$MESS["SC_T_LOG"] = "Logdatei wird erstellt";
$MESS["SC_T_SOCK"] = "Sockel verwenden";
$MESS["SC_T_UPLOAD"] = "Datei hochladen";
$MESS["SC_T_UPLOAD_BIG"] = "Über 4 Mb große Dateien hochladen";
$MESS["SC_T_POST"] = "POST-Anfragen mit mehreren Parametern";
$MESS["SC_T_PRECISION"] = "Wert des Parameters \"precision\" (nicht weniger als 10)";
$MESS["SC_T_MAIL"] = "E-Mail wird gesendet";
$MESS["SC_T_MAIL_BIG"] = "Große E-Mail wird gesendet (über 64 KB)";
$MESS["SC_T_MAIL_B_EVENT"] = "Auf ungesendete Nachrichten überprüfen";
$MESS["SC_T_MAIL_B_EVENT_ERR"] = "Fehler beim Versand von Systemnachrichten. Folgende Nachrichten wurden nicht gesendet:";
$MESS["SC_T_REDIRECT"] = "Lokale Weiterleitungen (LocalRedirect-Funktion)";
$MESS["SC_T_MEMORY"] = "Speicherlimit";
$MESS["SC_T_SESS"] = "Sitzung beibehalten";
$MESS["SC_T_SESS_UA"] = "Sitzung beibehalten ohne NutzerAgent";
$MESS["SC_T_CACHE"] = "Cache-Dateien verwenden";
$MESS["SC_T_AUTH"] = "HTTP-Autorisierung";
$MESS["SC_T_EXEC"] = "Dateierstellung und Ausführung";
$MESS["SC_T_SUHOSIN"] = "Verfügbarkeit des Suhosin-Moduls";
$MESS["SC_T_BX_CRONTAB"] = "Die Konstante BX_CRONTAB (muss nicht bestimmt sein)";
$MESS["SC_T_SECURITY"] = "Das Apache mod_security Modul";
$MESS["SC_T_DELIMITER"] = "Dezimaltrennzeichen";
$MESS["SC_T_DBCONN"] = "Redundante Ausgabe in den Konfigurationsdateien";
$MESS["SC_T_MYSQL_VER"] = "MySQL-Version";
$MESS["SC_T_TIME"] = "Datenbank- und Webserverzeiten";
$MESS["SC_T_SQL_MODE"] = "MySQL-Modus";
$MESS["SC_T_AUTOINC"] = "MySQL auto_increment Wert";
$MESS["SC_T_CHARSET"] = "Datenbanktabellenzeichensatz";
$MESS["SC_DB_CHARSET"] = "Zeichenkodierung der Datenbank";
$MESS["SC_MBSTRING_NA"] = "Prüfung ist wegen der UTF-Konfigurationsfehler fehlgeschlagen";
$MESS["SC_CONNECTION_CHARSET"] = "Zeichenkodierung der Verbindung";
$MESS["SC_TABLES_NEED_REPAIR"] = "Tabellenintegrität ist verletzt, eine Korrektur ist erforderlich.";
$MESS["SC_TABLE_ERR"] = "Fehler in der Tabelle #VAL#:";
$MESS["SC_T_CHECK"] = "Tabellenprüfung";
$MESS["SC_TEST_SUCCESS"] = "Erfolg";
$MESS["SC_LOG_OK"] = "Logdatei erstellt:";
$MESS["SC_F_OPEN"] = "Öffnen";
$MESS["SC_SENT"] = "Gesendet in:";
$MESS["SC_SEC"] = "Sek.";
$MESS["SC_DB_ERR"] = "Fehler bei der Datenbankversion:";
$MESS["SC_DB_ERR_MODE"] = "Die sql_mode Variable in MySQL muss leer sein. Aktueller Wert:";
$MESS["SC_DOCROOT_FAIL"] = "Die Pfade in __FILE__ (#DIR0#) und _SERVER[DOCUMENT_ROOT] (#DIR1#) stimmen nicht überein.";
$MESS["SC_NO_PROXY"] = "Kann keine Verbindung zum Proxyserver aufbauen.";
$MESS["SC_PROXY_ERR_RESP"] = "Ungülgige Antwort des Updateservers wegen dem Proxy";
$MESS["SC_UPDATE_ERR_RESP"] = "Ungültige Antwort des Updateservers.";
$MESS["SC_FILE_EXISTS"] = "Datei vorhanden:";
$MESS["SC_WARN_SUHOSIN"] = "Der Suhosin-Modul wurde geladen, es können Probleme mit dem administrativen Panel auftreten.";
$MESS["SC_WARN_SECURITY"] = "Der mod_security-Modul wurde geladen, es können Probleme mit dem administrativen Panel auftreten.";
$MESS["SC_DELIMITER_ERR"] = "Aktuelles Trennzeichen: &quot;#VAL#&quot;, &quot;.&quot; ist erforderlich.";
$MESS["SC_DB_MISC_CHARSET"] = "Der Zeichensatz (#T_CHAR#) der Tabelle #TBL# entspricht nicht dem Datenbankzeichensatz (#CHARSET#).";
$MESS["SC_COLLATE_WARN"] = "Der Vergleichswert für &quot;#TABLE#&quot; (#VAL0#) weicht von dem Datenbankwert (#VAL1#) ab. ";
$MESS["SC_TABLE_CHARSET_WARN"] = "Die &quot;#TABLE#&quot;-Tabelle enthält Felder, die in der Kodierung nicht mit der Datenbankkdierung übereinstimmen. ";
$MESS["SC_FIELDS_COLLATE_WARN"] = "Das Ergebnis des Feldes &quot;#FIELD#&quot; in der Tabelle &quot;#TABLE#&quot;  (#VAL1#) stimmt nicht mit dem der Datenbank (#VAL1#) überein.";
$MESS["SC_TABLE_SIZE_WARN"] = "Die Größe der &quot;#TABLE#&quot;-Tabelle ist möglicherweise zu groß (#SIZE# M).";
$MESS["SC_NOT_LESS"] = "Nicht kleiner als #VAL# M.";
$MESS["SC_TIK_ADD_TEST"] = "Testlog senden";
$MESS["SC_SUPPORT_COMMENT"] = "Wenn Sie Probleme mit dem Nachrichtenversand haben, verwenden Sie sich bitte die Kontaktform auf unserer Website:";
$MESS["SC_NOT_FILLED"] = "Problembeschreibung erforderlich.";
$MESS["SC_TEST_WARN"] = "Der Konfigurationsbericht wird abgeschlossen.\\r\\nWenn Fehler auftreten, bitte entfernen Sie die Markierung \"Testlog senden\", und versuchen Sie es noch ein mal.";
$MESS["SC_SOCK_NA"] = "Prüfung ist wegen Socket-Fehler fehlgeschlagen.";
$MESS["SC_T_CLONE"] = "Objekt-Übergabe mit dem Verweis";
$MESS["SC_T_GETIMAGESIZE"] = "Getimagesize-Unterstützung für SWF";
$MESS["SC_TEST_DOMAIN_VALID"] = "Die aktuelle Domain ist ungültig (#VAL#). Der Domainname kann nur Ziffern, lateinische Buchstaben und Bindestriche enthalten. Die Top-Level-Domain muss durch einen Punkt getrennt werden (z.B. .com).";
$MESS["SC_SWF_WARN"] = "Einfügen der SWF-Objekte wird eventuell nicht funktionieren.";
$MESS["SC_TIME_DIFF"] = "Der Zeitunterschied beträgt #VAL# Sekunden.";
$MESS["SC_T_MODULES"] = "Erforderliche PHP-Module";
$MESS["SC_MOD_MBSTRING"] = "Mbstring-Unterstützung";
$MESS["SC_MB_UTF"] = "Die Website funktioniert in der UTF-Kodierung";
$MESS["SC_MB_NOT_UTF"] = "Die Website funktioniert in der Einzelbyte-Kodierung";
$MESS["SC_MB_CUR_SETTINGS"] = "Mbstring-Parameter:";
$MESS["SC_MB_REQ_SETTINGS"] = "Erforderlich:";
$MESS["SC_T_MBSTRING"] = "Parameter der UTF-Konfiguration (mbstring und BX_UTF)";
$MESS["SC_T_SITES"] = "Website-Parameter";
$MESS["SC_BX_UTF"] = "Benutzen Sie folgenden Code in <i>/bitrix/php_interface/dbconn.php</i>:
<code>define('BX_UTF', true);</code> 
";
$MESS["SC_BX_UTF_DISABLE"] = "Die Konstante BX_UTF muss nicht bestimmt werden";
$MESS["SC_T_PHP"] = "Erforderliche PHP-Parameter";
$MESS["SC_ERR_PHP_PARAM"] = "Der Parameter #PARAM# ist #CUR#, erforderlich ist jedoch #REQ#.";
$MESS["SC_MYSQL_ERR_VER"] = "Aktuell ist die MySQL-Version #CUR# installiert, erforderlich ist jedoch #REQ#.";
$MESS["SC_T_SERVER"] = "Server-Variablen";
$MESS["SC_MORE"] = "Mehr";
$MESS["SC_CONNECTION_CHARSET_WRONG"] = "Die Zeichenkodierung der Verbindung mit der Datenbank muss #VAL# sein, der aktuelle Wert ist #VAL1#.";
$MESS["SC_CONNECTION_CHARSET_WRONG_NOT_UTF"] = "Die Zeichenkodierung der Verbindung mit der Datenbank soll nicht UTF-8 sein, der aktuelle Wert ist: #VAL#.";
$MESS["SC_CONNECTION_COLLATION_WRONG_UTF"] = "Die alphabetische Sortierung der Verbindung mit der Datenbank muss utf8_unicode_ci sein, der aktuelle Wert ist #VAL#.";
$MESS["SC_TABLE_CHECK_NA"] = "Prüfung ist wegen eines Fehlers der Datenbank-Zeichenkodierung fehlgeschlagen.";
$MESS["SC_FIX"] = "Korrigieren";
$MESS["SC_FIX_DATABASE"] = "Datenbank-Fehler korrigieren";
$MESS["SC_FIX_DATABASE_CONFIRM"] = "Das System wird jetzt versuchen, die Datenbank-Fehler zu korrigieren. Diese Aktion kann gefährlich sein. Erstellen Sie eine Datenbank-Sicherungskopie, bevor Sie weitere Schritte unternehmen.\\n\\nFortfahren?";
$MESS["SC_CHECK_TABLES_ERRORS"] = "Datenbank-Tabellen enthalten #VAL# Fehler der Zeichenkodierung, #VAL1# von ihnen können automatisch korrigiert werden.";
$MESS["SC_CONNECTION_CHARSET_NA"] = "Prüfung ist wegen eines Fehlers der Verbindungskodierung fehlgeschlagen.";
$MESS["SC_DATABASE_COLLATION_DIFF"] = "Die alphabetische Sortierung der Datenbank (#VAL1#) stimmt nicht mit der alphabetischen Sortierung der Verbindung  (#VAL0#) überein.";
$MESS["SC_DATABASE_CHARSET_DIFF"] = "Die Datenbank-Zeichenkodierung (#VAL1#) stimmt nicht mit der Zeichencodierung der alphabetischen Sortierung (#VAL0#) überein.";
$MESS["SC_HELP_NOTOPIC"] = "Zu diesem Thema gibt es leider keine Informationen.";
$MESS["SC_HELP_OPENLOG"] = "Hier wird eine Textdatei mit einem eindeutigen Namen wie site_checker_e45a34e4bf940ef3d78b2493cd56cc47.log im /bitrix folder erstellt. Solche Namen hindern Drittpersonen daran, diese Datei herunterzuladen.  Das System speichert in dieser Datei die Testergebnisse sowie zusätzliche Debugger-Informationen.

Wenn eine solche Datei nicht erstellt werden kann, überprüfen Sie die Systemzugriffsberechtigungen auf der Registerkarte  <b>Zugriffsüberprüfung</b>. Können Sie erforderliche Berechtigungen nicht hinzufügen, kontaktieren Sie den Technischen Support Ihres Hosting-Anbieters oder Ihren Systemadministrator. Im Bitrix Control Panel können diese Einstellung nicht vorgenommen werden.
";
$MESS["SC_HELP_CHECK_PHP_MODULES"] = "Hier werden die für das System erforderlichen PHP-Erweiterungen geprüft. Fehlen einige solche Erweiterungen, dann werden die Module angezeigt, welche ohne diese Erweiterungen nicht funktionieren können.

Um fehlende PHP-Erweiterungen hinzuzufügen, kontaktieren Sie den Technischen Support Ihres Hosting-Anbieters. Wenn Sie das System auf einem lokalen Computer installieren, werden Sie diese Erweiterungen manuell installieren müssen. Benutzen Sie dafür Dokumentation auf php.net.
";
$MESS["SC_HELP_CHECK_PHP_SETTINGS"] = "Hier werden kritische Parameter geprüft, die in der Datei php.ini bestimmt werden. Bei Fehlern werden nicht korrekt eingestellte Parameter angezeigt. Eine detaillierte Parameterbeschreibung finden Sie auf php.net.";
$MESS["SC_HELP_CHECK_SERVER_VARS"] = "Hier werden die Server-Variablen geprüft.

Der Wert von HTTP_HOST ist vom aktuellen virtuellen Host (der Domain) abgeleitet. Einige Browser können Cookies für nicht korrekte Domainnamen nicht speichern, weswegen auch eine Cookie-Autorisierung nicht möglich sein wird.
";
$MESS["SC_HELP_CHECK_MBSTRING"] = "Das Modul mbstring ist erforderlich, um mit mehreren Sprachen arbeiten zu können. Die Einstellungen dieses Moduls müssen sehr genau bestimmt werden in Abhängigkeit von der aktuellen Website-Kodierung: Die Parameter der UTF-8 Kodierung unterscheiden sich von denen einer nationalen Zeichenkodierung (z.B. cp1252).

Folgende Parameter sind für die Websites auf der Basis von UTF-8 erforderlich: 
<b>mbstring.func_overload=2</b>
<b>mbstring.internal_encoding=utf-8</b>

Der erste Parameter leitet Aufrufe der Funktionen von PHP-Zeichenketten implizit auf mbstring Funktionen um. Der zweite Parameter bestimmt die Text-Kodierung.

Wenn Ihre Website nicht UTF-8 benutzt, muss der erste Parameter 0 sein:
<b>mbstring.func_overload=0</b>

Wenn Sie aus irgend einem Grund die Funktionsumleitung nicht deaktivieren können, versuchen Sie eine Einzelbyte-Kodierung zu benutzen:
<b>mbstring.func_overload=2</b>
<b>mbstring.internal_encoding=latin1</b>

Wenn die festgelegten Werte mit den Parametern der Website nicht übereinstimmen, werden Sie merkwürdige und bizarre Fehler wie etwa abgeschnittene Wörter, den nicht funktionierenden XML-Import etc. feststellen.

<b>Beachten Sie,</b> dass der Parameter <b>mbstring.func_overload</b>in der globalen Datei  php.ini (oder in httpd.conf für einen virtuellen Server) bestimmt wird, während sich der Parameter der Kodierung inn .htaccess befindet. 

Alle Bitrix Module benutzen die Konstante <i>BX_UTF</I>, um die aktuelle Kodierung zu erkennen. Eine UTF-8-Website erfordert einen folgenden Code in <i>/bitrix/php_interface/dbconn.php</i>:
<code>define('BX_UTF', true);</code>
";
$MESS["SC_HELP_CHECK_SITES"] = "Allgemeine Multisite-Parameter werden geprüft. Wenn für eine Website der Pfad zum Root-Verzeichnis angegeben ist (was nur dann erforderlich ist, wenn die Websites auf verschiedenen Domains existieren), muss dieses Verzeichnis eine symbolische Verlinkung zum beschreibbaren \"bitrix\" Ordner enthalten.

Alle Websites, die mit demselben Bitrix System eingerichtet wurden, müssen dieselbe Kodierung benutzen: Entweder UTF-8 oder Einzelbyte-Kodierung.
";
$MESS["SC_HELP_CHECK_SOCKET"] = "Hier wird der Web-Server eingestellt, um die Verbindung mit sich selbst herstellen zu können. Dies ist erforderlich, um die Netzwerk-Funktionen zu prüfen und einige nachfolgende Tests durchzuführen.

Wenn dieser Test fehlschlägt, können nachfolgende Tests, bei denen ein extra PHP-Prozess benötigt wird, nicht durchgeführt werden. If this test fails, the subsequent tests requiring a child PHP process cannot be performed. Dieses Problem kann durch eine Firewall, einen beschränkten IP-Zugriff oder eine HTTP/HTLM Autorisierung verursacht werden. Deaktivieren Sie diese Funktionen, wenn Sie den Test durchführen.
";
$MESS["SC_HELP_CHECK_DBCONN"] = "Hier wird die Textausgabe in den Konfigurationsdateien  <i>dbconn.php</i> und <i>init.php</i> geprüft.

Selbst ein Leerzeichen oder ein Zeilenumbruch können dazu führen, dass eine komprimierte Seite von dem Client-Browser nicht entpackt und gelesen werden kann.

Darüber hinaus können Probleme mit Autorisierung und CAPTCHA auftreten.
";
$MESS["SC_HELP_CHECK_UPLOAD"] = "Hier wird versucht, die Verbindung mit dem Web-Server herzustellen und binäre Daten als eine Datei zu übertragen. Der Server wird dann die erhaltenen Daten mit den ursprünglichen vergleichen. Wird ein Problem entstehen, so kann es durch einige Parameter in <i>php.ini</I> verursacht werden, denn diese Datei lässt Übertragung von binären Daten nicht zu, oder durch einen nicht verfügbaren temporären Ordner (oder <i>/bitrix/tmp</i>).

Soll das Problem auftreten, kontaktieren Sie Ihren Hosting-Anbieter. Wenn Sie das System auf einem lokalen Computer installieren, werden Sie den Server manuell konfigurieren müssen.
";
$MESS["SC_HELP_CHECK_UPLOAD_BIG"] = "Hier wird eine große binäre Datei (über 4 Mb) hochgeladen. Wenn nun dieser Test fehlschlägt, der vorherige jedoch erfolgreich war, kann das Problem in der Einschränkung in php.ini (<b>post_max_size</b> oder <b>upload_max_filesize</b>) liegen. Benutzen Sie phpinfo, um aktuelle Werte zu setzen (Einstellungen - Tools - PHP Einstellungen).

Auch ein unzureichender Festplattenspeicher kann dieses Problem verursachen.
";
$MESS["SC_HELP_CHECK_POST"] = "Hier wird eine POST-Anfrage mit mehreren Parametern gesendet. Einige Softwares, die den Server schützen, beispielsweise \"suhosin\", können ausführliche Anfragen blockieren. In diesem Fall können die Informationsblockelemente meistens nicht gespeichert werden.";
$MESS["SC_HELP_CHECK_MAIL"] = "Hier wird eine E-Mail-Nachricht an hosting_test@bitrixsoft.com via PHP-Standardfunktion \"mail\" gesendet. Dabei gibt es ein spezielles Postfach, damit die Testbedingungen an die des wirklichen Lebens maximal angepasst werden. 

Dieser Test sendet das Skript der Seitenprüfung als eine Testnachricht, aber  <b>er sendet nie irgendwelche Nutzerdaten</b>.

Beachten Sie, dass der Test den Nachrichtempfang nicht überprüft. Der Empfang bei den anderen Postfächern kann ebenso nicht überprüft werden.

Wenn Versenden von E-Mails länger als eine Sekune dauern, kann die Server-Leistungsstärke wesentlich beeinträchtigt werden. Kontaktieren Sie den Technischen Support Ihres Hosting-Anbieters, damit er das Versenden der E-Mails über einen Spooler konfiguriert.

Alternativ können Sie cron benutzen, um die E-Mails zu versenden. Dafür fügen Sie <code>define('BX_CRONTAB_SUPPORT', true);</code> zu dbconn.php hinzu. Dann stellen Sie cron ein, <i>php /var/www/bitrix/modules/main/tools/cron_events.php</I> jede Minute auszuführen (ersetzen Sie <i>/var/www</i> durch das Root-Verzeichnis Ihrer Website).

Wenn der Aufruf der Funktion mail() fehlgeschlagen wurde, werden Sie die E-Mails von Ihrem Server mit Standardverfahren nicht versenden können.

Wenn Ihr Hosting-Anbieter alternative Services zum Versenden von E-Mails anbietet, können Sie diese via Funktion \"custom_mail\" benutzen. Bestimmen Sie diese Funktion in <i>/bitrix/php_interface/dbconn.php</I>. Wird diese Funktion bestimmt, wird sie im System anstatt der PHP-Funktion \"mail\" mit denselben Ausgabeparametern benutzt.
";
$MESS["SC_HELP_CHECK_MAIL_BIG"] = "Beim Versenden einer umfangreichen Nachricht wird der Text der vorherigen Mail (Skript der Seitenprüfung) 10 Mal wiederholt. Darüber hinaus wird die Betreff-Zeile in zwei Zeilen aufgeteilt sowie das BCC-Feld zum Senden an noreply@bitrixsoft.com hinzugefügt.

Wenn der Server nicht korrekt konfiguriert ist, können solche Nachrichten nicht versendet werden.

Sollten etwaige Probleme entstehen, kontaktieren Sie Ihren Hosting-Anbieter. Wenn Sie das System auf einem lokalen Computer installieren, werden Sie den Server manuell konfigurieren müssen.
";
$MESS["SC_HELP_CHECK_MAIL_B_EVENT"] = "In der Datenbank-Tabelle B_EVENT werden die E-Mail-Warteschlangen von der Website gespeichert sowie die Aktivitäten zum Versenden der E-Mails registriert. Wenn einige Nachrichten nicht versendet werden können, sind mögliche Problemursachen eine ungültige Empfängeradresse, nicht korrekte Parameter der E-Mail-Vorlage oder das E-Mailsystem des Servers.";
$MESS["SC_HELP_CHECK_LOCALREDIRECT"] = "Nachdem das Formular des administrativen Bereichs gespeichert ist (also auf Speichern oder Anwenden geklickt wurde), wird der Client auf die ursprüngliche Seite umgeleitet. Das wird gemacht, um wiederholte Formulareinträge zu vermeiden, wenn ein Nutzer die Seite aktualisiert. Damit diese Umleitung erfolgreich funktioniert, muss eine ganze Reihe von wichtigen Variablen auf dem Web-Server korrekt bestimmt sind sowie das Überschreiben der http-Überschriften erlaubt ist.

Wenn einige der Server-Variablen in <i>dbconn.php</i> neu bestimmt wurden, wird der Test eben diese Neubestimmungen benutzen. Mit anderen Worten, bei der Umleitung werden reale Lebenssituationen komplett simuliert.
";
$MESS["SC_HELP_CHECK_MEMORY_LIMIT"] = "Bei diesem Test wird ein extra PHP-Prozess erstellt, um eine Variable mit der schrittweise inkrementierten Größe zu generieren. Zum Schluss wird dadurch der Speicherumfang festgelegt, welcher für den PHP-Prozess verfügbar sein wird.

PHP bestimmt die Speichereinschränkungen in php.ini , indem der Parameter <b>memory_limit</b> eingestellt wird. Aber Sie sollten diesem Parameter nicht vertrauen, da auf den Hostings auch noch weitere Einschränkungen gesetzt werden können.

Der Test versucht, den Wert von <b>memory_limit</b> zu erhöhen, indem er den folgenden Code benutzt:
<code>ini_set(&quot;memory_limit&quot;, &quot;512M&quot;)</code>

Wenn der aktuelle Wert kleiner ist, fügen Sie die Zeile vom Code zu <i>/bitrix/php_interface/dbconn.php</i> hinzu.
";
$MESS["SC_HELP_CHECK_SESSION"] = "Dieser Test prüft, ob der Server die Daten mithilfe von Sitzungen speichern kann. Das ist erforderlich, damit die Autorisierung zwischen den Hits verfügbar bleibt.

Dieser Test wird fehlschlagen, wenn auf dem Server die Unterstützung für Sitzungen nicht installiert ist, ein nicht gültiges Sitzungsverzeichnis angegeben ist oder wenn dieses Verzeichnis schreibgeschützt ist.
";
$MESS["SC_HELP_CHECK_SESSION_UA"] = "Hier wird die Fähigkeit geprüft, Sitzungen zu speichern, ohne dabei die http-Überschrift <i>User-Agent</i> einzustellen. 

Mehrere externe Anwendungen und Add-Ons stellen diese Überschrift nicht ein, beispielsweise Uploader für Dateien und Fotos, WebDav-Clients etc. 

Wenn der Test fehlschlägt, liegt das Problem höchstwahrscheinlich bei der nicht korrekten Konfiguration des PHP-Moduls <b>suhosin</b>.
";
$MESS["SC_HELP_CHECK_CACHE"] = "Bei diesem Test wird geprüft, ob ein PHP-Prozess eine <b>.tmp</b> Datei im Cache-Verzeichnis erstellen und diese dann zu <b>.php</b> umbenennen kann. Einige Web-Server für Windows können beim Umbenennen versagen, wenn die Nutzer-Zugriffsberechtigungen nicht korrekt eingestellt sind.";
$MESS["SC_HELP_CHECK_UPDATE"] = "Hier wird versucht, mithilfe von den aktuellen Einstellungen des Hauptmoduls eine Testverbindung zum Update-Server herzustellen. Kann die Verbindung nicht hergestellt werden, werden Sie die Updates nicht installieren oder Ihre Testversion aktivieren können.

Die wahrscheinlichen Problemursachen  dafür sind nicht korrekte Proxy-Einstellungen, Firewall-Einschränkungen oder ungültige Netzwerk-Einstellungen des Servers.
";
$MESS["SC_HELP_CHECK_HTTP_AUTH"] = "Mithilfe von den HTTP-Überschriften werden bei diesem Test die Autorisierungsdaten gesendet. Dann wird versucht, diese Daten mit der Server-Variablen REMOTE_USER (oder REDIRECT_REMOTE_USER) zu bestimmen. Die HTTP-Autorisierung ist für die Integration mit den Softwares der Dritthersteller erforderlich.



Wenn PHP im Modus CGI/FastCGI funktioniert (fragen Sie dies bei Ihrem Hosting-Anbieter nach), verlangt der Apache-Server das Modul mod_rewrite module und eine folgende Regel in .htaccess:

<b>RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]</b>



Wenn möglich, konfigurieren Sie PHP als ein Apache-Modul.
";
$MESS["SC_HELP_CHECK_EXEC"] = "Wenn PHP im Modus CGI/FastCGI auf einem Unix-System funktioniert, verlangen Skripts bestimmte Berechtigungen zur Ausführung, sonst werden sie nicht funktionieren.

Wenn dieser Test fehlschlägt, kontaktieren Sie den Technischen Support Ihres Hosting-Anbieters, um benötigte Zugriffsberechtigungen für Dateien zu bekommen und stellen Sie dann die Konstanten <b>BX_FILE_PERMISSIONS</b> und <b>BX_DIR_PERMISSIONS</b> in <i>dbconn.php</i> entsprechend ein.



Wenn möglich, konfigurieren Sie PHP als ein Apache-Modul.
";
$MESS["SC_HELP_CHECK_SUHOSIN"] = "Das Modul suhosin ist dazu gedacht, Server und Nutzer vor Hackern zu schützen. Es kann jedoch auch ganz harmlose Aktionen blockieren, welche ein gewöhnlicher Nutzer ausführen kann. Grundsätzlich wird es empfohlen, dieses Modul zu deaktivieren. Können Sie das nicht machen, dann aktivieren Sie die Option einer Simulation, bei der die Daten nur geprüft, aber nicht blockiert werden:

<b>suhosin.simulation=1</b>



Das Modul Proaktiver Schutz wurde mit der Rücksicht auf die Bitrix-Architektur entwickelt, es bietet eine effektivere Möglichkeit, Ihre Web-Lösung zu schützen.
";
$MESS["SC_HELP_CHECK_BX_CRONTAB"] = "Um die aperiodischen Agenten und die E-Mail auf cron zu übertragen, fügen Sie die folgende Konstante zu <i>/bitrix/php_interface/dbconn.php</i> hinzu:

<code>define('BX_CRONTAB_SUPPORT', true);</code>



Wenn bei dieser Konstante der Wert \"true\" gesetzt wird, werden im System nur periodische Agenten bei Hits ausgeführt. Nun fügen Sie zu cron eine Aufgabe hinzu, das Skript <i>/var/www/bitrix/modules/main/tools/cron_events.php</i> jede Minute auszuführen (ersetzen Sie  <i>/var/www</i> durch den Pfad zum Root-Verzeichnis Ihrer Website).



Das Skript bestimmt die Konstante <b>BX_CRONTAB</b>, die zeigt, dass das Skript von cron aus aktiviert ist und nur aperiodische Agenten ausführt. Wenn Sie diese Konstante aus Versehen in <i>dbconn.php</i> bestimmen, werden periodische Agenten nie ausgeführt.
";
$MESS["SC_HELP_CHECK_SECURITY"] = "Das Apache-Modul  mod_security ist genauso wie das PHP-Modul suhosin dazu gedacht, die Website gegen Hacker zu schützen. In der Tat stört es aber meistens, normale Nutzeraktivitäten auszuführen. Es wird also empfohlen, das Standardmodul \"Proaktiver Schutz\" anstatt von mod_security zu benutzen.";
$MESS["SC_HELP_CHECK_DIVIDER"] = "Ein Punkt muss als ein Dezimaltrennzeichen benutzt werden. Nachdem Sie die Lokale eingestellt haben, können Sie das Dezimaltrennzeichen als ein beliebig anderes Zeichen neu bestimmen. Zur Problemlösung verwenden Sie den folgenden Code:

<code>setlocale(LC_NUMERIC,'C');</code>
";
$MESS["SC_HELP_CHECK_PRECISION"] = "Der PHP-Präzisionswert (<b>precision</b>) muss nicht weniger als 10 sein. Kleinere Werte können merkwürdige Fehler verursachen. Der Standardwert ist 14.";
$MESS["SC_HELP_CHECK_CLONE"] = "Seit der Version 5 werden im PHP die Objekte eher mit dem Verweis übertragen als kopiert. Es gibt aber nach wie vor PHP 5 Sets, die das Vererben unterstützen, so dass Objekte als Kopien übertragen werden.



Um dieses Problem zu lösen, laden Sie einen aktuelleren PHP 5 Set herunter und installieren Sie ihn.
";
$MESS["SC_HELP_CHECK_GETIMAGESIZE"] = "Wenn Sie ein Flash-Objekt hinzufügen, braucht der visuelle Editor die Objektgröße zu erkennen. Dazu führt er die PHP-Standardfunktion <b>getimagesize</b> aus, welche die Erweiterung <b>Zlib</b> erfordert. Bei komprimierten Flash-Objekten kann diese Funktion fehlschlagen, wenn die Erweiterung  <b>Zlib</b> als ein Modul installiert ist. Die Erweiterung muss also statisch aufgebaut werden.



Zur Lösung dieses Problem kontaktieren Sie den Technischen Support Ihres Hosting-Anbieters.
";
$MESS["SC_HELP_CHECK_MYSQL_BUG_VERSION"] = "Es gibt einige MySQL-Versionen, in denen Fehler enthalten sind, welche ein fehlerhaftes Funktionieren der Website verursachen können.

<b>4.1.21</b> - Sortierung funktioniert unter bestimmten Bedingungen nicht korrekt;

<b>5.0.41</b> - Die Funktion EXISTS funktioniert nicht korrekt; die Suchfunktionen lassen nicht korrekte Ergebnisse anzeigen;

<b>5.1.34</b> - Der Schritt auto_increment ist standardmäßig 2, während 1 erforderlich ist.



Haben Sie bei Ihnen eine dieser MySQL-Versionen installiert, sollten Sie MySQL aktualisieren.
";
$MESS["SC_HELP_CHECK_MYSQL_TIME"] = "Bei diesem Test wird die Systemzeit der Datenbank mit der Zeit des Web-Servers verglichen. Diese zwei Zeitsysteme können nicht synchron laufen, wenn sie auf individuellen Maschinen installiert sind. Öfter kommt es jedoch vor, dass die Konfiguration der Zeitzone nicht korrekt vorgenommen wurde.



Stellen Sie die PHP-Zeitzone in <i>/bitrix/php_interface/dbconn.php</i> ein:

<code>date_default_timezone_set(&quot;Europe/Berlin&quot;);</code> (geben Sie Ihre Region und Stadt ein).



Stellen Sie die Zeitzone der Datenbank ein, indem Sie den folgenden Code zu <i>/bitrix/php_interface/after_connect.php</i> hinzufügen:

<code>\$DB->Query(&quot;SET LOCAL time_zone='Europe/Berlin'&quot;);</code> (geben Sie Ihre Region und Stadt ein).



Unter http://en.wikipedia.org/wiki/List_of_tz_database_time_zones finden Sie eine Liste der standardmäßigen Regionen und Städte.
";
$MESS["SC_HELP_CHECK_MYSQL_MODE"] = "Der Parameter  <i>sql_mode</i> bestimmt den Arbeitsmodus von MySQL. Beachten Sie, dass dieser Parameter auch die Werte akzeptiert, welche mit Bitrix Lösungen nicht kompatibel sind. Fügen Sie den folgenden Code zu <i>/bitrix/php_interface/after_connect.php</I> hinzu, um den Standardmodus einzustellen:

<code>\$DB->Query(&quot;SET sql_mode=''&quot;);</code>
";
$MESS["SC_HELP_CHECK_MYSQL_INCREMENT"] = "Hier wird der Wert des Parameters auto_increment_increment (welcher den Abstand zwischen den aufeinanderfolgenden Spaltenwerten kontrolliert) geprüft, indem eine Testtabelle erstellt und zwei Einträge dazu hinzugefügt werden. Der Wert muss auf 1 gesetzt werden. Bei anderen Werten fügen Sie den folgenden Code zu <i>/bitrix/php_interface/after_connect.php</i> hinzu:

<code>\$DB->Query(&quot;SET @@auto_increment_increment=1&quot;);</code>
";
$MESS["SC_HELP_CHECK_MYSQL_TABLE_CHARSET"] = "Die Zeichenkodierung aller Tabellen und Felder muss mit der Zeichenkodierung der datenbank übereinstimmen. Unterscheidet sich die Zeichenkodierung von irgendeiner der Tabellen, müssen Sie dies manuell mithilfe von SQL-Befehlen korrigieren.



Die alphabetische Sortierung der Tabellen muss mit der alphabetischen Sortierung der Datenbank übereinstimmen. Sind die Zeichenkodierungen korrekt konfiguriert, dann wird das Nichtübereinstimmen der alphabetischen Sortierungen automatisch korrigiert.



<b>Achtung!</b> Erstellen Sie immer eine komplette Sicherungskopie von der Datenbank, bevor Sie die Zeichenkodierung ändern.
";
$MESS["SC_HELP_CHECK_MYSQL_TABLE_STATUS"] = "In diesem Test werden die MySQL-üblichen Mechanismen zur Tabellenprüfung verwendet. Wird der Test eine oder mehrere beschädigte Tabellen feststellen, wird Ihnen vorgeschlagen, Korrekturen vorzunehmen.";
$MESS["SC_HELP_CHECK_MYSQL_DB_CHARSET"] = "Bei diesem Test wird geprüft, ob die Zeichenkodierung und alphabetische Sortierung der Datenbank mit denen der Verbindung übereinstimmen. MySQL verwendet diese Parameter, um neue Tabellen zu erstellen.



Solche Fehler, falls sie auftreten, können automatisch korrigiert werden, wenn der aktuelle Nutzer Schreibrechte für die Datenbank hat (ALTER DATABASE).
";
$MESS["SC_HELP_CHECK_MYSQL_CONNECTION_CHARSET"] = "Bei diesem Test werden die Zeichenkodierung und die alphabetische Sortierung geprüft, die vom System verwendet werden, wenn Daten an den MySQL-Server gesendet werden.



Benutzt Ihre Website <i>UTF-8</I>, muss die Zeichenkodierung auf <i>utf8</I> und die alphabetische Sortierung auf <i>utf8_unicode_ci</i> gesetzt werden. Benutzt die Website <i>iso-8859-1</i>, muss die Verbindung dieselbe Zeichenkodierung benutzen.



Um die Zeichenkodierung der Verbindung zu ändern (sie beispielsweise auf UTF-8 zu setzen), fügen Sie den folgenden Code zu <i>/bitrix/php_interface/after_connect.php</i> hinzu:

<code>\$DB->Query('SET NAMES &quot;utf8&quot;');</code>



Um die alphabetische Sortierung zu ändern, fügen Sie den Code <b>after the charset declaration</b> hinzu:

<code>\$DB->Query('SET collation_connection = &quot;utf8_unicode_ci&quot;');</code>



<b>Achtung!</b> Nachdem Sie neue Werte gesetzt haben, stellen Sie sicher, dass Ihre Website ordentlich funktioniert.
";
$MESS["SC_HELP_CHECK_BACKTRACK_LIMIT"] = "In PHP ist ein Parameter enthalten, mit dem die maximale Länge der Zeile bei der Arbeit mit regulären Ausdrücken bestimmt wird: <i>pcre.backtrack_limit</i>.

Grundsätzlich sollten Sie diesen Wert mit dem Aufruf von <i>ini_set</i> erhöhen. Sonst können Fehler bei der Arbeit des Web-Antivirus und des Visuellen Editors auftreten. Auch beim E-Mailversand sowie Ausführen anderer Funktionen sind Probleme zu erwarten.
";
$MESS["SC_T_UPLOAD_RAW"] = "Datei hochladen via php://input";
$MESS["SC_HELP_CHECK_UPLOAD_RAW"] = "Sendet Binärdaten im Körper einer POST-Anfrage. Auf der Serverseite können diese Daten manchmal beschädigt werden: In diesem Fall wird der Flach-Lader für Bilder nicht funktionieren.";
?>