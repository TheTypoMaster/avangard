<?
$MESS ['VOTE_NEW_DESC'] = "#ID# - Abstimmungsergebnis ID
#TIME# - Zeit der Abstimmung
#VOTE_TITLE# - Abstimmungsüberschrift
#VOTE_DESCRIPTION# - Abstimmungsbeschreibung
#VOTE_ID# - Abstimmungs ID
#CHANNEL# - Gruppenname
#CHANNEL_ID# - Gruppen ID
#VOTER_ID# - Besucher ID
#USER_NAME# - Username
#LOGIN# - Loginname
#USER_ID# - User ID
#STAT_GUEST_ID# - User ID des Moduls \"Statistik\"
#SESSION_ID# - Session ID des Moduls \"Statistik\"
#IP# - IP Adresse
";
$MESS ['VOTE_NEW_MESSAGE'] = "Neue Abstimmung

Abstimmung  - [#VOTE_ID#] #VOTE_TITLE#
Gruppe      - [#CHANNEL_ID#] #CHANNEL#

--------------------------------------------------------------

Besucher    - [#VOTER_ID#] (#LOGIN#) #USER_NAME# [#STAT_GUEST_ID#]
Session     - #SESSION_ID#
IP Adresse  - #IP#
Zeit        - #TIME#

Um die Abstimmungsergebnisse anzusehen, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/bitrix/admin/vote_user_results.php?EVENT_ID=#ID#&lang=de


Um das Ergebnisdiagramm anzusehen, klicken Sie bitte auf den folgenden Link:
http://#SERVER_NAME#/bitrix/admin/vote_results.php?lang=de&VOTE_ID=#VOTE_ID#

Dies ist eine automatisch generierte Nachricht.
";
$MESS ['VOTE_NEW_SUBJECT'] = "#SITE_NAME#: Neue Stimmen bei der Abstimmung \"[#VOTE_ID#] #VOTE_TITLE#\"";
$MESS ['VOTE_NEW_NAME'] = "Neue Abstimmung";
?>