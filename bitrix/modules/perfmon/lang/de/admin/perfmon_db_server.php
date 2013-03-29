<?
$MESS["PERFMON_DB_SERVER_TITLE"] = "Performance Monitor: DB-Server";
$MESS["PERFMON_STATUS_TITLE"] = "Serverstatistiken";
$MESS["PERFMON_WAITS_TITLE"] = "Statistik der Serverwartezeiten";
$MESS["PERFMON_PARAMETERS_TITLE"] = "Servereinstellungen";
$MESS["PERFMON_STATS_TITLE"] = "Statistik der Datenbankobjekte";
$MESS["PERFMON_KPI_NAME"] = "Name";
$MESS["PERFMON_KPI_VALUE"] = "Wert";
$MESS["PERFMON_KPI_RECOMENDATION"] = "Empfehlung";
$MESS["PERFMON_KPI_NAME_VERSION"] = "Version";
$MESS["PERFMON_KPI_REC_VERSION_OLD"] = "Ihre MySQL-Version ist veraltet. Bitte aktualisieren Sie diese so schnell wie m�glich.";
$MESS["PERFMON_KPI_REC_VERSION_OK"] = "Diese Version von MySQL wird von diesem Diagnose-Tool unterstutzt.";
$MESS["PERFMON_KPI_REC_VERSION_NEW"] = "Diese Version von MySQL wird nicht von diesem Diagnose-Tool unterstutzt. Die Ergebnisse k�nnen falsch sein.";
$MESS["PERFMON_KPI_NAME_UPTIME"] = "Zeit";
$MESS["PERFMON_KPI_VAL_UPTIME"] = "#DAYS#d #HOURS#h #MINUTES#m #SECONDS#s";
$MESS["PERFMON_KPI_REC_UPTIME_OK"] = "Laufzeit des MySQL Servers.";
$MESS["PERFMON_KPI_REC_UPTIME_TOO_SHORT"] = "Der MySQL-Server lief weniger als 24 Stunden. Die Empfehlungen k�nnen ungenau sein.";
$MESS["PERFMON_KPI_NAME_QUERIES"] = "Serveranfragen insgesamt";
$MESS["PERFMON_KPI_REC_NO_QUERIES"] = "Keine M�glichkeit zur Diagnose des Servers, der keine Anfragen bearbeitet.";
$MESS["PERFMON_KPI_NAME_GBUFFERS"] = "Globale Speicher";
$MESS["PERFMON_KPI_REC_GBUFFERS"] = "Gr��e des globalen Speichers (#VALUE#).";
$MESS["PERFMON_KPI_NAME_CBUFFERS"] = "Verbindungsspeicher";
$MESS["PERFMON_KPI_REC_CBUFFERS"] = "Speichergr��e einer Verbindung (#VALUE#).";
$MESS["PERFMON_KPI_NAME_CONNECTIONS"] = "Verbindungen";
$MESS["PERFMON_KPI_REC_CONNECTIONS"] = "Maximale Anzahl der Verbindungen (#VALUE#).";
$MESS["PERFMON_KPI_NAME_MEMORY"] = "Speicher";
$MESS["PERFMON_KPI_REC_MEMORY"] = "Maximale Verwendung des Speichers (Globaler Speicher + Verbindungsspeicher * Verbindungen ).<br>Vergewissern Sie sich, dass der Wert nicht gr��er ist als 85-90 Prozent des physischen Serverspeichers (ausschlie�lich der andere Prozesse).";
$MESS["PERFMON_KPI_NAME_MYISAM_IND"] = "MyISAM Index";
$MESS["PERFMON_KPI_REC_MYISAM_IND"] = "Gr��e der MyISAM Index.";
$MESS["PERFMON_KPI_REC_MYISAM_NOIND"] = "Keine MyISAM Index vorhanden.";
$MESS["PERFMON_KPI_REC_MYISAM4_IND"] = "Keine M�glichkeit zur Absch�tzung der Indexgr��e f�r MySQL der Version kleiner als 5.";
$MESS["PERFMON_KPI_NAME_KEY_MISS"] = "MyISAM Index Cache (Ausf�lle)";
$MESS["PERFMON_KPI_REC_KEY_MISS"] = "Wenn der Wert > 5% ist, erh�hen Sie den Wert des Parameters #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#)";
$MESS["PERFMON_KPI_NAME_QCACHE_SIZE"] = "Anfragen-Cache (Gr��e)";
$MESS["PERFMON_KPI_REC_QCACHE_ZERO_SIZE"] = "Schalten Sie das Anfragen-Caching ein (Setzen Sie #PARAM_NAME# auf #PARAM_VALUE_LOW# oder h�her, jedoch nicht h�her als #PARAM_VALUE_HIGH#).";
$MESS["PERFMON_KPI_REC_QCACHE_TOOLARGE_SIZE"] = "Die Gr��e des Anfrage-Caches (#PARAM_NAME#) ist h�her als #PARAM_VALUE_HIGH#. Dadurch kann die Performance verschlechtert werden.";
$MESS["PERFMON_KPI_REC_QCACHE_OK_SIZE"] = "Gr��e des Anfrage-Caches (#PARAM_NAME#).";
$MESS["PERFMON_KPI_NAME_QCACHE"] = "Anfragen-Cache (Effizienz)";
$MESS["PERFMON_KPI_REC_QCACHE_NO"] = "Anfragen-Cache wird nicht verwendet, weil keine SELECT Anfragen vorhanden sind.";
$MESS["PERFMON_KPI_REC_QCACHE"] = "Wenn die Effizienz der Caching-Nutzung kleiner als #GOOD_VALUE# ist, ist es m�glicherweise erforderlich den Wert des Parameters #PARAM_NAME# zu erh�hen (Aktueller Wert: #PARAM_VALUE#)";
$MESS["PERFMON_KPI_NAME_QCACHE_PRUNES"] = "Anfragen-Cache (Verdr�ngungen)";
$MESS["PERFMON_KPI_REC_QCACHE_PRUNES"] = "Anzahl der Anfragen, die aus dem Speicher ausgedr�ngt wurde (#STAT_NAME#). Wenn der Wert schnell w�chst, erh�hen Sie den Wert des Parameters #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#), jedoch nicht gr��er als #PARAM_VALUE_HIGH#.";
$MESS["PERFMON_KPI_NAME_SORTS"] = "Sortierungen";
$MESS["PERFMON_KPI_REC_SORTS"] = "Gesamtzahl der Sortierungen (#STAT_NAME#).";
$MESS["PERFMON_KPI_NAME_SORTS_DISK"] = "Sortierungen (Festplatte)";
$MESS["PERFMON_KPI_REC_SORTS_DISK"] = "Anteil der Sortierungen zu deren Ausf�hrung eine tempor�re Tabelle erstellt werden musste (#STAT_NAME#). Wenn der Wert h�her als #GOOD_VALUE# ist, erh�hen Sie die Parameter #PARAM1_NAME# (Aktueller Wert: #PARAM1_VALUE#) ? #PARAM2_NAME# (Aktueller Wert: #PARAM2_VALUE#).";
$MESS["PERFMON_KPI_NAME_JOINS"] = "Select_range_check + Select_full_join";
$MESS["PERFMON_KPI_REC_JOINS"] = "Anzahl der Tabellenverbindungen, die kein Index verwenden. (#STAT_NAME#). Wenn der Wert gro� ist, erh�hen Sie den Wert des Parameters #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#) oder f�gen Sie Index f�r Tabellenverbindungen hinzu.";
$MESS["PERFMON_KPI_NAME_TMP_DISK"] = "Tempor�re Tabellen (Festplatte)";
$MESS["PERFMON_KPI_REC_TMP_DISK_1"] = "Anteil der tempor�ren Tabellen, f�r die die Erstellung auf der Festplatte (#STAT_NAME#) erforderlich ist. Der Anteil ist gr��er als #STAT_VALUE#, und die Parameter #PARAM1_NAME# (Aktueller Wert: #PARAM1_VALUE#) und #PARAM2_NAME# (Aktueller Wert: #PARAM2_VALUE#) m�ssen erh�ht werden. M�glicherweise ist es erforderlich, die Anzahl von SELECT DISTINCT Anfragen ohne LIMIT zu reduzieren.";
$MESS["PERFMON_KPI_REC_TMP_DISK_2"] = "Anteil der tempor�ren Tabellen, f�r die die Erstellung auf der Festplatte (#STAT_NAME#) erforderlich ist. Anteil ist h�her als #STAT_VALUE# und die Gr��e der tempor�ren Tabelle ziemlich gro�. M�glicherweise ist es erforderlich, die Anzahl von SELECT DISTINCT Anfragen ohne LIMIT zu reduzieren.";
$MESS["PERFMON_KPI_REC_TMP_DISK_3"] = "Anteil der tempor�ren Tabellen, f�r die die Erstellung auf der Festplatte (#STAT_NAME#) erforderlich ist,  ist ziemlich klein (nicht mehr als #STAT_VALUE#).";
$MESS["PERFMON_KPI_NAME_THREAD_CACHE"] = "Thread-Cache";
$MESS["PERFMON_KPI_REC_THREAD_NO_CACHE"] = "Thread-Cache(#PARAM_NAME#)  ist abgeschaltet. Stellen Sie den Wert dieses Parameters gleich #PARAM_VALUE# ein.";
$MESS["PERFMON_KPI_REC_THREAD_CACHE"] = "Effizienz des Thread-Caches (#STAT_NAME#).  Wenn dieser Wert kleiner als #GOOD_VALUE# ist, vergr��ern Sie den Wert des Parameters #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#).";
$MESS["PERFMON_KPI_NAME_TABLE_CACHE"] = "Cache der offenen Tabellen";
$MESS["PERFMON_KPI_REC_TABLE_CACHE"] = "Effizienz des Cachings der offenen Tabellen (#STAT_NAME#). Wenn der Wert kleiner ist als #GOOD_VALUE#, erh�hen Sie den Wert des Parameters #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#). Erh�hen Sie den Parameter schrittweise, um die �berschreitung der Limits auf Anzahl der gleichzeitig ge�ffneten Dateien zu vermeiden.";
$MESS["PERFMON_KPI_NAME_OPEN_FILES"] = "Offene Dateien";
$MESS["PERFMON_KPI_REC_OPEN_FILES"] = "Anzahl der offenen Dateien (#STAT_NAME#). Wenn der Wert gr��er ist als #GOOD_VALUE#, erh�hen Sie den Wert #PARAM_NAME# (Aktueller Wert: #PARAM_VALUE#).";
$MESS["PERFMON_KPI_NAME_LOCKS"] = "Blockierungen";
$MESS["PERFMON_KPI_REC_LOCKS"] = "Anteil der Blockierungen ohne Warteschleife (#STAT_NAME#). Wenn der Wert kleiner #GOOD_VALUE# als ist, m�ssen Sie Ihre Anfragen optimieren und InnoDB verwenden.";
$MESS["PERFMON_KPI_NAME_INSERTS"] = "Gleichzeitiges Einf�gen";
$MESS["PERFMON_KPI_REC_INSERTS"] = "Gleichzeigtiges Einf�gen ist ausgeschaltet. Schalten Sie diese mit Hilfe des Parameters #PARAM_NAME# = #REC_VALUE#.";
$MESS["PERFMON_KPI_NAME_CONN_ABORTS"] = "Verbindungsabbr�che";
$MESS["PERFMON_KPI_REC_CONN_ABORTS"] = "Anzahl der Verbindungen, die nicht korrekt geschlossen wurden. Wenn dieser Wert 5% �bersteigt, muss die Anwendung berichtigt werden.";
$MESS["PERFMON_KPI_NAME_INNODB_BUFFER"] = "InnoDB Buffer";
$MESS["PERFMON_KPI_REC_INNODB_BUFFER"] = "Die Effizienz des InnoDB Buffers (#STAT_NAME#). Wenn der Wert kleiner ist als #GOOD_VALUE# ist, ist es empfohlen den Wert des Parameters #PARAM_NAME# zu erh�hen (Aktueller Wert: #PARAM_VALUE#).";
$MESS["PERFMON_KPI_REC_INNODB_FLUSH_LOG"] = "Der Wert #PARAM_NAME# muss gr��er als #GOOD_VALUE# sein.";
$MESS["PERFMON_KPI_REC_INNODB_FLUSH_METHOD"] = "Der Wert #PARAM_NAME# muss gr��er als #GOOD_VALUE# sein.";
$MESS["PERFMON_KPI_REC_TX_ISOLATION"] = "Der Wert #PARAM_NAME# muss gr��er als #GOOD_VALUE# sein.";
$MESS["PERFMON_KPI_EMPTY"] = "leer";
$MESS["PERFMON_KPI_NO"] = "Nein";
$MESS["PERFMON_KPI_NAME_INNODB_LOG_WAITS"] = "Anzahl der Protokollpuffer-Wartezeiten";
$MESS["PERFMON_KPI_REC_INNODB_LOG_WAITS"] = "Wenn gr��er als Null und w�chst, erh�hen Sie den Wert von <span class=\"perfmon_code\">innodb_log_file_size</span> (Aktueller Wert: #VALUE#). Wichtig! Stoppen Sie zuerst den Server, dann Sie MySQL. �ndern Sie den Wert in der Konfigurationsdatei. Speichern Sie die bestehende Datei in einem tempor�ren Ordner. Starten Sie den Server. Wenn keine Fehler auftreten, l�schen Sie die alten Protokolldateien.";
$MESS["PERFMON_KPI_NAME_BINLOG"] = "Binlog_cache_disk_use";
$MESS["PERFMON_KPI_REC_BINLOG"] = "Erh�hen Sie den Wert <span class=\"perfmon_code\">binlog_cache_size</span>, wenn die Zahl gr��er als Null ist (Aktueller Wert: #VALUE#)";
$MESS["PERFMON_WAIT_EVENT"] = "Serverwartezeitenereignisse";
$MESS["PERFMON_WAIT_PCT"] = "Prozentzahl der Gesamtzeit";
$MESS["PERFMON_WAIT_AVERAGE_WAIT_MS"] = "Durchschnittliche Ereigniszeit (ms)";
$MESS["PERFMON_PARAMETER_NAME"] = "Parameter";
$MESS["PERFMON_PARAMETER_VALUE"] = "Aktueller Wert";
$MESS["PERFMON_REC_PARAMETER_VALUE"] = "Empfohlener Wert";
$MESS["PERFMON_KPI_ORA_PERMISSIONS"] = "Nicht gen�gend Rechte um Statistik anzuzeigen. Erforderlich: <span class=\"perfmon_code\">SELECT ANY DICTIONARY</span>.";
$MESS["PERFMON_KPI_ORA_REC_DB_FILE_SEQUENTIAL_READ"] = "Gutes Ereignis. Wenn die durchschnittliche Lesezeit gr��er als 10 ms ist, betrachten Sie die Leistung des Ein-/Ausgang-Untersystems und die Gr��e des Cache-Puffers <span class=\"perfmon_code\">SGA</span>. Die Sitzung wartet auf das Ende des nacheinander folgenden Lesens.";
$MESS["PERFMON_KPI_ORA_REC_DB_FILE_SCATTERED_READ"] = "Gutes Ereignis. Wenn die durchschnittliche Lesezeit gr��er als 10 ms ist, betrachten Sie die Leistung des Ein-/Ausgang-Untersystems und die Gr��e des Cache-Puffers <span class=\"perfmon_code\">SGA</span>. Die Sitzung wartet auf das Ende des nacheinander folgenden Lesens, die f�r mehrere Bl�cke auf einmal aufgef�hrt wird.";
$MESS["PERFMON_KPI_ORA_REC_ENQ__TX___ROW_LOCK_CONTENTION"] = "Die Sitzungslinienf�hrung ist erforderlich. Am Anfang der Transaktion wurde die exklusive Blockierung erhalten, und sie wird bis zur Ende der Transaktion beibehalten.";
$MESS["PERFMON_KPI_ORA_REC_LOG_FILE_SYNC"] = "Gutes Ereignis. Wenn die durchschnittliche Lesezeit gr��er als 10 ms ist, betrachten Sie die Leistung des Ein-/Ausgang-Untersystems und den Wert the <span class=\"perfmon_code\">COMMIT_WRITE</span>. Diese Wartezeit beinhaltet die Eintragung in das Pufferprotokoll und die Ausf�hrung.";
$MESS["PERFMON_KPI_ORA_REC_LATCH__CACHE_BUFFERS_CHAINS"] = "Permanenter Zugriff auf den Datenblock (oder einer kleinen Anzahl von Blocks), bekannt als \"Hot Block\".";
$MESS["PERFMON_KPI_ORA_REC_LATCH__LIBRARY_CACHE"] = "Bitte beachten Sie den Wert span class=\"perfmon_code\">CURSOR_SHARING</span>, die Objektstatistik, sowie den Wert <span class=\"perfmon_code\">METHOD_OPT</span> bei der Statistikerfassung.  Die Sitzung versucht die getrennte Blockierung zu bekommen, die von einer anderen Sitzung beansprucht wird.";
$MESS["PERFMON_KPI_ORA_REC_ENQ__TX___INDEX_CONTENTION"] = "Die Sitzungslinienf�hrung ist erforderlich. Am Anfang der Transaktion wurde die exklusive Blockierung erhalten, und sie wird bis zur Ende der Transaktion beibehalten.";
$MESS["PERFMON_KPI_ORA_REC_LOG_FILE_SWITCH_COMPLETION"] = "Bezieht sich auf die Ein-/Ausgangsgeschwindigkeit. Erwartung der Protokollumschaltung.";
$MESS["PERFMON_KPI_ORA_REC_SQL_NET_MORE_DATA_FROM_CLIENT"] = "Bezieht sich auf die Geschwindigkeit der Client-Server-Nachrichten. Server hat eine Nachricht an den Client gesendet, dieser hat aber die vorherige nicht beantwortet.";
$MESS["PERFMON_KPI_ORA_REC_LATCH__SHARED_POOL"] = "Bitte beachten Sie den Wert span class=\"perfmon_code\">CURSOR_SHARING</span>, die Objektstatistik, sowie den Wert <span class=\"perfmon_code\">METHOD_OPT</span> bei der Statistikerfassung.  Die Sitzung versucht die getrennte Blockierung zu bekommen, die von einer anderen Sitzung beansprucht wird.";
$MESS["PERFMON_KPI_ORA_REC_CURSOR__PIN_S_WAIT_ON_X"] = "Bitte beachten Sie den Wert span class=\"perfmon_code\">CURSOR_SHARING</span>, die Objektstatistik, sowie den Wert <span class=\"perfmon_code\">METHOD_OPT</span> bei der Statistikerfassung.  Die Sitzung versucht die getrennte Blockierung zu bekommen, die von einer anderen Sitzung beansprucht wird.";
$MESS["PERFMON_KPI_ORA_REC_BUFFER_BUSY_WAITS"] = "Kann durch niedrige Ein-/Ausgangsgeschwindigkeit verursacht werden. Wartezeit, bis der Block zug�nglich wird. Oder der Block wird von einer anderen Sitzung gelesen, oder wird gerade ver�ndert.";
$MESS["PERFMON_KPI_ORA_REC_READ_BY_OTHER_SESSION"] = "Kann durch niedrige Ein-/Ausgangsgeschwindigkeit verursacht werden. Dies tritt auf, wenn die Sitzung einen Block anfragt, der bereits im Puffer-Cache einer anderen Sitzung gelesen wird.";
$MESS["PERFMON_KPI_ORA_REC_EVENTS_IN_WAITCLASS_OTHER"] = "Bitrix / Oracle Supportproblem.";
$MESS["PERFMON_KPI_ORA_REC_ROW_CACHE_LOCK"] = "Kann durch niedrige Ein-/Ausgangsgeschwindigkeit verursacht werden. Sitzung versucht die Metadaten zu blockieren.";
$MESS["PERFMON_KPI_ORA_REC_DB_FILE_PARALLEL_READ"] = "Kann durch niedrige Ein-/Ausgangsgeschwindigkeit verursacht werden.";
$MESS["PERFMON_KPI_ORA_REC_DB_BLOCK_CHECKSUM"] = "F�r die Geschwindigkeitserh�hung der <span class=\"perfmon_code\">DBWR</span>-Operationen.";
$MESS["PERFMON_KPI_ORA_REC_SESSION_CACHED_CURSORS"] = "Empirischer Wert f�r BITRIX Anwendung";
$MESS["PERFMON_KPI_ORA_REC_CURSOR_SHARING_FORCE"] = "Typisch f�r HTTP/PHP-Anwendungen ohne Verwendung von Bind Variablen";
$MESS["PERFMON_KPI_ORA_REC_PARALLEL_MAX_SERVERS"] = "Empirischer Wert f�r BITRIX Anwendung.";
$MESS["PERFMON_KPI_ORA_REC_COMMIT_WRITE"] = "F�r die Geschwindigkeitserh�hung der <span class=\"perfmon_code\">LGWR</span>-Operationen.";
$MESS["PERFMON_KPI_ORA_REC_OPEN_CURSORS"] = "Empirischer Wert f�r BITRIX Anwendung.";
$MESS["PERFMON_KPI_ORA_REC_OPTIMIZER_MODE"] = "Empirischer Wert f�r BITRIX Anwendung.";
$MESS["PERFMON_USER_NAME"] = "Schemaname";
$MESS["PERFMON_MIN_LAST_ANALYZED"] = "Zeit der �ltesten Schemaanalyse";
$MESS["PERFMON_MAX_LAST_ANALYZED"] = "Zeit der letzten Schemaanalyse";
$MESS["PERFMON_KPI_ORA_REC_STATS_NEW"] = "Die Statistik f�r die Benutzerobjekte  #USER_NAME# ist aktuell.";
$MESS["PERFMON_KPI_ORA_REC_STATS_OLD"] = "Achtung! Die Objektstatistik f�r den Benutzer  #USER_NAME# ist nicht aktuell. Es ist �u�erst empfehlenswert, die Benutzerstatistik mindestens ein Mal w�chentlich zu erfassen. Verwenden Sie dazu den Befehl <span class=\"perfmon_code\">BEGIN DBMS_STATS.GATHER_SCHEMA_STATS('#USER_NAME#', ESTIMATE_PERCENT=>NULL, CASCADE=>TRUE); END;</span>";
?>