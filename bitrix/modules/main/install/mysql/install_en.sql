insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values('Y', 100, 'N', 'Administrators', 'full access') ;
insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values('Y', 200, 'Y', 'Anonymous', 'applied to everyone by default');

insert into b_language (LID,SORT,DEF,ACTIVE,NAME,FORMAT_DATE,FORMAT_DATETIME,CHARSET,DIRECTION) values ('en','200','N','Y','English','MM/DD/YYYY','MM/DD/YYYY HH:MI:SS','iso-8859-1','Y');

insert into b_lang (LID,SORT,DEF,ACTIVE,NAME,DIR,FORMAT_DATE,FORMAT_DATETIME,CHARSET,LANGUAGE_ID,DOMAIN_LIMITED) values ('s1',100,'Y','Y','Default site','/','MM/DD/YYYY','MM/DD/YYYY HH:MI:SS','iso-8859-1','en','N');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('en', 'USER_INFO', 'Account Information', '
#USER_ID# - User ID
#STATUS# - Account status
#MESSAGE# - Message for user
#LOGIN# - Login
#CHECKWORD# - Check string for password change
#NAME# - Name
#LAST_NAME# - Last Name
#EMAIL# - User E-Mail
', '1');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('en', 'NEW_USER', 'New user was registered', '
#USER_ID# - User ID
#LOGIN# - Login
#EMAIL# - EMail
#NAME# - Name
#LAST_NAME# - Last Name
#USER_IP# - User IP
#USER_HOST# - User Host
', '2');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(1, 'USER_INFO', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#EMAIL#', '#SITE_NAME#: Registration info', 'Informational message from #SITE_NAME#
---------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

Your registration info:

User ID: #USER_ID#
Account status: #STATUS#
Login: #LOGIN#

To change your password please visit the link below:

http://#SERVER_NAME#/auth.php?change_password=yes&lang=en&USER_CHECKWORD=#CHECKWORD#

Automatically generated message.
', 'text', '');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(2, 'NEW_USER', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#DEFAULT_EMAIL_FROM#', '#SITE_NAME#: New user has been registered on the site', 'Informational message from #SITE_NAME#
---------------------------------------

New user has been successfully registered on the site #SERVER_NAME#.

User details:
User ID: #USER_ID#

Name: #NAME# 
Last Name: #LAST_NAME#
User''s E-Mail: #EMAIL# 
Login: #LOGIN#

Automatically generated message.

', 'text', '');

insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(1, 's1');
insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(2, 's1');