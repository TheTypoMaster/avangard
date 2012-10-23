insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values ('Y',100,'N','Администратор','Полный доступ к управлению сайтом.');
insert into b_group (ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values ('Y',200,'Y','Everyone','Все неавторизованные на сайте пользователи.');

insert into b_language (LID,SORT,DEF,ACTIVE,NAME,FORMAT_DATE,FORMAT_DATETIME,CHARSET,DIRECTION) values ('ru','100','Y','Y','Russian','DD.MM.YYYY','DD.MM.YYYY HH:MI:SS','windows-1251','Y');
insert into b_language (LID,SORT,DEF,ACTIVE,NAME,FORMAT_DATE,FORMAT_DATETIME,CHARSET,DIRECTION) values ('en','200','N','Y','English','MM/DD/YYYY','MM/DD/YYYY HH:MI:SS','windows-1251','Y');

insert into b_lang (LID,SORT,DEF,ACTIVE,NAME,DIR,FORMAT_DATE,FORMAT_DATETIME,CHARSET,LANGUAGE_ID,DOMAIN_LIMITED) values ('s1',100,'Y','Y','Сайт по умолчанию','/','DD.MM.YYYY','DD.MM.YYYY HH:MI:SS','windows-1251','ru','N');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('ru', 'USER_INFO', 'Информация о пользователе', '
#USER_ID# - ID пользователя
#STATUS# - Статус логина
#MESSAGE# - Сообщение пользователю
#LOGIN# - Логин
#CHECKWORD# - Контрольная строка для смены пароля
#NAME# - Имя
#LAST_NAME# - Фамилия
#EMAIL# - E-Mail пользователя
', '1');

insert into b_event_type(LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values('ru', 'NEW_USER', 'Зарегистрировался новый пользователь', '
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
', '2');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(1, 'NEW_USER', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#DEFAULT_EMAIL_FROM#', '#SITE_NAME#: Зарегистрировался новый пользователь', '
Информационное сообщение сайта #SITE_NAME#
------------------------------------------

На сайте #SERVER_NAME# успешно зарегистрирован новый пользователь.

Данные пользователя:
ID пользователя: #USER_ID#

Имя: #NAME# 
Фамилия: #LAST_NAME#
E-Mail: #EMAIL# 

Login: #LOGIN#

Письмо сгенерировано автоматически. 
', 'text', '');


insert into b_event_message(ID, EVENT_NAME, LID, ACTIVE, EMAIL_FROM, EMAIL_TO, SUBJECT, MESSAGE, BODY_TYPE, BCC) values(2, 'USER_INFO', 's1', 'Y', '#DEFAULT_EMAIL_FROM#', '#EMAIL#', '#SITE_NAME#: Регистрационная информация', 'Информационное сообщение сайта #SITE_NAME#
------------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

Ваша регистрационная информация:

ID пользователя: #USER_ID#
Статус бюджета: #STATUS#
Login: #LOGIN#

Для смены пароля перейдите по следующей ссылке:
http://#SERVER_NAME#/auth.php?change_password=yes&USER_CHECKWORD=#CHECKWORD#

Сообщение сгенерировано автоматически.
', 'text', '#DEFAULT_EMAIL_FROM#');

insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(1, 's1');
insert into b_event_message_site(EVENT_MESSAGE_ID, SITE_ID) values(2, 's1');
