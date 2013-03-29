<?
$MESS["cache_admin_note4"] = "<p>Es wird empfolen, für Seitenabschnitte, die selten geändert und größtenteils von nicht autorisierten Nutzer besucht werden, HTML-Caching zu verwenden. Folgende Prozesse werden verwendet, wenn der HTML-Cachespeicher aktiv ist: </p>
<ul style=\"font-size:100%\">
<li>HTML-Cachespeicher bearbeitet nur Seiten, die im Eingabeformular verzeichnet und nicht als Ausnahme gekennzeichnet sind;</li>
<li>Für nichtautorisierte Nutzer überprüft das System die Kopie der Seite, die im HTML-Cachespeicher gespeichert ist. Wenn die Seite im Cachespeicher gefunden wird, wird sie ohne Modulaufruf angezeigt. Der Nutzer wird z.B. nicht in der Statistik verfolgt. Werbung, Hauptmodul und andere Module werden ebenso nicht aufgerufen.;</li>
<li>Die Seite wird komprimiert übertragen, wenn das Kompressionsmodul im Moment der Cachespeicher-Generierung aktiviert war;</li>
<li> Wenn für die Seite kein Cachespeicher gefunden wird, wird sie auf normalem Weg bearbeitet. Nachdem die Seite fertig geladen ist, wird sie im HTML-Cachespeicher gespeichert;</li>
</ul>
<p>Bereinigen des Cachespeichers:</p>
<ul style=\"font-size:100%\">
<li>Wenn die Datenarchivierung den zugeteilten Speicherplatz überschreitet, wird der Cachespeicher komplett gelöscht;</li>
<li>Komplette Cachespeicher-Bereinigung erfolgt auch, wenn  Daten  geändert werden;</li>
<li>Wenn Daten von den öffentlichen Seiten der Seite gepostet werden (z.B das Hinzufügen von Kommentaren oder Abstimmungen) , werden nur damit verbundene Teile des Cachespeichers bereinigt;</li>
</ul>
<p>Bitte beachten Sie, dass wenn nichtautorisierte Nutzer Seiten, die nicht im Cachespeicher vorhanden sind, besuchen, eine Session gestartet wird und der HTML-Cachespeicher für sie nicht mehr funktioniert.</p>
<p>Wichtige Anmerkungen:</p>
<ul style=\"font-size:100%\">
<li>Statistiken werden deaktiviert</li>
<li>Werbemodule funktionieren, wenn eine Seite im HTML-Cache verfügbar ist. Beachten Sie, dass dies nicht für externe Werbemodule zutrifft (Google Ad Sense usw.)</li>
<li>Ergebnisse von Warenvergleichen werden für nichtautorisierte Nutzer nicht gespeichert (dafür sollte eine Session gestartet werden)</li>
<li> Disk Quota sollten sorgfältig definiert werden, um DOS-Attacken auf den Speicher zu vermeiden</li>
<li>Alle Funktionalitäten sollten kontrolliert werden, nachdem der HTML-Cachespeicher aktiviert wurde (z.B. Blog-Kommentare werden nicht mit alten Blog-Vorlagen funktionieren usw.);</li>
</ul>";
$MESS["cache_admin_note1"] = "
<p>Durch das Aktivieren des Autocachemodus wird Ihre Seite schneller!</p>
<p>Im Autocachemodus wird die Information, die von den Komponenten wiedergegeben wird, gemäß der Einstellungen dieser Komponenten aktualisiert.</p>
<p>Um die von der Seite zwischengespeicherte Objekte zu aktualisieren, können Sie:</p>
<p>1. In der Leiste des Ansicht-Modus auf den \"Cache\" Button klicken, um die Objekte zu aktualisieren</p>
<img src=\"/bitrix/images/main/page_cache_de.png\" vspace=\"5\" />
<p>2. Im Ansicht-Modus bei eingeschaltetem Bearbeitungsmodus im Kontextmenü der benötigten Komponente auf den Button \"Komponenten-Cache löschen\" klicken. </p>
<img src=\"/bitrix/images/main/comp_cache_de.png\" vspace=\"5\" />
<p>3. direkt zu den Komponenten-Parameter wechseln und bei den benötigten Komponenten die Cache-Einstellung \"nicht cachen\" auswählen.</p>
<img src=\"/bitrix/images/main/spisok_de.png\" vspace=\"5\" />
<p>Nach dem Einschalten des Cache-Modus als Standardeinstellung werden alle Komponenten mit der Cache-Einstellung <i>\"Auto verwaltet\"</i> zum Arbeiten mit dem Zwischenspeicher aktiviert.</p>
<p>Komponenten im Cache-Modus <i>\"cachen\"</i> und mit einer Zwischnspeicherzeit größer als 0 (null), werden immer im Cache-Modus arbeiten.</p>
<p>Komponenten im Cache-Modus <i>\"nicht cachen\"</i> oder mit einer Zwischenspeicherzeit die 0 (null) beträgt, werden immer ohne Cache-Modus arbeiten.</p>";
$MESS["cache_admin_note2"] = "Nach dem Säubern der zwischengespeicherten Dateien werden alle angezeigten Inhalte gemäß neuen Daten aktualisiert.
Neue zwischengespeicherte Dateien werden schrittweise auf den angeforderten Seiten mit Zwischenspeicherbereichen erzeugt.";
$MESS["MAIN_OPTION_CLEAR_CACHE_ALL"] = "Alle";
$MESS["MAIN_OPTION_CLEAR_CACHE_MANAGED"] = "Gesamt verwaltender";
$MESS["MAIN_OPTION_CLEAR_CACHE_STATIC"] = "Alle Seiten im HTML Cache";
$MESS["MAIN_OPTION_HTML_CACHE_RESET"] = "Standardeinstellung übernehmen";
$MESS["MAIN_OPTION_HTML_CACHE_WARNING_TRANSID"] = "Achtung! Der session.use_trans_sid Parameter ist aktiv. Der HTML-Cache wird nicht funktionieren.";
$MESS["MAIN_OPTION_HTML_CACHE_WARNING"] = "Achtung! Das Modul \"Statistik\" oder \"Werbung\" ist installiert. Die Daten im HTML-Cachespeicher werden nicht richtig verfolgt.";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_POSTS"] = "Cacheerneuerung aufgrund von Datenmodifikation";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_QUOTA"] = "Cacheerneuerung aufgrund von Platzmangel (Disk)";
$MESS["MAIN_OPTION_CACHE_OK"] = "Gecachte Dateien wurden gelöscht";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_HITS"] = "Cache Treffer";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_MISSES"] = "Cachespeicher verfehlt";
$MESS["MCACHE_TITLE"] = "Cache-Einstellungen";
$MESS["MAIN_OPTION_CLEAR_CACHE"] = "Bereinigung der Cache-Dateien";
$MESS["MAIN_TAB_3"] = "Bereinigung der Cache-Dateien";
$MESS["MAIN_OPTION_CLEAR_CACHE_CLEAR"] = "Bereinigen";
$MESS["MAIN_TAB_4"] = "Komponenten Cache";
$MESS["MAIN_OPTION_PUBL"] = "Komponenten Cache konfigurieren";
$MESS["MAIN_OPTION_CACHE_OFF"] = "Auto-Cache der Komponenten ist deaktiviert";
$MESS["MAIN_OPTION_CACHE_ON"] = "Auto-Cache der Komponenten ist aktiviert";
$MESS["MAIN_OPTION_CACHE_BUTTON_OFF"] = "Auto-Cache deaktivieren";
$MESS["MAIN_OPTION_HTML_CACHE_BUTTON_OFF"] = "HTML Cache deaktivieren";
$MESS["MAIN_OPTION_HTML_CACHE_QUOTA"] = "Speicherlimit (MB)";
$MESS["MAIN_OPTION_CACHE_BUTTON_ON"] = "Auto-Cache aktivieren";
$MESS["MAIN_OPTION_HTML_CACHE_BUTTON_ON"] = "HTML Cache aktivieren";
$MESS["MAIN_OPTION_HTML_CACHE_EXC_MASK"] = "Ausnahmen";
$MESS["MAIN_TAB_2"] = "HTML Cache";
$MESS["MAIN_OPTION_HTML_CACHE_OFF"] = "HTML Cache ist nicht aktiv";
$MESS["MAIN_OPTION_HTML_CACHE_ON"] = "HTML Cache ist aktiv";
$MESS["MAIN_OPTION_HTML_CACHE_OPT"] = "HTML Cache-Einstellungen";
$MESS["MAIN_OPTION_HTML_CACHE"] = "HTML Seitenaufbau";
$MESS["MAIN_OPTION_HTML_CACHE_INC_MASK"] = "Maske";
$MESS["MAIN_OPTION_CLEAR_CACHE_MENU"] = "Menü";
$MESS["MAIN_OPTION_CLEAR_CACHE_OLD"] = "Nur veraltete";
$MESS["MAIN_OPTION_HTML_CACHE_SAVE"] = "HTML Cache-Einstellungen speichern";
$MESS["MAIN_OPTION_HTML_CACHE_STAT"] = "Statistik";
$MESS["MAIN_OPTION_HTML_CACHE_SUCCESS"] = "Der HTML Cache-Modus wurde erfolgreich geändert.";
$MESS["MAIN_OPTION_CACHE_ERROR"] = "Der Typ des Teilcachings wurde schon auf diesen Wert gesetzt";
$MESS["MAIN_OPTION_CACHE_SUCCESS"] = "Der Typ des Teilcachings wurde erfolgreich gewechselt";
$MESS["main_cache_managed_saved"] = "Die Einstellungen vom verwalteten Cache wurden gespeichert.";
$MESS["main_cache_managed"] = "Verwalteter Cache";
$MESS["main_cache_managed_sett"] = "Parameter des verwalteten Cache";
$MESS["main_cache_managed_on"] = "Der verwaltete Cache ist aktiv.";
$MESS["main_cache_managed_off"] = "Der verwaltete Cache ist nicht aktiv (nicht empfohlen).";
$MESS["main_cache_managed_turn_off"] = "Den verwalteten Cache deaktivieren (nicht empfohlen)";
$MESS["main_cache_managed_const"] = "Die Konstante BX_COMP_MANAGED_CACHE ist angegeben. Der verwaltete Cache ist immer aktiv.";
$MESS["main_cache_managed_turn_on"] = "Den verwalteten Cache aktivieren";
$MESS["main_cache_managed_note"] = "Die <b>Technologie der Cache-Abhängigkeit</b> aktualisiert den Cache jedes Mal, wenn Daten geändert warden. Wenn diese Funktion eingeschaltet ist, müssen Sie den Cache beim Update von News oder Produkten nicht manuell aktualisieren: die Website-Besucher werden immer aktuelle Informationen sehen. <br><br> Mehr Information über die Technologie der Cache-Abhängigkeit bekommen Sie auf der Bitrix Website.
<br><br><span style=\"color:grey\"> Bitte beachten: nicht alle Komponenten unterstützen diese Funktion. </span>
";
$MESS["cache_admin_note5"] = "In dieser Edition ist der HTML-Cache immer aktiviert.";
$MESS["main_cache_wrong_cache_type"] = "Der Cache-Typ ist nicht korrekt.";
$MESS["main_cache_wrong_cache_path"] = "Der Pfad zur Cache-Datei ist nicht korrekt.";
$MESS["main_cache_in_progress"] = "Cache-Dateien werden gelöscht.";
$MESS["main_cache_finished"] = "Das Löschen der Cache-Dateien ist beendet.";
$MESS["main_cache_files_scanned_count"] = "Bearbeitet: #value#";
$MESS["main_cache_files_scanned_size"] = "Größe der bearbeiteten Dateien: #value#";
$MESS["main_cache_files_deleted_count"] = "Gelöscht: #value#";
$MESS["main_cache_files_deleted_size"] = "Größe der gelöschten Dateien: #value#";
$MESS["main_cache_files_delete_errors"] = "Fehler beim Löschen: #value#";
$MESS["main_cache_files_last_path"] = "Aktueller Ordner: #value#";
$MESS["main_cache_files_start"] = "Starten";
$MESS["main_cache_files_continue"] = "Fortfahren";
$MESS["main_cache_files_stop"] = "Abbrechen";
?>