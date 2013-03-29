<?
$MESS["VULNSCAN_SIMILAR"] = "Analog";
$MESS["VULNSCAN_REQUIRE"] = "Erforderliche Bedingungen";
$MESS["VULNSCAN_FILE"] = "Datei";
$MESS["VULNSCAN_XSS_NAME"] = "Cross-Site Scripting";
$MESS["VULNSCAN_XSS_HELP"] = "Internet-Kriminellen k�nnen einen sch�dlichen HTML/JS Code im Browser des Opfers ausf�hren. Es wird empfohlen, Variablen zu filtern, bevor sie 
im HTML/JS Code angezeigt werden.<br> N�heres dazu: <a href=\"https://www.owasp.org/index.php/Cross-site_Scripting_(XSS)\">https://www.owasp.org/index.php/Cross-site_(XSS)</a>
";
$MESS["VULNSCAN_HEADER_NAME"] = "HTTP Response Splitting";
$MESS["VULNSCAN_HEADER_HELP"] = "Mit der Eingliederung einer eigenen �berschrift in die HTTP-Antwort k�nnen Internet-Kriminelle angreifen, indem sie HTML/JS Code umleiten oder einen sch�dlichen Code einf�gen. Es wird empfohlen, Zeilen�bersetzungen zu filtern, bevor sie in der Antwort�berschrift angezeigt werden. G�ltig f�r PHP < 5.4. N�heres dazu: <a href=\"http://www.infosecwriters.com/text_resources/pdf/HTTP_Response.pdf\">http://www.infosecwriters/pdf/HTTP_Response.pdf</a>";
$MESS["VULNSCAN_DATABASE_NAME"] = "SQL Injection";
$MESS["VULNSCAN_DATABASE_HELP"] = "Internet-Kriminelle k�nnen sch�dliche SQL Befehle an den Server senden. Das ist sehr gef�hrlich, es wird empfohlen, Nutzerdaten zu filtern, bevor sie genutzt werden. N�heres dazu:  <a href=\"https://www.owasp.org/index.php/SQL_Injection\">https://www.owasp.org/index.php/SQL_Injection</a>";
$MESS["VULNSCAN_INCLUDE_NAME"] = "File Inclusion";
$MESS["VULNSCAN_INCLUDE_HELP"] = "Internet-Kriminelle k�nnen lokale/entfernte Dateien anbinden oder Dateien der Website ablesen. Es wird empfohlen, Pfadnormalisierung in den Nutzerdaten durchzuf�hren, bevor sie genutzt werden. N�heres dazu: <a href=\"https://rdot.org/forum/showthread.php?t=343\">https://rdot.org/forum/showthread.php?t=343</a>";
$MESS["VULNSCAN_EXEC_NAME"] = "Ausf�hrung willk�rlicher Befehle";
$MESS["VULNSCAN_EXEC_HELP"] = "Internet-Kriminelle k�nnen eillk�rliche Systembefehle ausf�hren, das ist sehr gef�hrlich. N�heres dazu:  <a href=\"https://www.owasp.org/index.php/Code_Injection\">https://www.owasp.org/index.php/Code_Injection</a>";
$MESS["VULNSCAN_CODE_NAME"] = "Ausf�hrung eines willk�rlichen Codes";
$MESS["VULNSCAN_CODE_HELP"] = "Internet-Kriminelle k�nnen willk�rlichen sch�dlichen PHP-Code einf�gen und ausf�hren. N�heres dazu: <a href=\"http://cwe.mitre.org/data/definitions/78.html\">http://cwe.mitre.org/data/definitions/78.html</a>";
$MESS["VULNSCAN_POP_NAME"] = "Daten-Serialisierung";
$MESS["VULNSCAN_POP_HELP"] = "Eine Deserialisierung der Nutzerdaten kann ziemlich gef�hrlich sein. N�heres dazu: <a href=\"https://rdot.org/forum/showthread.php?t=950\">https://rdot.org/forum/showthread.php?t=950</a>";
$MESS["VULNSCAN_OTHER_NAME"] = "M�gliche �nderung der Systemlogik";
$MESS["VULNSCAN_OTHER_HELP"] = "Keine Beschreibung";
$MESS["VULNSCAN_UNKNOWN"] = "M�gliche Sicherheitsl�cke";
$MESS["VULNSCAN_UNKNOWN_HELP"] = "Keine Beschreibung";
$MESS["VULNSCAN_HELP_INPUT"] = "Quelle";
$MESS["VULNSCAN_HELP_FUNCTION"] = "Funktion/Methode";
$MESS["VULNSCAN_HELP_VULNTYPE"] = "Typ der Sicherheitsl�cke";
$MESS["VULNSCAN_FIULECHECKED"] = "Dateien gepr�ft: ";
$MESS["VULNSCAN_VULNCOUNTS"] = "M�gliche Probleme festgestellt: ";
$MESS["VULNSCAN_DYNAMIC_FUNCTION"] = "Dynamische Funktion aufrufen.";
$MESS["VULNSCAN_EXTRACT"] = "Die fr�her initialisierten Variablen k�nnen �berschrieben werden.";
$MESS["VULNSCAN_TOKENIZER_NOT_INSTALLED"] = "PHP-tokenizer ist nicht aktiviert. Bitte aktivieren Sie ihn, um den Test abzuschlie�en.";
$MESS["VULNSCAN_XSS_HELP_SAFE"] = "<b>htmlspecialcharsbx</b> benutzen. Werte der Tag-Attribute sind immer mit doppelten Anf�hrungszeichen anzugeben. Das Protokoll (http) zwingend in Attribut-Werten href und src angeben falls erforderlich.";
$MESS["VULNSCAN_HEADER_HELP_SAFE"] = "Neue Zeilen sollten herausgefiltert werden, bevor Text zum Header hinzugef�gt wird.";
$MESS["VULNSCAN_DATABASE_HELP_SAFE"] = "F�r numerische Daten explizite Datentypen benutzen (int, float etc.). Benutzen Sie mysql_escape_string, \$DB->ForSQL() u.�. f�r Zeilendaten.";
$MESS["VULNSCAN_INCLUDE_HELP_SAFE"] = "Pfade vor der Nutzung normalisieren.";
$MESS["VULNSCAN_EXEC_HELP_SAFE"] = "�berpr�fen, dass die Variablenwerte g�ltig und in einem erlaubten Bereich sind. Sie k�nnen z.B. die Angabe von nationalen Alphabet Zeichen verbieten. Der erlaubte Bereich wird durch Projektanforderungen bestimmt. Benutzen Sie escapeshellcmd und escapeshellarg zur besseren Sicherheit.";
$MESS["VULNSCAN_CODE_HELP_SAFE"] = "Nutzereingabe mit <b>EscapePHPString</b> filtern.";
$MESS["VULNSCAN_HELP_SAFE"] = "Bleiben Sie gesch�tzt";
?>