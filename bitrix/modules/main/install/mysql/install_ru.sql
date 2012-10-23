insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values ('Y',100,'N','�������������','������ ������ � ���������� ������.');
insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values ('Y',200,'Y','Everyone','��� ���������������� �� ����� ������������.');

insert into b_language (LID,SORT,DEF,ACTIVE,NAME,FORMAT_DATE,FORMAT_DATETIME,CHARSET,DIRECTION) values ('ru','100','Y','Y','Russian','DD.MM.YYYY','DD.MM.YYYY HH:MI:SS','windows-1251','Y');
insert into b_language (LID,SORT,DEF,ACTIVE,NAME,FORMAT_DATE,FORMAT_DATETIME,CHARSET,DIRECTION) values ('en','200','N','Y','English','MM/DD/YYYY','MM/DD/YYYY HH:MI:SS','windows-1251','Y');

insert into b_lang (LID,SORT,DEF,ACTIVE,NAME,DIR,FORMAT_DATE,FORMAT_DATETIME,CHARSET,LANGUAGE_ID,DOMAIN_LIMITED) values ('s1',100,'Y','Y','���� �� ���������','/','DD.MM.YYYY','DD.MM.YYYY HH:MI:SS','windows-1251','ru','N');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('ru', 'USER_INFO', '���������� � ������������', '
#USER_ID# - ID ������������
#STATUS# - ������ ������
#MESSAGE# - ��������� ������������
#LOGIN# - �����
#CHECKWORD# - ����������� ������ ��� ����� ������
#NAME# - ���
#LAST_NAME# - �������
#EMAIL# - E-Mail ������������
', '1');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('ru', 'NEW_USER', '����������������� ����� ������������', '
#USER_ID# - ID ������������
#LOGIN# - �����
#EMAIL# - EMail
#NAME# - ���
#LAST_NAME# - �������
#USER_IP# - IP ������������
#USER_HOST# - ���� ������������
', '2');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(1, 'NEW_USER', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#DEFAULT_EMAIL_FROM#', '#SITE_NAME#: ����������������� ����� ������������', '
�������������� ��������� ����� #SITE_NAME#
------------------------------------------

�� ����� #SERVER_NAME# ������� ��������������� ����� ������������.

������ ������������:
ID ������������: #USER_ID#

���: #NAME# 
�������: #LAST_NAME#
E-Mail: #EMAIL# 

Login: #LOGIN#

������ ������������� �������������. 
', 'text', '');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(2, 'USER_INFO', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#EMAIL#', '#SITE_NAME#: ��������������� ����������', '�������������� ��������� ����� #SITE_NAME#
------------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

���� ��������������� ����������:

ID ������������: #USER_ID#
������ �������: #STATUS#
Login: #LOGIN#

��� ����� ������ ��������� �� ��������� ������:
http://#SERVER_NAME#/auth.php?change_password=yes&USER_CHECKWORD=#CHECKWORD#

��������� ������������� �������������.
', 'text', '#DEFAULT_EMAIL_FROM#');

insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(1, 's1');
insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(2, 's1');
