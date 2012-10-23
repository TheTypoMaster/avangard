<?
$MESS["cache_admin_note4"] = "<p>Es wird empfolen, f�r Seitenabschnitte, die selten ge�ndert und gr��tenteils von nicht autorisierten Nutzer besucht werden, HTML-Caching zu verwenden. Folgende Prozesse werden verwendet, wenn der HTML-Cachespeicher aktiv ist: </p>
<ul style=\"font-size:100%\">
<li>HTML-Cachespeicher bearbeitet nur Seiten, die im Eingabeformular verzeichnet und nicht als Ausnahme gekennzeichnet sind;</li>
<li>F�r nichtautorisierte Nutzer �berpr�ft das System die Kopie der Seite, die im HTML-Cachespeicher gespeichert ist. Wenn die Seite im Cachespeicher gefunden wird, wird sie ohne Modulaufruf angezeigt. Der Nutzer wird z.B. nicht in der Statistik verfolgt. Werbung, Hauptmodul und andere Module werden ebenso nicht aufgerufen.;</li>
<li>Die Seite wird komprimiert �bertragen, wenn das Kompressionsmodul im Moment der Cachespeicher-Generierung aktiviert war;</li>
<li> Wenn f�r die Seite kein Cachespeicher gefunden wird, wird sie auf normalem Weg bearbeitet. Nachdem die Seite fertig geladen ist, wird sie im HTML-Cachespeicher gespeichert;</li>
</ul>
<p>Bereinigen des Cachespeichers:</p>
<ul style=\"font-size:100%\">
<li>Wenn die Datenarchivierung den zugeteilten Speicherplatz �berschreitet, wird der Cachespeicher komplett gel�scht;</li>
<li>Komplette Cachespeicher-Bereinigung erfolgt auch, wenn  Daten  ge�ndert werden;</li>
<li>Wenn Daten von den �ffentlichen Seiten der Seite gepostet werden (z.B das Hinzuf�gen von Kommentaren oder Abstimmungen) , werden nur damit verbundene Teile des Cachespeichers bereinigt;</li>
</ul>
<p>Bitte beachten Sie, dass wenn nichtautorisierte Nutzer Seiten, die nicht im Cachespeicher vorhanden sind, besuchen, eine Session gestartet wird und der HTML-Cachespeicher f�r sie nicht mehr funktioniert.</p>
<p>Wichtige Anmerkungen:</p>
<ul style=\"font-size:100%\">
<li>Statistiken werden deaktiviert</li>
<li>Werbemodule funktionieren, wenn eine Seite im HTML-Cache verf�gbar ist. Beachten Sie, dass dies nicht f�r externe Werbemodule zutrifft (Google Ad Sense usw.)</li>
<li>Ergebnisse von Warenvergleichen werden f�r nichtautorisierte Nutzer nicht gespeichert (daf�r sollte eine Session gestartet werden)</li>
<li> Disk Quota sollten sorgf�ltig definiert werden, um DOS-Attacken auf den Speicher zu vermeiden</li>
<li>Alle Funktionalit�ten sollten kontrolliert werden, nachdem der HTML-Cachespeicher aktiviert wurde (z.B. Blog-Kommentare werden nicht mit alten Blog-Vorlagen funktionieren usw.);</li>
</ul>";
$MESS["cache_admin_note1"] = "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr>
<td valign=\"top\">Durch das Aktivieren des Autocachemodus wird Ihre Seite schneller!</td>
</tr>
<tr>
<td valign=\"top\"><br />
Im Autocachemodus wird die Information, die von den Komponenten wiedergegeben wird, gem�� der Einstellungen dieser Komponenten aktualisiert.</td>
</tr>
<tr>
<td valign=\"top\"><br />
Um die von der Seite zwischengespeicherte Objekte zu aktualisieren, k�nnen Sie:</td>
</tr>
</table>
<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr>
<td align=\"center\" valign=\"top\">&nbsp;</td>
</tr>
<tr>
<td valign=\"top\">1. In der Leiste des Ansicht-Modus auf den \"Cache\" Button klicken, um die Objekte zu aktualisieren<br />
<img src=\"/bitrix/images/main/page_cache_de.png\" vspace=\"5\" /></td>
</tr>
<tr>
<td align=\"center\" valign=\"top\">&nbsp;</td>
</tr>
<tr>
<td valign=\"top\">2. Im Ansicht-Modus bei eingeschaltetem Bearbeitungsmodus im Kontextmen� der ben�tigten Komponente auf den Button \"Komponenten-Cache l�schen\" klicken. <br />
<img src=\"/bitrix/images/main/comp_cache_de.png\" vspace=\"5\" /></td>
</tr>
<tr>
<td valign=\"top\">&nbsp;</td>
</tr>
<tr>
<td valign=\"top\">3. direkt zu den Komponenten-Parameter wechseln und bei den ben�tigten Komponenten die Cache-Einstellung \"nicht cachen\" ausw�hlen.<br>
<img src=\"/bitrix/images/main/spisok_de.gif\" vspace=\"5\" /></td>
</tr>
</table>
<br />
<p>Nach dem Einschalten des Cache-Modus als Standardeinstellung werden alle Komponenten mit der Cache-Einstellung <i>\"Auto verwaltet\"</i> zum Arbeiten mit dem Zwischenspeicher aktiviert.<br><br>
Komponenten im Cache-Modus <i>\"cachen\"</i> und mit einer Zwischnspeicherzeit gr��er als 0 (null), werden immer im Cache-Modus arbeiten.<br><br>
Komponenten im Cache-Modus <i>\"nicht cachen\"</i> oder mit einer Zwischenspeicherzeit die 0 (null) betr�gt, werden immer ohne Cache-Modus arbeiten.</p>";
$MESS["cache_admin_note2"] = "Nach dem S�ubern der zwischengespeicherten Dateien werden alle angezeigten Inhalte gem�� neuen Daten aktualisiert.
Neue zwischengespeicherte Dateien werden schrittweise auf den angeforderten Seiten mit Zwischenspeicherbereichen erzeugt.";
$MESS["MAIN_OPTION_CLEAR_CACHE_ALL"] = "Alle";
$MESS["MAIN_OPTION_CLEAR_CACHE_MANAGED"] = "Gesamt verwaltender";
$MESS["MAIN_OPTION_CLEAR_CACHE_STATIC"] = "Alle Seiten im HTML Cache";
$MESS["MAIN_OPTION_HTML_CACHE_RESET"] = "Standardeinstellung �bernehmen";
$MESS["MAIN_OPTION_HTML_CACHE_WARNING_TRANSID"] = "Achtung! Der session.use_trans_sid Parameter ist aktiv. Der HTML-Cache wird nicht funktionieren.";
$MESS["MAIN_OPTION_HTML_CACHE_WARNING"] = "Achtung! Das Modul \"Statistik\" oder \"Werbung\" ist installiert. Die Daten im HTML-Cachespeicher werden nicht richtig verfolgt.";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_POSTS"] = "Cacheerneuerung aufgrund von Datenmodifikation";
$MESS["MAIN_OPTION_HTML_CACHE_STAT_QUOTA"] = "Cacheerneuerung aufgrund von Platzmangel (Disk)";
$MESS["MAIN_OPTION_CACHE_OK"] = "Gecachte Dateien wurden gel�scht";
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
$MESS["MAIN_OPTION_CLEAR_CACHE_MENU"] = "Men�";
$MESS["MAIN_OPTION_CLEAR_CACHE_OLD"] = "Nur veraltete";
$MESS["MAIN_OPTION_HTML_CACHE_SAVE"] = "HTML Cache-Einstellungen speichern";
$MESS["MAIN_OPTION_HTML_CACHE_STAT"] = "Statistik";
$MESS["MAIN_OPTION_HTML_CACHE_SUCCESS"] = "Der HTML Cache-Modus wurde erfolgreich ge�ndert.";
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
$MESS["main_cache_managed_note"] = "Die <b>Technologie der Cache-Abh�ngigkeit</b> aktualisiert den Cache jedes Mal, wenn Daten ge�ndert warden. Wenn diese Funktion eingeschaltet ist, m�ssen Sie den Cache beim Update von News oder Produkten nicht manuell aktualisieren: die Website-Besucher werden immer aktuelle Informationen sehen. <br><br> Mehr Information �ber die Technologie der Cache-Abh�ngigkeit bekommen Sie auf der Bitrix Website. 
<br><br><span style=\"color:grey\"> Bitte beachten: nicht alle Komponenten unterst�tzen diese Funktion. </span>
";
$MESS["cache_admin_note5"] = "In dieser Edition ist der HTML-Cache immer aktiviert.";
$MESS["main_cache_wrong_cache_type"] = "Der Cache-Typ ist nicht korrekt.";
$MESS["main_cache_wrong_cache_path"] = "Der Pfad zur Cache-Datei ist nicht korrekt.";
$MESS["main_cache_in_progress"] = "Cache-Dateien werden gel�scht.";
$MESS["main_cache_finished"] = "Das L�schen der Cache-Dateien ist beendet.";
$MESS["main_cache_files_scanned_count"] = "Bearbeitet: #value#";
$MESS["main_cache_files_scanned_size"] = "Gr��e der bearbeiteten Dateien: #value#";
$MESS["main_cache_files_deleted_count"] = "Gel�scht: #value#";
$MESS["main_cache_files_deleted_size"] = "Gr��e der gel�schten Dateien: #value#";
$MESS["main_cache_files_delete_errors"] = "Fehler beim L�schen: #value#";
$MESS["main_cache_files_last_path"] = "Aktueller Ordner: #value#";
$MESS["main_cache_files_start"] = "Starten";
$MESS["main_cache_files_continue"] = "Fortfahren";
$MESS["main_cache_files_stop"] = "Abbrechen";
?>