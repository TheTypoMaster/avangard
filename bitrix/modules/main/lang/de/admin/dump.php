<?
$MESS["MAIN_DUMP_FILE_CNT"] = "Dateien komprimiert:";
$MESS["MAIN_DUMP_FILE_SIZE"] = "Dateigr��e:";
$MESS["MAIN_DUMP_FILE_FINISH"] = "Backup ist vollst�ndig";
$MESS["MAIN_DUMP_FILE_MAX_SIZE"] = "Dateien ausschlie�en, die gr��er sind als (0 - ohne Begrenzung):";
$MESS["MAIN_DUMP_FILE_STEP"] = "Schritt:";
$MESS["MAIN_DUMP_FILE_STEP_SLEEP"] = "Interval:";
$MESS["MAIN_DUMP_FILE_STEP_sec"] = "Sekunden";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_b"] = "B";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_kb"] = "KB";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_mb"] = "MB ";
$MESS["MAIN_DUMP_FILE_MAX_SIZE_gb"] = "GB ";
$MESS["MAIN_DUMP_FILE_DUMP_BUTTON"] = "Archivieren";
$MESS["MAIN_DUMP_FILE_STOP_BUTTON"] = "Stop";
$MESS["MAIN_DUMP_FILE_KERNEL"] = "Kernel archivieren:";
$MESS["MAIN_DUMP_FILE_NAME"] = "Name";
$MESS["FILE_SIZE"] = "Dateigr��e";
$MESS["MAIN_DUMP_FILE_TIMESTAMP"] = "Ge�ndert";
$MESS["MAIN_DUMP_FILE_PUBLIC"] = "�ffentlichen Teil archivieren:";
$MESS["MAIN_DUMP_FILE_TITLE"] = "Dateien";
$MESS["MAIN_DUMP_BASE_STAT"] = "Statistik";
$MESS["MAIN_DUMP_BASE_SINDEX"] = "Suchindex";
$MESS["MAIN_DUMP_BASE_IGNORE"] = "Aus dem Archiv ausschlie�en:";
$MESS["MAIN_DUMP_BASE_TRUE"] = "Datenbank archivieren:";
$MESS["MAIN_DUMP_BASE_TITLE"] = "Datenbank";
$MESS["MAIN_DUMP_BASE_SIZE"] = "MB";
$MESS["MAIN_DUMP_PAGE_TITLE"] = "Backup";
$MESS["MAIN_DUMP_TAB"] = "Backup";
$MESS["MAIN_DUMP_SITE_PROC"] = "Komprimieren...";
$MESS["MAIN_DUMP_ARC_SIZE"] = "Archivgr��e:";
$MESS["MAIN_DUMP_TABLE_FINISH"] = "Tabellen bearbeitet:";
$MESS["MAIN_DUMP_ACTION_DOWNLOAD"] = "Download";
$MESS["MAIN_DUMP_DELETE"] = "L�schen";
$MESS["MAIN_DUMP_ALERT_DELETE"] = "Wollen Sie die Datei wirklich l�schen?";
$MESS["MAIN_DUMP_FILE_PAGES"] = "Backup-Kopie";
$MESS["MAIN_RIGHT_CONFIRM_EXECUTE"] = "Achtung! Das Entpacken der Sicherungskopie auf einer funktionierenden Seite kann das Archiv besch�digen! Wollen Sie fortfahren?";
$MESS["MAIN_DUMP_RESTORE"] = "Entpacken";
$MESS["MAIN_DUMP_ENCODE"] = "Achtung! Sie benutzten eine codierte Produktversion";
$MESS["MAIN_DUMP_MYSQL_ONLY"] = "Das Backupsystem arbeitet nur mit MySQL Datenbankdaten.<br>Bitte verwenden Sie externe Tools um eine Datenbankkopie zu erstellen.";
$MESS["MAIN_DUMP_HEADER_MSG"] = "Um das Seitenarchiv zum anderen Host zu �bertragen, f�gen Sie bitte im Root-Verzeichnis der neuen Seite das Wiederherstellungsskript <a href='/bitrix/admin/restore_export.php'>restore.php</a> und das Archiv selbst ein. Dann geben Sie in der Browser-Zeile &quot;&lt;Seitenname&gt;/restore.php&quot; und folgen Sie den Instruktionen.";
$MESS["MAIN_DUMP_SKIP_SYMLINKS"] = "Symbolische Links zu Verzeichnissen �berspringen:";
$MESS["MAIN_DUMP_MASK"] = "Dateien und Verzeichnisse aus dem Archiv ausschlie�en (nach Maske):";
$MESS["MAIN_DUMP_MORE"] = "Mehr...";
$MESS["MAIN_DUMP_FOOTER_MASK"] = "F�r die Ausschlussmaske gelten folgenden Regeln:
<p>
 <li>Die Maskenvorlage kann Symbole &quot;*&quot; beinhalten, die einem beliebigen Anzahl verschiedener Symbole, die im Datei- oder Ordnernamen enthalten sind, entsprechen;</li>
 <li>Wenn am Anfang ein Slash oder ein Backslash steht (&quot;/&quot; oder &quot;\\&quot;), gilt der Pfad vom Root-Verzeichnis aus;</li>
 <li>Im Gegenfall wird die Vorlage auf jede Datei und jeden Ordner angewendet;</li>
 <p>Vorlagenbeispiele:</p>
 <li>/content/photo - Ausschlu� des ganzen Ordners /content/photo;</li>
 <li>*.zip - Ausschlu� aller Dateien mit der Dateiendung &quot;zip&quot;;</li>
 <li>.access.php - Ausschlu� aller Dateien &quot;.access.php&quot;;</li>
 <li>/files/download/*.zip - Ausschlu� aller Dateien mit der Dateiendung &quot;zip&quot; im /files/download Ordner;</li>
 <li>/files/d*/*.ht* - Ausschlu� der Dateien aus den Ordner, die mit &quot;/files/d&quot anfangen und Dateiendungen, die mit &quot;ht&quot; anfangen.</li>";
$MESS["MAIN_DUMP_ERROR"] = "Fehler";
$MESS["DUMP_NO_PERMS"] = "Keine Berechtigung auf dem Server f�r Archiverstellung";
$MESS["DUMP_NO_PERMS_READ"] = "Fehler beim �ffnen des Archivs zum Lesen";
$MESS["DUMP_DB_CREATE"] = "Dumperstellung f�r die Datenbank";
$MESS["DUMP_CUR_PATH"] = "Aktueller Pfad:";
$MESS["INTEGRITY_CHECK"] = "Integrit�tspr�fung";
$MESS["CURRENT_POS"] = "Aktuelle Position:";
$MESS["TAB_STANDARD"] = "Standard";
$MESS["TAB_STANDARD_DESC"] = "Standardparameter der Durchf�hrung der Datensicherung";
$MESS["TAB_ADVANCED"] = "Erweiterte";
$MESS["TAB_ADVANCED_DESC"] = "Spezialparameter der Durchf�hrung der Datensicherung";
$MESS["MODE_DESC"] = "Es wird ein komplettes Archiv des �ffentlichen Teils   <b>der aktuellen Website</b> (f�r eine Multisitekonfiguration auf verschiedenen Dom�nen), <b>des Systemkernels</b> und <b>der Datenbank</b> (nur f�r MySQL) erstellt, welches das System komplett wiederherstellen und auf einen anderen Server �bertragen kann. W�hlen Sie den gew�nschten Modus aus und, wenn erforderlich, stellen Sie zus�tzliche Parameter auf der Registerkarte &quot;<b>Erweiterte</b>&quot ; ein.";
$MESS["MODE_VPS"] = "Extraserver oder VPS (zeitoptimal)";
$MESS["MODE_SHARED"] = "Standardhosting (geeignet f�r die meisten Websites)";
$MESS["MODE_SLOW"] = "Sicherer Modus (diesen benutzen, wenn andere Modi nicht funktionieren: ohne Komprimierung, mit Pausen zwischen den Schritten)";
$MESS["PUBLIC_PART"] = "�ffentlicher Teil der Website:";
$MESS["SERVER_LIMIT"] = "Servereinschr�nkungen";
$MESS["STEP_LIMIT"] = "Schrittdauer:";
$MESS["DISABLE_GZIP"] = "Archivkomprimierung ausschalten (reduziert Prozessorbelastung):";
$MESS["INTEGRITY_CHECK_OPTION"] = "Wenn abgeschlossen, die Archivintegrit�t pr�fen:";
$MESS["MAIN_DUMP_DB_PROC"] = "Dump der Datenbank komprimieren";
$MESS["CDIR_FOLDER_ERROR"] = "Fehler beim Bearbeiten des Ordners: ";
$MESS["CDIR_FOLDER_OPEN_ERROR"] = "Fehler beim �ffnen des Ordners: ";
$MESS["CDIR_FILE_ERROR"] = "Fehler beim Bearbeiten der Datei:";
$MESS["BACKUP_NO_PERMS"] = "Keine Berechtigung f�r einen Eintrag in den Ordner /bitrix/backup";
$MESS["TIME_SPENT"] = "Verwendete Zeit:";
$MESS["TIME_H"] = "St.";
$MESS["TIME_M"] = "Min.";
$MESS["TIME_S"] = "Sek.";
?>
