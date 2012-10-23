create table B_BLOG_SITE_PATH
(
  ID int not null
,  SITE_ID char(2) not null
,  PATH varchar(255) not null
,  TYPE char(1) null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_SITE_PATH INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_BLOG_SITE_PATH_INSERT
BEFORE INSERT
ON B_BLOG_SITE_PATH
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_SITE_PATH.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE UNIQUE INDEX IX_BLOG_SITE_PATH_2 ON B_BLOG_SITE_PATH(SITE_ID, TYPE)
/

create table B_BLOG
(
  ID number(18) not null
,  NAME varchar2(255) not null
,  DESCRIPTION clob null
,  DATE_CREATE date not null
,  DATE_UPDATE date not null
,  ACTIVE char(1) default 'Y' not null
,  OWNER_ID number(18) not null
,  URL varchar2(255) not null
,  REAL_URL varchar2(255) null
,  GROUP_ID number(18) not null
,  ENABLE_COMMENTS char(1) default 'Y' not null
,  ENABLE_IMG_VERIF char(1) default 'N' not null
,  ENABLE_RSS char(1) default 'Y' not null
,  LAST_POST_ID number(18) null
,  LAST_POST_DATE date null
,  AUTO_GROUPS varchar2(255) null
,  EMAIL_NOTIFY char(1) DEFAULT 'Y' NOT NULL
,  primary key (ID)
)
/

CREATE SEQUENCE SQ_B_BLOG INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_INSERT
BEFORE INSERT
ON B_BLOG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX IX_BLOG_BLOG_1 ON B_BLOG(GROUP_ID, ACTIVE)
/
CREATE INDEX IX_BLOG_BLOG_2 ON B_BLOG(OWNER_ID)
/
CREATE UNIQUE INDEX IX_BLOG_BLOG_3 ON B_BLOG(NAME)
/
CREATE UNIQUE INDEX IX_BLOG_BLOG_4 ON B_BLOG(URL)
/
CREATE INDEX IX_BLOG_BLOG_5 ON B_BLOG(LAST_POST_DATE)
/

create table B_BLOG_GROUP
(
  ID number(18) not null
,  NAME varchar2(255) not null
,  SITE_ID char(2) not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_GROUP INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE INDEX IX_BLOG_GROUP_1 ON B_BLOG_GROUP(SITE_ID)
/

CREATE OR REPLACE TRIGGER B_BLOG_GROUP_INSERT
BEFORE INSERT
ON B_BLOG_GROUP
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_GROUP.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

create table B_BLOG_POST
(
  ID number(18) not null
,  TITLE varchar2(255) not null
,  BLOG_ID number(18) not null
,  AUTHOR_ID number(18) not null
,  PREVIEW_TEXT clob null
,  PREVIEW_TEXT_TYPE char(4) default 'text' not null
,  DETAIL_TEXT clob not null
,  DETAIL_TEXT_TYPE char(4) default 'text' not null
,  DATE_CREATE date not null
,  DATE_PUBLISH date not null
,  KEYWORDS varchar2(255) null
,  PUBLISH_STATUS char(1) default 'P' not null
,  CATEGORY_ID number(18) null
,  ATRIBUTE varchar2(255) null
,  ENABLE_TRACKBACK char(1) default 'Y' not null
,  ENABLE_COMMENTS char(1) default 'Y' not null
,  ATTACH_IMG number(18) null
,  NUM_COMMENTS int default '0' not null
,  NUM_TRACKBACKS int default '0' not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_POST INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_POST_INSERT
BEFORE INSERT
ON B_BLOG_POST
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_POST.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX IX_BLOG_POST_1 ON B_BLOG_POST(BLOG_ID, PUBLISH_STATUS, DATE_PUBLISH)
/
CREATE INDEX IX_BLOG_POST_2 ON B_BLOG_POST(BLOG_ID, DATE_PUBLISH, PUBLISH_STATUS)
/
CREATE INDEX IX_BLOG_POST_3 ON B_BLOG_POST(BLOG_ID, CATEGORY_ID)
/

create table B_BLOG_CATEGORY
(
  ID number(18) not null
,  BLOG_ID number(18) not null
,  NAME varchar2(255) not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_CATEGORY INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE UNIQUE INDEX IX_BLOG_CAT_1 ON B_BLOG_CATEGORY(BLOG_ID, NAME)
/

CREATE OR REPLACE TRIGGER B_BLOG_CATEGORY_INSERT
BEFORE INSERT
ON B_BLOG_CATEGORY
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_CATEGORY.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/


create table B_BLOG_COMMENT
(
  ID number(18) not null
,  BLOG_ID number(18) not null
,  POST_ID number(18) not null
,  PARENT_ID number(18) null
,  AUTHOR_ID number(18) null
,  ICON_ID number(18) null
,  AUTHOR_NAME varchar2(255) null
,  AUTHOR_EMAIL varchar2(255) null
,  AUTHOR_IP varchar2(20) null
,  AUTHOR_IP1 varchar2(20) null
,  DATE_CREATE date not null
,  TITLE varchar2(255) null
,  POST_TEXT clob not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_COMMENT INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_COMMENT_INSERT
BEFORE INSERT
ON B_BLOG_COMMENT
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_COMMENT.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX IX_BLOG_COMM_1 ON B_BLOG_COMMENT(BLOG_ID, POST_ID)
/
CREATE INDEX IX_BLOG_COMM_2 ON B_BLOG_COMMENT(AUTHOR_ID)
/


create table B_BLOG_USER
(
  ID number(18) not null
,  USER_ID number(18) not null
,  ALIAS varchar2(255) null
,  DESCRIPTION clob null
,  AVATAR number(18) null
,  INTERESTS varchar2(255) null
,  LAST_VISIT date null
,  DATE_REG date not null
,  ALLOW_POST char(1) default 'Y' not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_USER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_USER_INSERT
BEFORE INSERT
ON B_BLOG_USER
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_USER.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_BLOG_USER_1 ON B_BLOG_USER(USER_ID)
/
CREATE INDEX IX_BLOG_USER_2 ON B_BLOG_USER(ALIAS)
/

create table B_BLOG_USER_GROUP
(
  ID number(18) not null
,  BLOG_ID number(18) null
,  NAME varchar2(255) not null
,  primary key (ID)
)
/

CREATE SEQUENCE SQ_B_BLOG_USER_GROUP START WITH 3 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_USER_GROUP_INSERT
BEFORE INSERT
ON B_BLOG_USER_GROUP
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_USER_GROUP.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX IX_BLOG_USER_GROUP_1 ON B_BLOG_USER_GROUP(BLOG_ID)
/

INSERT INTO b_blog_user_group(ID, BLOG_ID, NAME) VALUES(1, null, 'all')
/
INSERT INTO b_blog_user_group(ID, BLOG_ID, NAME) VALUES(2, null, 'registered')
/


create table B_BLOG_USER2USER_GROUP
(
  ID number(18) not null
,  USER_ID number(18) not null
,  BLOG_ID number(18) not null
,  USER_GROUP_ID number(18) not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_USER2USER_GROUP INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_USER2USER_GROUP_INSERT
BEFORE INSERT
ON B_BLOG_USER2USER_GROUP
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_USER2USER_GROUP.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_BLOG_USER2GROUP_1 ON B_BLOG_USER2USER_GROUP(USER_ID, BLOG_ID, USER_GROUP_ID)
/

create table B_BLOG_USER_GROUP_PERMS
(
  ID number(18) not null
,  BLOG_ID number(18) not null
,  USER_GROUP_ID number(18) not null
,  PERMS_TYPE char(1) default 'P' not null
,  POST_ID number(18) null
,  PERMS char(1) default 'D' not null
,  AUTOSET char(1) default 'N' not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_USER_GROUP_PERMS INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_USER_GROUP_PERMS_INSERT
BEFORE INSERT
ON B_BLOG_USER_GROUP_PERMS
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_USER_GROUP_PERMS.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_BLOG_UG_PERMS_1 ON B_BLOG_USER_GROUP_PERMS(BLOG_ID, USER_GROUP_ID, PERMS_TYPE, POST_ID)
/
CREATE INDEX IX_BLOG_UG_PERMS_2 ON B_BLOG_USER_GROUP_PERMS(USER_GROUP_ID, PERMS_TYPE, POST_ID)
/
CREATE INDEX IX_BLOG_UG_PERMS_3 ON B_BLOG_USER_GROUP_PERMS(POST_ID, USER_GROUP_ID, PERMS_TYPE)
/

create table B_BLOG_USER2BLOG
(
  ID int not null
,  USER_ID int not null
,  BLOG_ID int not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_USER2BLOG INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_USER2BLOG_INSERT
BEFORE INSERT
ON B_BLOG_USER2BLOG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_USER2BLOG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_BLOG_USER2BLOG_1 ON B_BLOG_USER2BLOG(BLOG_ID, USER_ID)
/



create table B_BLOG_TRACKBACK
(
  ID number(18) not null
,  TITLE varchar2(255) not null
,  URL varchar2(255) not null
,  PREVIEW_TEXT clob not null
,  BLOG_NAME varchar2(255) null
,  POST_DATE date not null
,  BLOG_ID number(18) not null
,  POST_ID number(18) not null
,  primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_TRACKBACK INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_TRACKBACK_INSERT
BEFORE INSERT
ON B_BLOG_TRACKBACK
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_TRACKBACK.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX IX_BLOG_TRBK_1 ON B_BLOG_TRACKBACK(BLOG_ID, POST_ID)
/
CREATE INDEX IX_BLOG_TRBK_2 ON B_BLOG_TRACKBACK(POST_ID)
/


create table B_BLOG_SMILE (
   ID number(18) not null,
   SMILE_TYPE char(1) default 'S' not null,
   TYPING varchar2(100) null,
   IMAGE varchar2(128) not null,
   DESCRIPTION varchar2(50),
   CLICKABLE char(1) default 'Y' not null,
   SORT number(18) default '150' not null,
   IMAGE_WIDTH number(18) default '0' not null,
   IMAGE_HEIGHT number(18) default '0' not null,
   primary key (ID))
/
CREATE SEQUENCE SQ_B_BLOG_SMILE START WITH 91 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_SMILE_INSERT
BEFORE INSERT
ON B_BLOG_SMILE
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_SMILE.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

insert  into b_blog_smile values (68, 'S', ':D :-D', 'icon_biggrin.gif', 'FICON_BIGGRIN', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (69, 'S', ':) :-)', 'icon_smile.gif', 'FICON_SMILE', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (70, 'S', ':( :-(', 'icon_sad.gif', 'FICON_SAD', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (71, 'S', ':o :-o :shock:', 'icon_eek.gif', 'FICON_EEK', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (72, 'S', '8) 8-)', 'icon_cool.gif', 'FICON_COOL', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (73, 'S', ':{} :-{}', 'icon_kiss.gif', 'FICON_KISS', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (74, 'S', ':oops:', 'icon_redface.gif', 'FICON_REDFACE', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (75, 'S', ':cry: :~(', 'icon_cry.gif', 'FICON_CRY', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (76, 'S', ':evil: >:-<', 'icon_evil.gif', 'FICON_EVIL', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (77, 'S', ';) ;-)', 'icon_wink.gif', 'FICON_WINK', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (78, 'S', ':!:', 'icon_exclaim.gif', 'FICON_EXCLAIM', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (79, 'S', ':?:', 'icon_question.gif', 'FICON_QUESTION', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (80, 'S', ':idea:', 'icon_idea.gif', 'FICON_IDEA', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (81, 'S', ':| :-|', 'icon_neutral.gif', 'FICON_NEUTRAL', 'Y', 150, 16, 16) 
/
insert  into b_blog_smile values (82, 'I', '', 'icon1.gif', 'FICON_NOTE', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (83, 'I', '', 'icon2.gif', 'FICON_DIRRECTION', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (84, 'I', '', 'icon3.gif', 'FICON_IDEA2', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (85, 'I', '', 'icon4.gif', 'FICON_ATTANSION', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (86, 'I', '', 'icon5.gif', 'FICON_QUESTION2', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (87, 'I', '', 'icon6.gif', 'FICON_BAD', 'Y', 150, 15, 15) 
/
insert  into b_blog_smile values (88, 'I', '', 'icon7.gif', 'FICON_GOOD', 'Y', 150, 15, 15) 
/


create table B_BLOG_SMILE_LANG (
   ID number(18) not null,
   SMILE_ID number(18) default '0' not null,
   LID char(2) not null,
   NAME varchar2(255) not null,
   primary key (ID)
)
/
CREATE SEQUENCE SQ_B_BLOG_SMILE_LANG START WITH 120 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_BLOG_SMILE_LANG_INSERT
BEFORE INSERT
ON B_BLOG_SMILE_LANG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_BLOG_SMILE_LANG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_BLOG_SMILE_K ON B_BLOG_SMILE_LANG(SMILE_ID, LID)
/


insert  into b_blog_smile_lang values (106, 85, 'en', 'Attention') 
/
insert  into b_blog_smile_lang values (105, 85, 'ru', 'Внимание') 
/
insert  into b_blog_smile_lang values (104, 84, 'en', 'Idea') 
/
insert  into b_blog_smile_lang values (103, 84, 'ru', 'Идея') 
/
insert  into b_blog_smile_lang values (102, 83, 'en', 'Direction') 
/
insert  into b_blog_smile_lang values (101, 83, 'ru', 'Направление') 
/
insert  into b_blog_smile_lang values (100, 82, 'en', 'Note') 
/
insert  into b_blog_smile_lang values (99, 82, 'ru', 'Заметка') 
/
insert  into b_blog_smile_lang values (98, 81, 'en', 'Confused') 
/
insert  into b_blog_smile_lang values (97, 81, 'ru', 'Скептически') 
/
insert  into b_blog_smile_lang values (96, 80, 'en', 'Idea') 
/
insert  into b_blog_smile_lang values (95, 80, 'ru', 'Идея') 
/
insert  into b_blog_smile_lang values (94, 79, 'en', 'Question') 
/
insert  into b_blog_smile_lang values (93, 79, 'ru', 'Вопрос') 
/
insert  into b_blog_smile_lang values (92, 78, 'en', 'Exclamation') 
/
insert  into b_blog_smile_lang values (91, 78, 'ru', 'Восклицание') 
/
insert  into b_blog_smile_lang values (90, 77, 'en', 'Wink') 
/
insert  into b_blog_smile_lang values (89, 77, 'ru', 'Шутливо') 
/
insert  into b_blog_smile_lang values (88, 76, 'en', 'Angry') 
/
insert  into b_blog_smile_lang values (87, 76, 'ru', 'Со злостью') 
/
insert  into b_blog_smile_lang values (86, 75, 'en', 'Crying') 
/
insert  into b_blog_smile_lang values (85, 75, 'ru', 'Очень грустно') 
/
insert  into b_blog_smile_lang values (84, 74, 'en', 'Embaressed') 
/
insert  into b_blog_smile_lang values (83, 74, 'ru', 'Смущенно') 
/
insert  into b_blog_smile_lang values (82, 73, 'en', 'Kiss') 
/
insert  into b_blog_smile_lang values (81, 73, 'ru', 'Поцелуй') 
/
insert  into b_blog_smile_lang values (80, 72, 'en', 'Cool') 
/
insert  into b_blog_smile_lang values (79, 72, 'ru', 'Здорово') 
/
insert  into b_blog_smile_lang values (78, 71, 'en', 'Surprised') 
/
insert  into b_blog_smile_lang values (77, 71, 'ru', 'Удивленно') 
/
insert  into b_blog_smile_lang values (76, 70, 'en', 'Sad') 
/
insert  into b_blog_smile_lang values (75, 70, 'ru', 'Печально') 
/
insert  into b_blog_smile_lang values (74, 69, 'en', 'Smile') 
/
insert  into b_blog_smile_lang values (73, 69, 'ru', 'С улыбкой') 
/
insert  into b_blog_smile_lang values (72, 68, 'en', 'Big grin') 
/
insert  into b_blog_smile_lang values (71, 68, 'ru', 'Широкая улыбка') 
/
insert  into b_blog_smile_lang values (107, 86, 'ru', 'Вопрос') 
/
insert  into b_blog_smile_lang values (108, 86, 'en', 'Question') 
/
insert  into b_blog_smile_lang values (109, 87, 'ru', 'Плохо') 
/
insert  into b_blog_smile_lang values (110, 87, 'en', 'Thumbs Up') 
/
insert  into b_blog_smile_lang values (111, 88, 'ru', 'Хорошо') 
/
insert  into b_blog_smile_lang values (112, 88, 'en', 'Thumbs Down') 
/


CREATE TABLE b_blog_image (
  ID number(18),
  FILE_ID number(18) default 0 NOT NULL,
  BLOG_ID number(18) default 0 NOT NULL,
  POST_ID number(18) default 0 NOT NULL,
  USER_ID number(18) default 0 NOT NULL,
  TIMESTAMP_X date default sysdate NOT NULL,
  TITLE varchar2(255),
  IMAGE_SIZE number(18) default 0 NOT NULL,
  CONSTRAINT PK_b_blog_image PRIMARY KEY  (ID)
)
/
CREATE sequence SQ_B_BLOG_IMAGE
/