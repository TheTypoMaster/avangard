create table B_BLOG_SITE_PATH
(
  ID int not null IDENTITY (1, 1)
,  SITE_ID char(2) not null
,  PATH varchar(255) not null
,  TYPE char(1) null
)
GO
ALTER TABLE B_BLOG_SITE_PATH ADD CONSTRAINT PK_B_BLOG_SITE_PATH PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_SITE_PATH_2 ON B_BLOG_SITE_PATH(SITE_ID, TYPE)
GO

create table B_BLOG
(
  ID int not null IDENTITY (1, 1)
,  NAME varchar(255) not null
,  DESCRIPTION text null
,  DATE_CREATE datetime not null
,  DATE_UPDATE datetime not null
,  ACTIVE char(1) not null default 'Y'
,  OWNER_ID int not null
,  URL varchar(255) not null
,  REAL_URL varchar(255) null
,  GROUP_ID int not null
,  ENABLE_COMMENTS char(1) not null default 'Y'
,  ENABLE_IMG_VERIF char(1) not null default 'N'
,  ENABLE_RSS char(1) not null default 'Y'
,  LAST_POST_ID int null
,  LAST_POST_DATE datetime null
,  AUTO_GROUPS varchar(255) null
,  EMAIL_NOTIFY CHAR( 1 ) NOT NULL DEFAULT 'Y'
)
GO
ALTER TABLE B_BLOG ADD CONSTRAINT PK_B_BLOG PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_BLOG_1 ON B_BLOG(GROUP_ID, ACTIVE)
GO
CREATE INDEX IX_BLOG_BLOG_2 ON B_BLOG(OWNER_ID)
GO
CREATE UNIQUE INDEX IX_BLOG_BLOG_3 ON B_BLOG(NAME)
GO
CREATE UNIQUE INDEX IX_BLOG_BLOG_4 ON B_BLOG(URL)
GO
CREATE INDEX IX_BLOG_BLOG_5 ON B_BLOG(LAST_POST_DATE)
GO

create table B_BLOG_GROUP
(
  ID int not null IDENTITY (1, 1)
,  NAME varchar(255) not null
,  SITE_ID char(2) not null
)
GO
ALTER TABLE B_BLOG_GROUP ADD CONSTRAINT PK_BLG_GROUP PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_GROUP_1 ON B_BLOG_GROUP(SITE_ID)
GO

create table B_BLOG_POST
(
  ID int not null IDENTITY (1, 1)
,  TITLE varchar(255) not null
,  BLOG_ID int not null
,  AUTHOR_ID int not null
,  PREVIEW_TEXT text null
,  PREVIEW_TEXT_TYPE char(4) not null default 'text'
,  DETAIL_TEXT text not null
,  DETAIL_TEXT_TYPE char(4) not null default 'text'
,  DATE_CREATE datetime not null
,  DATE_PUBLISH datetime not null
,  KEYWORDS varchar(255) null
,  PUBLISH_STATUS char(1) not null default 'P'
,  CATEGORY_ID int null
,  ATRIBUTE varchar(255) null
,  ENABLE_TRACKBACK char(1) not null default 'Y'
,  ENABLE_COMMENTS char(1) not null default 'Y'
,  ATTACH_IMG int null
,  NUM_COMMENTS int not null default '0'
,  NUM_TRACKBACKS int not null default '0'
)
GO
ALTER TABLE B_BLOG_POST ADD CONSTRAINT PK_BLG_POST PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_POST_1 ON B_BLOG_POST(BLOG_ID, PUBLISH_STATUS, DATE_PUBLISH)
GO
CREATE INDEX IX_BLOG_POST_2 ON B_BLOG_POST(BLOG_ID, DATE_PUBLISH, PUBLISH_STATUS)
GO
CREATE INDEX IX_BLOG_POST_3 ON B_BLOG_POST(BLOG_ID, CATEGORY_ID)
GO


create table B_BLOG_CATEGORY
(
  ID int not null IDENTITY (1, 1)
,  BLOG_ID int not null
,  NAME varchar(255) not null
)
GO
ALTER TABLE B_BLOG_CATEGORY ADD CONSTRAINT PK_BLG_CATEGORY PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_CAT_1 ON B_BLOG_CATEGORY(BLOG_ID, NAME)
GO


create table B_BLOG_COMMENT
(
  ID int not null IDENTITY (1, 1)
,  BLOG_ID int not null
,  POST_ID int not null
,  PARENT_ID int null
,  AUTHOR_ID int null
,  ICON_ID int null
,  AUTHOR_NAME varchar(255) null
,  AUTHOR_EMAIL varchar(255) null
,  AUTHOR_IP varchar(20) null
,  AUTHOR_IP1 varchar(20) null
,  DATE_CREATE datetime not null
,  TITLE varchar(255) null
,  POST_TEXT text not null
)
GO
ALTER TABLE B_BLOG_COMMENT ADD CONSTRAINT PK_BLG_COMMENT PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_COMM_1 ON B_BLOG_COMMENT(BLOG_ID, POST_ID)
GO
CREATE INDEX IX_BLOG_COMM_2 ON B_BLOG_COMMENT(AUTHOR_ID)
GO


create table B_BLOG_USER
(
  ID int not null IDENTITY (1, 1)
,  USER_ID int not null
,  ALIAS varchar(255) null
,  DESCRIPTION text null
,  AVATAR int null
,  INTERESTS varchar(255) null
,  LAST_VISIT datetime null
,  DATE_REG datetime not null
,  ALLOW_POST char(1) not null default 'Y'
)
GO
ALTER TABLE B_BLOG_USER ADD CONSTRAINT PK_BLG_USER PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_USER_1 ON B_BLOG_USER(USER_ID)
GO
CREATE INDEX IX_BLOG_USER_2 ON B_BLOG_USER(ALIAS)
GO

create table B_BLOG_USER_GROUP
(
  ID int not null IDENTITY (1, 1)
,  BLOG_ID int null
,  NAME varchar(255) not null
)
GO
ALTER TABLE B_BLOG_USER_GROUP ADD CONSTRAINT PK_BLG_USER_GROUP PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_USER_GROUP_1 ON B_BLOG_USER_GROUP(BLOG_ID)
GO

ALTER TABLE b_blog_user_group NOCHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_user_group DISABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_user_group ON
GO

INSERT INTO b_blog_user_group(ID, BLOG_ID, NAME) VALUES(1, null, 'all')
GO
INSERT INTO b_blog_user_group(ID, BLOG_ID, NAME) VALUES(2, null, 'registered')
GO

ALTER TABLE b_blog_user_group CHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_user_group ENABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_user_group OFF
GO

create table B_BLOG_USER2USER_GROUP
(
  ID int not null IDENTITY (1, 1)
,  USER_ID int not null
,  BLOG_ID int not null
,  USER_GROUP_ID int not null
)
GO
ALTER TABLE B_BLOG_USER2USER_GROUP ADD CONSTRAINT PK_BLG_USER2USER_GROUP PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_USER2GROUP_1 ON B_BLOG_USER2USER_GROUP(USER_ID, BLOG_ID, USER_GROUP_ID)
GO

create table B_BLOG_USER_GROUP_PERMS
(
  ID int not null IDENTITY (1, 1)
,  BLOG_ID int not null
,  USER_GROUP_ID int not null
,  PERMS_TYPE char(1) not null default 'P'
,  POST_ID int null
,  PERMS char(1) not null default 'D'
,  AUTOSET char(1) not null default 'N'
)
GO
ALTER TABLE B_BLOG_USER_GROUP_PERMS ADD CONSTRAINT PK_BLG_USER_GROUP_PERMS PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_UG_PERMS_1 ON B_BLOG_USER_GROUP_PERMS(BLOG_ID, USER_GROUP_ID, PERMS_TYPE, POST_ID)
GO
CREATE INDEX IX_BLOG_UG_PERMS_2 ON B_BLOG_USER_GROUP_PERMS(USER_GROUP_ID, PERMS_TYPE, POST_ID)
GO
CREATE INDEX IX_BLOG_UG_PERMS_3 ON B_BLOG_USER_GROUP_PERMS(POST_ID, USER_GROUP_ID, PERMS_TYPE)
GO

create table B_BLOG_USER2BLOG
(
  ID int not null IDENTITY (1, 1)
,  USER_ID int not null
,  BLOG_ID int not null
)
GO
ALTER TABLE B_BLOG_USER2BLOG ADD CONSTRAINT PK_BLG_USER2BLOG PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_USER2BLOG_1 ON B_BLOG_USER2BLOG(BLOG_ID, USER_ID)
GO

create table B_BLOG_TRACKBACK
(
  ID int not null IDENTITY (1, 1)
,  TITLE varchar(255) not null
,  URL varchar(255) not null
,  PREVIEW_TEXT text not null
,  BLOG_NAME varchar(255) null
,  POST_DATE datetime not null
,  BLOG_ID int not null
,  POST_ID int not null
)
GO
ALTER TABLE B_BLOG_TRACKBACK ADD CONSTRAINT PK_BLG_TRACKBACK PRIMARY KEY (ID)
GO
CREATE INDEX IX_BLOG_TRBK_1 ON B_BLOG_TRACKBACK(BLOG_ID, POST_ID)
GO
CREATE INDEX IX_BLOG_TRBK_2 ON B_BLOG_TRACKBACK(POST_ID)
GO

create table B_BLOG_SMILE (
   ID int not null IDENTITY (1, 1),
   SMILE_TYPE char(1) not null default 'S',
   TYPING varchar(100) null,
   IMAGE varchar(128) not null,
   DESCRIPTION varchar(50),
   CLICKABLE char(1) not null default 'Y',
   SORT int not null default 150,
   IMAGE_WIDTH int not null default 0,
   IMAGE_HEIGHT int not null default 0
)
GO
ALTER TABLE B_BLOG_SMILE ADD CONSTRAINT PK_BLG_SMILE PRIMARY KEY (ID)
GO

ALTER TABLE b_blog_smile NOCHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_smile DISABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_smile ON
GO

insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (68, 'S', ':D :-D', 'icon_biggrin.gif', 'FICON_BIGGRIN', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (69, 'S', ':) :-)', 'icon_smile.gif', 'FICON_SMILE', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (70, 'S', ':( :-(', 'icon_sad.gif', 'FICON_SAD', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (71, 'S', ':o :-o :shock:', 'icon_eek.gif', 'FICON_EEK', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (72, 'S', '8) 8-)', 'icon_cool.gif', 'FICON_COOL', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (73, 'S', ':{} :-{}', 'icon_kiss.gif', 'FICON_KISS', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (74, 'S', ':oops:', 'icon_redface.gif', 'FICON_REDFACE', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (75, 'S', ':cry: :~(', 'icon_cry.gif', 'FICON_CRY', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (76, 'S', ':evil: >:-<', 'icon_evil.gif', 'FICON_EVIL', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (77, 'S', ';) ;-)', 'icon_wink.gif', 'FICON_WINK', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (78, 'S', ':!:', 'icon_exclaim.gif', 'FICON_EXCLAIM', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (79, 'S', ':?:', 'icon_question.gif', 'FICON_QUESTION', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (80, 'S', ':idea:', 'icon_idea.gif', 'FICON_IDEA', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (81, 'S', ':| :-|', 'icon_neutral.gif', 'FICON_NEUTRAL', 'Y', 150, 16, 16) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (82, 'I', '', 'icon1.gif', 'FICON_NOTE', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (83, 'I', '', 'icon2.gif', 'FICON_DIRRECTION', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (84, 'I', '', 'icon3.gif', 'FICON_IDEA2', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (85, 'I', '', 'icon4.gif', 'FICON_ATTANSION', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (86, 'I', '', 'icon5.gif', 'FICON_QUESTION2', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (87, 'I', '', 'icon6.gif', 'FICON_BAD', 'Y', 150, 15, 15) 
GO
insert  into b_blog_smile(ID, SMILE_TYPE, TYPING, IMAGE, DESCRIPTION, CLICKABLE, SORT, IMAGE_WIDTH, IMAGE_HEIGHT) values (88, 'I', '', 'icon7.gif', 'FICON_GOOD', 'Y', 150, 15, 15) 
GO

ALTER TABLE b_blog_smile CHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_smile ENABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_smile OFF
GO

create table B_BLOG_SMILE_LANG (
   ID int not null IDENTITY (1, 1),
   SMILE_ID int not null default 0,
   LID char(2) not null,
   NAME varchar(255) not null,
)
GO
ALTER TABLE B_BLOG_SMILE_LANG ADD CONSTRAINT PK_BLG_SMILE_LANG PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX IX_BLOG_SMILE_K ON B_BLOG_SMILE_LANG(SMILE_ID, LID)
GO


ALTER TABLE b_blog_smile_lang NOCHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_smile_lang DISABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_smile_lang ON
GO

insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (106, 85, 'en', 'Attention') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (105, 85, 'ru', 'Внимание') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (104, 84, 'en', 'Idea') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (103, 84, 'ru', 'Идея') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (102, 83, 'en', 'Direction') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (101, 83, 'ru', 'Направление') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (100, 82, 'en', 'Note') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (99, 82, 'ru', 'Заметка') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (98, 81, 'en', 'Confused') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (97, 81, 'ru', 'Скептически') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (96, 80, 'en', 'Idea') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (95, 80, 'ru', 'Идея') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (94, 79, 'en', 'Question') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (93, 79, 'ru', 'Вопрос') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (92, 78, 'en', 'Exclamation') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (91, 78, 'ru', 'Восклицание') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (90, 77, 'en', 'Wink') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (89, 77, 'ru', 'Шутливо') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (88, 76, 'en', 'Angry') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (87, 76, 'ru', 'Со злостью') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (86, 75, 'en', 'Crying') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (85, 75, 'ru', 'Очень грустно') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (84, 74, 'en', 'Embaressed') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (83, 74, 'ru', 'Смущенно') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (82, 73, 'en', 'Kiss') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (81, 73, 'ru', 'Поцелуй') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (80, 72, 'en', 'Cool') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (79, 72, 'ru', 'Здорово') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (78, 71, 'en', 'Surprised') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (77, 71, 'ru', 'Удивленно') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (76, 70, 'en', 'Sad') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (75, 70, 'ru', 'Печально') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (74, 69, 'en', 'Smile') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (73, 69, 'ru', 'С улыбкой') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (72, 68, 'en', 'Big grin') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (71, 68, 'ru', 'Широкая улыбка') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (107, 86, 'ru', 'Вопрос') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (108, 86, 'en', 'Question') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (109, 87, 'ru', 'Плохо') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (110, 87, 'en', 'Thumbs Up') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (111, 88, 'ru', 'Хорошо') 
GO
insert  into b_blog_smile_lang(ID, SMILE_ID, LID, NAME) values (112, 88, 'en', 'Thumbs Down') 
GO

ALTER TABLE b_blog_smile_lang CHECK CONSTRAINT ALL
GO
ALTER TABLE b_blog_smile_lang ENABLE TRIGGER ALL
GO
SET IDENTITY_INSERT b_blog_smile_lang OFF
GO

CREATE TABLE B_BLOG_IMAGE (
  ID INT NOT NULL IDENTITY(1,1),
  FILE_ID INT NULL,
  BLOG_ID INT NULL,
  POST_ID INT NULL,
  USER_ID INT NULL,
  TIMESTAMP_X DATETIME NULL,
  TITLE VARCHAR(255) NULL,
  IMAGE_SIZE INT NULL
)
GO
ALTER TABLE B_BLOG_IMAGE ADD CONSTRAINT PK_B_B_BLOG_IMAGE PRIMARY KEY (ID)
GO
