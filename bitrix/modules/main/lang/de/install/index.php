<?
$MESS["MAIN_ADMIN_GROUP_NAME"] = "Administratoren";
$MESS["MAIN_ADMIN_GROUP_DESC"] = "Voller Zugriff zur Seitenverwaltung.";
$MESS["MAIN_EVERYONE_GROUP_NAME"] = "Alle Nutzer, einschlie�lich nicht autorisierte";
$MESS["MAIN_EVERYONE_GROUP_DESC"] = "Alle Nutzer, einschlie�lich nicht autorisierte";
$MESS["MAIN_DEFAULT_SITE_NAME"] = "Standardseite";
$MESS["MAIN_DEFAULT_LANGUAGE_NAME"] = "German";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_DATE"] = "DD.MM.YYYY";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_DATETIME"] = "DD.MM.YYYY HH:MI:SS";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_CHARSET"] = "iso-8859-1";
$MESS["MAIN_DEFAULT_SITE_FORMAT_DATE"] = "DD.MM.YYYY";
$MESS["MAIN_DEFAULT_SITE_FORMAT_DATETIME"] = "DD.MM.YYYY HH:MI:SS";
$MESS["MAIN_DEFAULT_SITE_FORMAT_CHARSET"] = "iso-8859-1";
$MESS["MAIN_MODULE_NAME"] = "Hauptmodul";
$MESS["MAIN_MODULE_DESC"] = "Produktkernel";
$MESS["MAIN_INSTALL_DB_ERROR"] = "Verbindung zur Datenbank nicht m�glich. �berpr�fen Sie die angegebenen Parameter.";
$MESS["MAIN_NEW_USER_TYPE_NAME"] = "Ein Neuer Nutzer hat sich registriert";
$MESS["MAIN_NEW_USER_TYPE_DESC"] = "#USER_ID# - Nutzer ID
#LOGIN# - Loginname
#EMAIL# -E-Mail
#NAME# - Vorname
#LAST_NAME# - Nachname
#USER_IP# - Nutzer IP
#USER_HOST# - Nutzer Host";
$MESS["MAIN_USER_INFO_TYPE_NAME"] = "Nutzerinformation";
$MESS["MAIN_USER_INFO_TYPE_DESC"] = "#USER_ID# - Nutzer ID
#STATUS# - Accountstatus
#MESSAGE# - Nachricht an den Nutzer
#LOGIN# - Loginname
#CHECKWORD# - Kontrollwort f�r die Passwort�nderung
#NAME# - Vorname
#LAST_NAME# - Nachname
#USER_IP# - Nutzer IP
#USER_HOST# - Nutzer Host";
$MESS["MAIN_NEW_USER_CONFIRM_TYPE_NAME"] = "Registrierungsbest�tigung f�r neue Nutzer";
$MESS["MAIN_NEW_USER_CONFIRM_TYPE_DESC"] = "#USER_ID# - Nutzer ID
#LOGIN# - Loginname
#EMAIL# - E-Mail
#NAME# - Vorname
#LAST_NAME# - Nachname
#USER_IP# - Nutzer IP
#USER_HOST# - Nutzer Host
#CONFIRM_CODE# - Best�tigungscode";
$MESS["MAIN_USER_INVITE_TYPE_NAME"] = "Einladung eines neuen Nutzers";
$MESS["MAIN_USER_INVITE_TYPE_DESC"] = "#ID# - Nutzer ID
#LOGIN# - Loginname
#URL_LOGIN# - Verschl�sselter Login bei der �bergabe �ber URL
#EMAIL# - E-Mail
#NAME# - Vorname
#LAST_NAME# - Nachname
#PASSWORD# - Passwort
#CHECKWORD# - Kontrollwort f�r die Passwort�nderung
#XML_ID# - Nutzer ID, um sich mit externen Datenquellen zu verbinden";
$MESS["MAIN_NEW_USER_EVENT_NAME"] = "#SITE_NAME#: Neuer Nutzer hat sich auf der Seite registriert";
$MESS["MAIN_NEW_USER_EVENT_DESC"] = "Nachricht von #SITE_NAME#
---------------------------------------

Ein neuer Nutzer wurde auf der Seite registriert #SERVER_NAME#.

Details:
Nutzer ID: #USER_ID#

Vorname: #NAME#
Nachname: #LAST_NAME#
E-Mail: #EMAIL#

Nutzername: #LOGIN#

Dies ist eine automatisch generierte Nachricht.";
$MESS["MAIN_USER_INFO_EVENT_NAME"] = "#SITE_NAME#: Registrierungsinformationen";
$MESS["MAIN_USER_INFO_EVENT_DESC"] = "Nachricht von #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Ihre Registrierungsinformation:

Nutzer ID: #USER_ID#
Kontostatus: #STATUS#
Loginname: #LOGIN#

Um Ihr Passwort zu �ndern, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/auth/index.php?change_password=yes&lang=de&
USER_CHECKWORD=#CHECKWORD#

Dies ist eine automatisch generierte Nachricht.";
$MESS["MAIN_USER_PASS_REQUEST_EVENT_DESC"] = "Nachricht von #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Um Ihr Passwort zu �ndern, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/auth/index.php?change_password=yes&lang=de&
USER_CHECKWORD=#CHECKWORD#

Ihre Registrierungsinformation:

Nutzer ID: #USER_ID#
Kontostatus: #STATUS#
Loginname: #LOGIN#

Dies ist eine automatisch generierte Nachricht.";
$MESS["MAIN_USER_PASS_CHANGED_EVENT_DESC"] = "Nachricht von #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Ihre Registrierungsinformation:

Nutzer ID: #USER_ID#
Kontostatus: #STATUS#
Loginname: #LOGIN#

Dies ist eine automatisch generierte Nachricht.";
$MESS["MAIN_NEW_USER_CONFIRM_EVENT_NAME"] = "#SITE_NAME#: Neue Registrierungsbest�tigung";
$MESS["MAIN_NEW_USER_CONFIRM_EVENT_DESC"] = "Nachricht von #SITE_NAME#!
------------------------------------------

Hallo,

Sie haben diese Nachricht erhalten, weil Sie (oder jemand Anderes) Ihre E-Mail benutzt hat, um sich auf #SERVER_NAME# anzumelden.

Ihr Registrierungsbest�tigungscode lautet: #CONFIRM_CODE#

Bitte benutzen Sie den folgenden Link, um Ihre Anmeldung zu best�tigen und zu aktivieren:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Oder Sie �ffnen diesen Link in Ihrem Browser und tragen den Code manuell ein:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Warnung! Ihr Account wird nicht aktiviert, bis Sie Ihre Anmeldung best�tigt haben.

---------------------------------------------------------------------

Dies ist eine automatisch erstellte Nachricht.";
$MESS["MAIN_USER_INVITE_EVENT_NAME"] = "#SITE_NAME#: Einladung zur Seite";
$MESS["MAIN_USER_INVITE_EVENT_DESC"] = "Nachricht von der Seite #SITE_NAME #
------------------------------------------
Hallo #NAME# #LAST_NAME#!

Der Administrator hat Sie zu den registrierten Nutzer hinzugef�gt. 

Wir laden Sie ein, unsere Seite zu besuchen.

Ihre Anmeldedaten:

Nutzer ID: #ID#
Loginname: #LOGIN#

Wir empfehlen Ihnen, das automatisch generierte Passwort zu �ndern.

Um das Passwort zu �ndern, benutzen Sie bitte den folgenden Link:
http://#SERVER_NAME#/auth.php?change_password=yes&USER_LOGIN=#URL_LOGIN#&USER_CHECKWORD=#CHECKWORD#";
$MESS["MF_EVENT_NAME"] = "Nachricht �ber das R�ckmeldeformular senden";
$MESS["MF_EVENT_DESCRIPTION"] = "#AUTHOR# - Nachrichtenautor
#AUTHOR_EMAIL# - Autoradresse
#TEXT# - Nachricht
#EMAIL_FROM# - Absenderadresse
#EMAIL_TO# - Empf�ngeradresse";
$MESS["MF_EVENT_SUBJECT"] = "#SITE_NAME#: Nachricht aus der R�ckmeldungsform";
$MESS["MF_EVENT_MESSAGE"] = "Benachrichtigung von #SITE_NAME#
------------------------------------------

Sie haben eine Nachricht erhalten. 

Gesendet von: #AUTHOR#
Absender-E-Mail: #AUTHOR_EMAIL#

Nachricht:
#TEXT#

Diese Benachrichtigung wurde automatisch erstellt.";
$MESS["MAIN_VOTE_RATING_GROUP_NAME"] = "Nutzer, die zur Abstimmung auf ein Ranking berechtigt sind";
$MESS["MAIN_VOTE_RATING_GROUP_DESC"] = "Nutzer werden zu dieser Gruppe automatisch hinzugef�gt.";
$MESS["MAIN_VOTE_AUTHORITY_GROUP_NAME"] = "Nutzer, die zur Abstimmung auf eine Autorit�t berechtigt sind";
$MESS["MAIN_VOTE_AUTHORITY_GROUP_DESC"] = "Nutzer werden zu dieser Gruppe automatisch hinzugef�gt.";
$MESS["MAIN_RULE_ADD_GROUP_AUTHORITY_NAME"] = "In die Gruppe Nutzer eintragen, die f�r Abstimmung auf eine Autorit�t berechtigt sind";
$MESS["MAIN_RULE_ADD_GROUP_RATING_NAME"] = "In die Gruppe Nutzer eintragen, die f�r Abstimmung auf ein Ranking berechtigt sind";
$MESS["MAIN_RULE_REM_GROUP_AUTHORITY_NAME"] = "Aus der Gruppe Nutzer entfernen, die f�r Abstimmung auf eine Autorit�t nicht berechtigt sind";
$MESS["MAIN_RULE_REM_GROUP_RATING_NAME"] = "Aus der Gruppe Nutzer entfernen, die f�r Abstimmung auf ein Ranking nicht berechtigt sind";
$MESS["MAIN_RATING_AUTHORITY_NAME"] = "Autorit�t";
$MESS["MAIN_RATING_NAME"] = "Ranking";
$MESS["MAIN_RATING_TEXT_LIKE_Y"] = "Gef�llt mir";
$MESS["MAIN_RATING_TEXT_LIKE_N"] = "Gef�llt mir nicht mehr";
$MESS["MAIN_RATING_TEXT_LIKE_D"] = "Gef�llt mir";
$MESS["MAIN_USER_PASS_REQUEST_TYPE_NAME"] = "Anfrage zum Passwortwechsel";
$MESS["MAIN_USER_PASS_CHANGED_TYPE_NAME"] = "Best�tigung des Passwortwechsels";
$MESS["MAIN_USER_PASS_REQUEST_EVENT_NAME"] = "#SITE_NAME#: Anfrage zum Passwortwechsel";
$MESS["MAIN_USER_PASS_CHANGED_EVENT_NAME"] = "#SITE_NAME#: Best�tigung des Passwortwechsels";
$MESS["MAIN_DESKTOP_CREATEDBY_KEY"] = "Website erstellt von";
$MESS["MAIN_DESKTOP_CREATEDBY_VALUE"] = "Unternehmen &laquo;Bitrix, Inc.&raquo;.";
$MESS["MAIN_DESKTOP_URL_KEY"] = "Website-URL";
$MESS["MAIN_DESKTOP_URL_VALUE"] = "<a href=\"http://www.bitrix.de\">www.bitrix.de</a>";
$MESS["MAIN_DESKTOP_PRODUCTION_KEY"] = "Website freigegeben";
$MESS["MAIN_DESKTOP_PRODUCTION_VALUE"] = "12. Dezember 2010";
$MESS["MAIN_DESKTOP_RESPONSIBLE_KEY"] = "Zust�ndige Person";
$MESS["MAIN_DESKTOP_RESPONSIBLE_VALUE"] = "Max Mustermann";
$MESS["MAIN_DESKTOP_EMAIL_KEY"] = "E-Mail";
$MESS["MAIN_DESKTOP_EMAIL_VALUE"] = "<a href=\"mailto:info@bitrix.de\">info@bitrix.de</a>";
$MESS["MAIN_DESKTOP_INFO_TITLE"] = "Informationen �ber die Website";
$MESS["MAIN_DESKTOP_RSS_TITLE"] = "Bitrix News";
$MESS["MAIN_RULE_AUTO_AUTHORITY_VOTE_NAME"] = "Automatische Abstimmung f�r die Nutzerautorit�t";
?>