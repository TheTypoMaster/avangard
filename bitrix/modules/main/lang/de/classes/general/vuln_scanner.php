<?
$MESS["VULNSCAN_SIMILAR"] = "Analog";
$MESS["VULNSCAN_REQUIRE"] = "Erforderliche Bedingungen";
$MESS["VULNSCAN_FILE"] = "Datei";
$MESS["VULNSCAN_XSS_NAME"] = "Cross-Site Scripting";
$MESS["VULNSCAN_XSS_HELP"] = "Internet-Kriminellen können einen schädlichen HTML/JS Code im Browser des Opfers ausführen. Es wird empfohlen, Variablen zu filtern, bevor sie 
im HTML/JS Code angezeigt werden.<br> Näheres dazu: <a href=\"https://www.owasp.org/index.php/Cross-site_Scripting_(XSS)\">https://www.owasp.org/index.php/Cross-site_(XSS)</a>
";
$MESS["VULNSCAN_HEADER_NAME"] = "HTTP Response Splitting";
$MESS["VULNSCAN_HEADER_HELP"] = "Mit der Eingliederung einer eigenen Überschrift in die HTTP-Antwort können Internet-Kriminelle angreifen, indem sie HTML/JS Code umleiten oder einen schädlichen Code einfügen. Es wird empfohlen, Zeilenübersetzungen zu filtern, bevor sie in der Antwortüberschrift angezeigt werden. Gültig für PHP < 5.4. Näheres dazu: <a href=\"http://www.infosecwriters.com/text_resources/pdf/HTTP_Response.pdf\">http://www.infosecwriters/pdf/HTTP_Response.pdf</a>";
$MESS["VULNSCAN_DATABASE_NAME"] = "SQL Injection";
$MESS["VULNSCAN_DATABASE_HELP"] = "Internet-Kriminelle können schädliche SQL Befehle an den Server senden. Das ist sehr gefährlich, es wird empfohlen, Nutzerdaten zu filtern, bevor sie genutzt werden. Näheres dazu:  <a href=\"https://www.owasp.org/index.php/SQL_Injection\">https://www.owasp.org/index.php/SQL_Injection</a>";
$MESS["VULNSCAN_INCLUDE_NAME"] = "File Inclusion";
$MESS["VULNSCAN_INCLUDE_HELP"] = "Internet-Kriminelle können lokale/entfernte Dateien anbinden oder Dateien der Website ablesen. Es wird empfohlen, Pfadnormalisierung in den Nutzerdaten durchzuführen, bevor sie genutzt werden. Näheres dazu: <a href=\"https://rdot.org/forum/showthread.php?t=343\">https://rdot.org/forum/showthread.php?t=343</a>";
$MESS["VULNSCAN_EXEC_NAME"] = "Ausführung willkürlicher Befehle";
$MESS["VULNSCAN_EXEC_HELP"] = "Internet-Kriminelle können eillkürliche Systembefehle ausführen, das ist sehr gefährlich. Näheres dazu:  <a href=\"https://www.owasp.org/index.php/Code_Injection\">https://www.owasp.org/index.php/Code_Injection</a>";
$MESS["VULNSCAN_CODE_NAME"] = "Ausführung eines willkürlichen Codes";
$MESS["VULNSCAN_CODE_HELP"] = "Internet-Kriminelle können willkürlichen schädlichen PHP-Code einfügen und ausführen. Näheres dazu: <a href=\"http://cwe.mitre.org/data/definitions/78.html\">http://cwe.mitre.org/data/definitions/78.html</a>";
$MESS["VULNSCAN_POP_NAME"] = "Daten-Serialisierung";
$MESS["VULNSCAN_POP_HELP"] = "Eine Deserialisierung der Nutzerdaten kann ziemlich gefährlich sein. Näheres dazu: <a href=\"https://rdot.org/forum/showthread.php?t=950\">https://rdot.org/forum/showthread.php?t=950</a>";
$MESS["VULNSCAN_OTHER_NAME"] = "Mögliche Änderung der Systemlogik";
$MESS["VULNSCAN_OTHER_HELP"] = "Keine Beschreibung";
$MESS["VULNSCAN_UNKNOWN"] = "Mögliche Sicherheitslücke";
$MESS["VULNSCAN_UNKNOWN_HELP"] = "Keine Beschreibung";
$MESS["VULNSCAN_HELP_INPUT"] = "Quelle";
$MESS["VULNSCAN_HELP_FUNCTION"] = "Funktion/Methode";
$MESS["VULNSCAN_HELP_VULNTYPE"] = "Typ der Sicherheitslücke";
$MESS["VULNSCAN_FIULECHECKED"] = "Dateien geprüft: ";
$MESS["VULNSCAN_VULNCOUNTS"] = "Mögliche Probleme festgestellt: ";
$MESS["VULNSCAN_DYNAMIC_FUNCTION"] = "Dynamische Funktion aufrufen.";
$MESS["VULNSCAN_EXTRACT"] = "Die früher initialisierten Variablen können überschrieben werden.";
$MESS["VULNSCAN_TOKENIZER_NOT_INSTALLED"] = "PHP-tokenizer ist nicht aktiviert. Bitte aktivieren Sie ihn, um den Test abzuschließen.";
$MESS["VULNSCAN_XSS_HELP_SAFE"] = "<b>htmlspecialcharsbx</b> benutzen. Werte der Tag-Attribute sind immer mit doppelten Anführungszeichen anzugeben. Das Protokoll (http) zwingend in Attribut-Werten href und src angeben falls erforderlich.";
$MESS["VULNSCAN_HEADER_HELP_SAFE"] = "Neue Zeilen sollten herausgefiltert werden, bevor Text zum Header hinzugefügt wird.";
$MESS["VULNSCAN_DATABASE_HELP_SAFE"] = "Für numerische Daten explizite Datentypen benutzen (int, float etc.). Benutzen Sie mysql_escape_string, \$DB->ForSQL() u.Ä. für Zeilendaten.";
$MESS["VULNSCAN_INCLUDE_HELP_SAFE"] = "Pfade vor der Nutzung normalisieren.";
$MESS["VULNSCAN_EXEC_HELP_SAFE"] = "Überprüfen, dass die Variablenwerte gültig und in einem erlaubten Bereich sind. Sie können z.B. die Angabe von nationalen Alphabet Zeichen verbieten. Der erlaubte Bereich wird durch Projektanforderungen bestimmt. Benutzen Sie escapeshellcmd und escapeshellarg zur besseren Sicherheit.";
$MESS["VULNSCAN_CODE_HELP_SAFE"] = "Nutzereingabe mit <b>EscapePHPString</b> filtern.";
$MESS["VULNSCAN_HELP_SAFE"] = "Bleiben Sie geschützt";
?>