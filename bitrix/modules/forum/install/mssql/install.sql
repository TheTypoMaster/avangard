CREATE TABLE B_FORUM_SMILE 
(
	ID int NOT NULL IDENTITY (1, 1),
	TYPE char(1) NOT NULL,
	TYPING varchar(100) NULL,
	IMAGE varchar(128) NOT NULL,
	DESCRIPTION varchar(50) NULL,
	CLICKABLE char(1) NOT NULL,
	SORT int NOT NULL,
	IMAGE_WIDTH int NOT NULL,
	IMAGE_HEIGHT int NOT NULL
)
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT PK_B_FORUM_SMILE PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT DF_B_FORUM_SMILE_TYPE DEFAULT 'S' FOR TYPE
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT DF_B_FORUM_SMILE_CLICKABLE DEFAULT 'Y' FOR CLICKABLE
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT DF_B_FORUM_SMILE_SORT DEFAULT '150' FOR SORT
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT DF_B_FORUM_SMILE_IMAGE_WIDTH DEFAULT '0' FOR IMAGE_WIDTH
GO
ALTER TABLE B_FORUM_SMILE ADD CONSTRAINT DF_B_FORUM_SMILE_IMAGE_HEIGHT DEFAULT '0' FOR IMAGE_HEIGHT
GO


CREATE TABLE B_FORUM
(
	ID int NOT NULL IDENTITY (1, 1),
	NAME varchar(128) NOT NULL,
	DESCRIPTION varchar(1000) NULL,
	SORT int NOT NULL,
	ACTIVE char(1) NOT NULL,
	ALLOW_HTML char(1) NOT NULL,
	ALLOW_ANCHOR char(1) NOT NULL,
	ALLOW_BIU char(1) NOT NULL,
	ALLOW_IMG char(1) NOT NULL,
	ALLOW_LIST char(1) NOT NULL,
	ALLOW_QUOTE char(1) NOT NULL,
	ALLOW_CODE char(1) NOT NULL,
	ALLOW_FONT char(1) NOT NULL,
	ALLOW_SMILES char(1) NOT NULL,
	ALLOW_UPLOAD char(1) NOT NULL,
	ALLOW_MOVE_TOPIC char(1) NOT NULL,
	MODERATION char(1) NOT NULL,
	ORDER_BY char(1) NOT NULL,
	ORDER_DIRECTION char(4) NOT NULL,
	LID char(2) NOT NULL,
	TOPICS int NOT NULL,
	POSTS int NOT NULL,
	LAST_POSTER_ID int NULL,
	LAST_POSTER_NAME varchar(64) NULL,
	LAST_POST_DATE datetime NULL,
	LAST_MESSAGE_ID int NULL,
	EVENT1 varchar(255) NULL,
	EVENT2 varchar(255) NULL,
	EVENT3 varchar(255) NULL,
	ALLOW_NL2BR char(1) NOT NULL,
	ALLOW_KEEP_AMP char(1) NOT NULL,
	PATH2FORUM_MESSAGE varchar(255) NULL,
	ALLOW_UPLOAD_EXT varchar(255) NULL,
	FORUM_GROUP_ID int NULL,
	ASK_GUEST_EMAIL char(1) NOT NULL,
	XML_ID varchar(255),
	USE_CAPTCHA char(1) NOT NULL,
	HTML varchar(255)
)
GO
ALTER TABLE B_FORUM ADD CONSTRAINT PK_B_FORUM PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM ADD CONSTRAINT FK_B_FORUM_B_USER FOREIGN KEY (LAST_POSTER_ID) REFERENCES B_USER(ID)
GO
CREATE INDEX IX_B_FORUM_1 ON B_FORUM(SORT)
GO
CREATE INDEX IX_B_FORUM_2 ON B_FORUM(ACTIVE)
GO
CREATE INDEX IX_B_FORUM_3 ON B_FORUM(FORUM_GROUP_ID)
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_SORT DEFAULT '150' FOR SORT
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_HTML DEFAULT 'N' FOR ALLOW_HTML
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_ANCHOR DEFAULT 'Y' FOR ALLOW_ANCHOR
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_BIU DEFAULT 'Y' FOR ALLOW_BIU
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_IMG DEFAULT 'Y' FOR ALLOW_IMG
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_LIST DEFAULT 'Y' FOR ALLOW_LIST
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_QUOTE DEFAULT 'Y' FOR ALLOW_QUOTE
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_CODE DEFAULT 'Y' FOR ALLOW_CODE
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_FONT DEFAULT 'Y' FOR ALLOW_FONT
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_SMILES DEFAULT 'Y' FOR ALLOW_SMILES
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_UPLOAD DEFAULT 'N' FOR ALLOW_UPLOAD
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_MODERATION DEFAULT 'N' FOR MODERATION
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ORDER_BY DEFAULT 'P' FOR ORDER_BY
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ORDER_DIRECTION DEFAULT 'DESC' FOR ORDER_DIRECTION
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_LID DEFAULT 'ru' FOR LID
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_TOPICS DEFAULT '0' FOR TOPICS
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_POSTS DEFAULT '0' FOR POSTS
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_EVENT1 DEFAULT 'forum' FOR EVENT1
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_EVENT2 DEFAULT 'message' FOR EVENT2
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_NL2BR DEFAULT 'N' FOR ALLOW_NL2BR
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ALLOW_KEEP_AMP DEFAULT 'N' FOR ALLOW_KEEP_AMP
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_ASK_GUEST_EMAIL DEFAULT 'N' FOR ASK_GUEST_EMAIL
GO
ALTER TABLE B_FORUM ADD CONSTRAINT DF_B_FORUM_USE_CAPTCHA DEFAULT 'N' FOR USE_CAPTCHA
GO


CREATE TABLE B_FORUM_TOPIC
(
	ID int NOT NULL IDENTITY (1, 1),
	TITLE varchar(70) NOT NULL,
	DESCRIPTION varchar(70) NULL,
	STATE char(1) NOT NULL,
	USER_START_ID int NULL,
	USER_START_NAME varchar(64) NOT NULL,
	START_DATE datetime NOT NULL,
	ICON_ID int NULL,
	POSTS int NOT NULL,
	VIEWS int NOT NULL,
	FORUM_ID int NOT NULL,
	APPROVED char(1) NOT NULL,
	SORT int NOT NULL,
	LAST_POSTER_ID int NULL,
	LAST_POSTER_NAME varchar(64) NOT NULL,
	LAST_POST_DATE datetime NOT NULL,
	LAST_MESSAGE_ID int NULL,
	XML_ID varchar(255) NULL,
	HTML text
)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT PK_B_FORUM_TOPIC PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT FK_B_FORUM_TOPIC_B_USER FOREIGN KEY (USER_START_ID) REFERENCES B_USER(ID)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT FK_B_FORUM_TOPIC_B_USER1 FOREIGN KEY (LAST_POSTER_ID) REFERENCES B_USER(ID)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT FK_B_FORUM_TOPIC_B_FORUM_SMILE FOREIGN KEY (ICON_ID) REFERENCES B_FORUM_SMILE(ID)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT FK_B_FORUM_TOPIC_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID)
GO
CREATE INDEX IX_B_FORUM_TOPIC_1 ON B_FORUM_TOPIC(FORUM_ID)
GO
CREATE INDEX IX_B_FORUM_TOPIC_2 ON B_FORUM_TOPIC(APPROVED)
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT DF_B_FORUM_TOPIC_STATE DEFAULT 'Y' FOR STATE
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT DF_B_FORUM_TOPIC_POSTS DEFAULT '0' FOR POSTS
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT DF_B_FORUM_TOPIC_VIEWS DEFAULT '0' FOR VIEWS
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT DF_B_FORUM_TOPIC_APPROVED DEFAULT 'Y' FOR APPROVED
GO
ALTER TABLE B_FORUM_TOPIC ADD CONSTRAINT DF_B_FORUM_TOPIC_SORT DEFAULT '150' FOR SORT
GO


CREATE TABLE B_FORUM_MESSAGE
(
	ID int NOT NULL IDENTITY (1, 1),
	AUTHOR_ID int NULL,
	AUTHOR_NAME varchar(128) NULL,
	AUTHOR_EMAIL varchar(128) NULL,
	AUTHOR_IP varchar(128) NULL,
	USE_SMILES char(1) NOT NULL,
	POST_DATE datetime NOT NULL,
	POST_MESSAGE text NULL,
	POST_MESSAGE_HTML text NULL,
	POST_MESSAGE_FILTER text NULL,
	FORUM_ID int NOT NULL,
	TOPIC_ID int NOT NULL,
	ATTACH_HITS int NOT NULL,
	ATTACH_TYPE varchar(128) NULL,
	ATTACH_FILE varchar(255) NULL,
	NEW_TOPIC char(1) NOT NULL,
	APPROVED char(1) NOT NULL,
	POST_MESSAGE_CHECK char(32) NULL,
	GUEST_ID int NULL,
	AUTHOR_REAL_IP varchar(128) NULL,
	ATTACH_IMG int NULL,
 XML_ID varchar(255) NULL
)
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT PK_B_FORUM_MESSAGE PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT FK_B_FORUM_MESSAGE_B_USER FOREIGN KEY (AUTHOR_ID) REFERENCES B_USER(ID)
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT FK_B_FORUM_MESSAGE_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID)
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT FK_B_FORUM_MESSAGE_B_FORUM_TOPIC FOREIGN KEY (TOPIC_ID) REFERENCES B_FORUM_TOPIC(ID)
GO
CREATE INDEX IX_FORUM_MESSAGE_1 ON B_FORUM_MESSAGE(FORUM_ID)
GO
CREATE INDEX IX_FORUM_MESSAGE_2 ON B_FORUM_MESSAGE(TOPIC_ID, AUTHOR_ID)
GO
CREATE INDEX IX_FORUM_MESSAGE_3 ON B_FORUM_MESSAGE(AUTHOR_ID)
GO
CREATE INDEX IX_FORUM_MESSAGE_4 ON B_FORUM_MESSAGE(APPROVED)
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT DF_B_FORUM_MESSAGE_USE_SMILES DEFAULT 'Y' FOR USE_SMILES
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT DF_B_FORUM_MESSAGE_ATTACH_HITS DEFAULT '0' FOR ATTACH_HITS
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT DF_B_FORUM_MESSAGE_NEW_TOPIC DEFAULT 'N' FOR NEW_TOPIC
GO
ALTER TABLE B_FORUM_MESSAGE ADD CONSTRAINT DF_B_FORUM_MESSAGE_APPROVED DEFAULT 'Y' FOR APPROVED
GO


CREATE TABLE B_FORUM_USER
(
	ID int NOT NULL IDENTITY (1, 1),
	USER_ID int NOT NULL,
	ALIAS varchar(64) NULL,
	DESCRIPTION varchar(64) NULL,
	IP_ADDRESS varchar(128) NULL,
	AVATAR int NULL,
	NUM_POSTS int NOT NULL,
	INTERESTS text NULL,
	LAST_POST int NULL,
	ALLOW_POST char(1) NOT NULL,
	LAST_VISIT datetime NOT NULL,
	DATE_REG datetime NOT NULL,
	REAL_IP_ADDRESS varchar(128) NULL,
	SIGNATURE varchar(255) NULL,
	SHOW_NAME char(1) NOT NULL,
	RANK_ID int NULL,
	POINTS int NOT NULL,
	HIDE_FROM_ONLINE char(1) NOT NULL,
	SUBSC_GROUP_MESSAGE char(1) DEFAULT 'N' NULL,
	SUBSC_GET_MY_MESSAGE char(1) DEFAULT 'Y' NULL
)
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT PK_B_FORUM_USER PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT FK_B_FORUM_USER_B_USER FOREIGN KEY (USER_ID) REFERENCES B_USER(ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_USER_1 ON B_FORUM_USER(USER_ID)
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT DF_B_FORUM_USER_NUM_POSTS DEFAULT '0' FOR NUM_POSTS
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT DF_B_FORUM_USER_ALLOW_POST DEFAULT 'Y' FOR ALLOW_POST
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT DF_B_FORUM_USER_SHOW_NAME DEFAULT 'Y' FOR SHOW_NAME
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT DF_B_FORUM_USER_POINTS DEFAULT '0' FOR POINTS
GO
ALTER TABLE B_FORUM_USER ADD CONSTRAINT DF_B_FORUM_USER_HIDE_FROM_ONLINE DEFAULT 'N' FOR HIDE_FROM_ONLINE
GO


CREATE TABLE B_FORUM_PERMS
(
	ID int NOT NULL IDENTITY (1, 1),
	FORUM_ID int NOT NULL,
	GROUP_ID int NOT NULL,
	PERMISSION char(1) NOT NULL
)
GO
ALTER TABLE B_FORUM_PERMS ADD CONSTRAINT PK_B_FORUM_PERMS PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_PERMS ADD CONSTRAINT FK_B_FORUM_PERMS_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID)
GO
ALTER TABLE B_FORUM_PERMS ADD CONSTRAINT FK_B_FORUM_PERMS_B_GROUP FOREIGN KEY (GROUP_ID) REFERENCES B_GROUP(ID)
GO
CREATE INDEX IX_FORUM_PERMS_1 ON B_FORUM_PERMS(FORUM_ID, GROUP_ID)
GO
CREATE INDEX IX_FORUM_PERMS_2 ON B_FORUM_PERMS(GROUP_ID)
GO
ALTER TABLE B_FORUM_PERMS ADD CONSTRAINT DF_B_FORUM_PERMS_PERMISSION DEFAULT 'M' FOR PERMISSION
GO


CREATE TABLE B_FORUM_SUBSCRIBE
(
	ID int NOT NULL IDENTITY (1, 1),
	USER_ID int NOT NULL,
	FORUM_ID int NOT NULL,
	TOPIC_ID int NULL,
	START_DATE datetime NOT NULL,
	LAST_SEND int NULL,
	NEW_TOPIC_ONLY char(1) NOT NULL,
	SITE_ID char(2) NOT NULL
)
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT PK_B_FORUM_SUBSCRIBE PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT FK_FORUM_SUBSCRIBE_USER FOREIGN KEY (USER_ID) REFERENCES B_USER(ID)
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT FK_FORUM_SUBSCRIBE_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID)
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT FK_FORUM_SUB_FORUM_TOPIC FOREIGN KEY (TOPIC_ID) REFERENCES B_FORUM_TOPIC(ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_SUBSCRIBE_1 ON B_FORUM_SUBSCRIBE(USER_ID, FORUM_ID, TOPIC_ID)
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT DF_B_FORUM_SUBSCRIBE_NEW_TOPIC_ONLY DEFAULT 'N' FOR NEW_TOPIC_ONLY
GO
ALTER TABLE B_FORUM_SUBSCRIBE ADD CONSTRAINT DF_B_FORUM_SUBSCRIBE_SITE_ID DEFAULT 'ru' FOR SITE_ID
GO


CREATE TABLE B_FORUM_RANK
(
	ID int NOT NULL IDENTITY (1, 1),
	CODE varchar(100) NULL,
	MIN_NUM_POSTS int NOT NULL
)
GO
ALTER TABLE B_FORUM_RANK ADD CONSTRAINT PK_B_FORUM_RANK PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_RANK ADD CONSTRAINT DF_B_FORUM_RANK_MIN_NUM_POSTS DEFAULT '0' FOR MIN_NUM_POSTS
GO


CREATE TABLE B_FORUM_RANK_LANG
(
	ID int NOT NULL IDENTITY (1, 1),
	RANK_ID int NOT NULL,
	LID char(2) NOT NULL,
	NAME varchar(100) NOT NULL
)
GO
ALTER TABLE B_FORUM_RANK_LANG ADD CONSTRAINT PK_B_FORUM_RANK_LANG PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_RANK_LANG_1 ON B_FORUM_RANK_LANG(RANK_ID, LID)
GO


CREATE TABLE B_FORUM_GROUP
(
	ID int NOT NULL IDENTITY (1, 1),
	SORT int NOT NULL,
 XML_ID varchar(255) NULL
)
GO
ALTER TABLE B_FORUM_GROUP ADD CONSTRAINT PK_B_FORUM_GROUP PRIMARY KEY (ID)
GO
ALTER TABLE B_FORUM_GROUP ADD CONSTRAINT DF_B_FORUM_GROUP_SORT DEFAULT '150' FOR SORT
GO


CREATE TABLE B_FORUM_GROUP_LANG
(
	ID int NOT NULL IDENTITY (1, 1),
	FORUM_GROUP_ID int NOT NULL,
	LID char(2) NOT NULL,
	NAME varchar(255) NOT NULL,
	DESCRIPTION varchar(255) NULL
)
GO
ALTER TABLE B_FORUM_GROUP_LANG ADD CONSTRAINT PK_B_FORUM_GROUP_LANG PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_GROUP_LANG ON B_FORUM_GROUP_LANG(FORUM_GROUP_ID, LID)
GO


CREATE TABLE B_FORUM_SMILE_LANG
(
	ID int NOT NULL IDENTITY (1, 1),
	SMILE_ID int NOT NULL,
	LID char(2) NOT NULL,
	NAME varchar(255) NOT NULL
)
GO
ALTER TABLE B_FORUM_SMILE_LANG ADD CONSTRAINT PK_B_FORUM_SMILE_LANG PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_SMILE_LANG_1 ON B_FORUM_SMILE_LANG(SMILE_ID, LID)
GO


CREATE TABLE B_FORUM_POINTS
(
	ID int NOT NULL IDENTITY (1, 1),
	MIN_POINTS int NOT NULL,
	CODE varchar(100) NULL,
	VOTES int NOT NULL
)
GO
ALTER TABLE B_FORUM_POINTS ADD CONSTRAINT PK_B_FORUM_POINTS PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_POINTS_1 ON B_FORUM_POINTS(MIN_POINTS)
GO


CREATE TABLE B_FORUM_POINTS_LANG
(
	POINTS_ID int NOT NULL,
	LID char(2) NOT NULL,
	NAME varchar(250) NULL
)
GO
ALTER TABLE B_FORUM_POINTS_LANG ADD CONSTRAINT PK_B_FORUM_POINTS_LANG PRIMARY KEY (POINTS_ID, LID)
GO


CREATE TABLE B_FORUM_POINTS2POST
(
	ID int NOT NULL IDENTITY (1, 1),
	MIN_NUM_POSTS int NOT NULL,
	POINTS_PER_POST decimal(18, 4) NOT NULL
)
GO
ALTER TABLE B_FORUM_POINTS2POST ADD CONSTRAINT PK_B_FORUM_POINTS2POST PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_FORUM_POINTS2POST_1 ON B_FORUM_POINTS2POST(MIN_NUM_POSTS)
GO
ALTER TABLE B_FORUM_POINTS2POST ADD CONSTRAINT DF_B_FORUM_POINTS2POST_POINTS_PER_POST DEFAULT '0' FOR POINTS_PER_POST
GO


CREATE TABLE B_FORUM_USER_POINTS
(
	FROM_USER_ID int NOT NULL,
	TO_USER_ID int NOT NULL,
	POINTS int NOT NULL,
	DATE_UPDATE datetime NULL
)
GO
ALTER TABLE B_FORUM_USER_POINTS ADD CONSTRAINT PK_B_FORUM_USER_POINTS PRIMARY KEY (FROM_USER_ID, TO_USER_ID)
GO
ALTER TABLE B_FORUM_USER_POINTS ADD CONSTRAINT DF_B_FORUM_USER_POINTS_POINTS DEFAULT '0' FOR POINTS
GO


CREATE TABLE B_FORUM2SITE
(
	FORUM_ID int NOT NULL,
	SITE_ID char(2) NOT NULL,
	PATH2FORUM_MESSAGE varchar(250) NULL
)
GO
ALTER TABLE B_FORUM2SITE ADD CONSTRAINT PK_B_FORUM2SITE PRIMARY KEY (FORUM_ID, SITE_ID)
GO
CREATE TABLE B_FORUM_PM_FOLDER
(
	ID int IDENTITY(1,1),
	TITLE varchar(255),
	USER_ID int,
	SORT int,
 CONSTRAINT PK_B_FORUM_PM_FOLDER PRIMARY KEY(ID)
)
GO
CREATE INDEX IX_PMF_FOLDER_ID ON B_FORUM_PM_FOLDER (USER_ID)
GO
CREATE TABLE B_FORUM_FILTER (
	ID INT NOT NULL IDENTITY (1, 1),
	DICTIONARY_ID INT,
	WORDS VARCHAR(255),
 	PATTERN TEXT,
   	REPLACEMENT VARCHAR(255),
   	DESCRIPTION TEXT,
   	USE_IT CHAR(1),
   	PATTERN_CREATE VARCHAR(5),
   	CONSTRAINT PK_B_FORUM_FILTER_KEY PRIMARY KEY(ID)
)
GO
CREATE INDEX IX_B_FORUM_FILTER_2 ON B_FORUM_FILTER(USE_IT)
GO
CREATE INDEX IX_B_FORUM_FILTER_3 ON B_FORUM_FILTER(PATTERN_CREATE)
GO
CREATE TABLE B_FORUM_PRIVATE_MESSAGE
(
	ID int IDENTITY(1,1),
	AUTHOR_ID int ,
	POST_DATE datetime ,
	POST_SUBJ varchar(255),
	POST_MESSAGE text,
	USER_ID int,
	RECIPIENT_ID int,
	FOLDER_ID int,
	IS_READ char(1),
	USE_SMILES char(1),
	CONSTRAINT PK_B_FORUM_PRIVATE_MESSAGE PRIMARY KEY(ID)
)
GO
CREATE INDEX I_PM_USER_ID ON B_FORUM_PRIVATE_MESSAGE (USER_ID)
GO
CREATE INDEX I_PM_FOLDER_ID ON B_FORUM_PRIVATE_MESSAGE (FOLDER_ID)
GO
CREATE TABLE B_FORUM_DICTIONARY (
	ID INT NOT NULL IDENTITY (1, 1),
   	TITLE VARCHAR(255),
   	[TYPE] CHAR(1),
   	CONSTRAINT PK_B_FORUM_DICTIONARY  PRIMARY KEY(ID)
)
GO
CREATE TABLE B_FORUM_LETTER (
	ID INT NOT NULL IDENTITY (1, 1),
   	DICTIONARY_ID INT,
   	LETTER VARCHAR(50),
   	REPLACEMENT VARCHAR(255),
   	CONSTRAINT PK_B_FORUM_LETTER  PRIMARY KEY(ID)
)
GO
CREATE UNIQUE INDEX UX_B_FORUM_LETTER ON B_FORUM_LETTER (DICTIONARY_ID, LETTER)
GO
CREATE TABLE B_FORUM_USER_TOPIC (
	ID INT NOT NULL IDENTITY (1, 1),
	TOPIC_ID INT,
   	USER_ID INT,
   	FORUM_ID INT,
   	LAST_VISIT DATETIME,
   	CONSTRAINT PK_B_FORUM_USER_TOPIC  PRIMARY KEY(TOPIC_ID, USER_ID)
)
GO
CREATE TABLE B_FORUM_USER_FORUM (
	ID INT NOT NULL IDENTITY (1, 1),
	USER_ID INT,
	FORUM_ID INT,
	LAST_VISIT DATETIME,
	MAIN_LAST_VISIT DATETIME,
   	CONSTRAINT PK_B_FORUM_USER_FORUM PRIMARY KEY(ID)
)
GO
CREATE INDEX IX_B_FORUM_USER_FORUM ON B_FORUM_USER_FORUM (USER_ID)
GO
CREATE TABLE B_FORUM_STAT (
	ID INT NOT NULL IDENTITY (1, 1),
	USER_ID INT,
	IP_ADDRESS VARCHAR(128),
	PHPSESSID VARCHAR(255),
	LAST_VISIT DATETIME,
	FORUM_ID INT,
	TOPIC_ID INT,
	SHOW_NAME VARCHAR(101) DEFAULT NULL
)
GO
CREATE INDEX IX_B_FORUM_STAT_TOPIC_ID ON B_FORUM_STAT(TOPIC_ID)
GO
CREATE INDEX IX_B_FORUM_STAT_FORUM_ID ON B_FORUM_STAT(FORUM_ID)
GO
CREATE INDEX IX_B_FORUM_STAT_PHPSESSID ON B_FORUM_STAT(PHPSESSID)
GO