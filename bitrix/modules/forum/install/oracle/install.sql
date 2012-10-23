CREATE TABLE B_FORUM_SMILE (
	ID number(3) not null,
	TYPE char(1) default 'S' not null,
	TYPING varchar2(100) null,
	IMAGE varchar2(128) not null,
	DESCRIPTION varchar2(50) null,
	CLICKABLE char(1) default 'Y' not null,
	SORT number(18) default '150' not null,
	IMAGE_WIDTH NUMBER(18) DEFAULT 0 NOT NULL,
	IMAGE_HEIGHT NUMBER(18) DEFAULT 0 NOT NULL,
	PRIMARY KEY (ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_SMILE INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_SMILE_insert
BEFORE INSERT
ON B_FORUM_SMILE
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_SMILE.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM (
    ID               NUMBER(18)     NOT NULL,
    NAME             VARCHAR2(128)  NOT NULL,
    DESCRIPTION      VARCHAR2(1000)     NULL,
    SORT             NUMBER(18)     DEFAULT '150' NOT NULL,
    ACTIVE           CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_HTML       CHAR(1)        DEFAULT 'N' NOT NULL,
    ALLOW_ANCHOR     CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_BIU        CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_IMG        CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_LIST       CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_QUOTE      CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_CODE       CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_FONT       CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_SMILES     CHAR(1)        DEFAULT 'Y' NOT NULL,
    ALLOW_UPLOAD     CHAR(1)        DEFAULT 'N' NOT NULL,
    ALLOW_MOVE_TOPIC CHAR(1)        DEFAULT 'Y' NOT NULL,
    MODERATION       CHAR(1)        DEFAULT 'N' NOT NULL,
    ORDER_BY         CHAR(1)        DEFAULT 'P' NOT NULL,
    ORDER_DIRECTION  CHAR(4)        DEFAULT 'DESC' NOT NULL,
    LID              CHAR(2)        DEFAULT 'ru' NOT NULL,
    TOPICS           NUMBER(18)     DEFAULT '0' NOT NULL,
    POSTS            NUMBER(18)     DEFAULT '0' NOT NULL,
    LAST_POSTER_ID   NUMBER(18)         NULL,
    LAST_POSTER_NAME VARCHAR2(64)       NULL,
    LAST_POST_DATE   DATE               NULL,
    LAST_MESSAGE_ID  NUMBER(18)         NULL,
    EVENT1           VARCHAR2(255)  DEFAULT 'forum'     NULL,
    EVENT2           VARCHAR2(255)  DEFAULT 'message'     NULL,
    EVENT3           VARCHAR2(255)      NULL,
    ALLOW_NL2BR      CHAR(1)        DEFAULT 'N' NOT NULL,
    ALLOW_KEEP_AMP   CHAR(1)        DEFAULT 'N' NOT NULL,
	 PATH2FORUM_MESSAGE VARCHAR2(255) NULL,
	 ALLOW_UPLOAD_EXT VARCHAR2(255) NULL,
	 FORUM_GROUP_ID NUMBER(18) NULL,
	 ASK_GUEST_EMAIL CHAR(1) DEFAULT 'N' NOT NULL,
    XML_ID VARCHAR2(255) NULL,
	HTML VARCHAR2(255) NULL,
	 USE_CAPTCHA CHAR(1) DEFAULT 'N' NOT NULL,
    PRIMARY KEY(ID),
    CONSTRAINT FK_B_FORUM_B_USER FOREIGN KEY (LAST_POSTER_ID) REFERENCES B_USER(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_insert
BEFORE INSERT
ON B_FORUM
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX ix_forum_sort ON B_FORUM(SORT)
/
CREATE INDEX ix_forum_active ON B_FORUM(ACTIVE)
/
CREATE INDEX IX_FORUM_GROUP_ID ON B_FORUM(FORUM_GROUP_ID)
/

CREATE TABLE B_FORUM_TOPIC (
	ID number(20) not null,
	TITLE varchar2(70) not null,
	DESCRIPTION varchar2(70),
	STATE char(1) default 'Y' not null,
	USER_START_ID number(18),
	USER_START_NAME varchar2(64) not null,
	START_DATE date not null,
	ICON_ID number(3),
	POSTS number(18) default '0' not null,
	VIEWS number(18) default '0' not null,
	FORUM_ID number(18) not null,
	APPROVED char(1) default 'Y' not null,
	SORT number(18) default '150' not null,
	LAST_POSTER_ID number(18),
	LAST_POSTER_NAME varchar2(64) not null,
	LAST_POST_DATE date not null,
	LAST_MESSAGE_ID number(20) null,
	XML_ID VARCHAR2(255) NULL,
	HTML CLOB NULL,
	PRIMARY KEY (ID),
	CONSTRAINT FK_B_FORUM_TOPIC_B_USER FOREIGN KEY (USER_START_ID) REFERENCES B_USER(ID),
	CONSTRAINT FK_B_FORUM_TOPIC_B_USER1 FOREIGN KEY (LAST_POSTER_ID) REFERENCES B_USER(ID),
	CONSTRAINT FK_B_FORUM_TOPIC_B_FORUM_SMILE FOREIGN KEY (ICON_ID) REFERENCES B_FORUM_SMILE(ID),
	CONSTRAINT FK_B_FORUM_TOPIC_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_TOPIC INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_TOPIC_insert
BEFORE INSERT
ON B_FORUM_TOPIC
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_TOPIC.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX ix_forum_topic_forum ON B_FORUM_TOPIC(FORUM_ID)
/
CREATE INDEX FR_APPROVED5_IX ON B_FORUM_TOPIC(APPROVED)
/

CREATE TABLE B_FORUM_MESSAGE (
	ID number(20) not null,
	AUTHOR_ID number(18) null,
	AUTHOR_NAME varchar2(128) null,
	AUTHOR_EMAIL varchar2(128) null,
	AUTHOR_IP varchar2(128) null,
	USE_SMILES char(1) default 'Y' not null,
	POST_DATE date not null,
	POST_MESSAGE clob,
	POST_MESSAGE_HTML clob,
	POST_MESSAGE_FILTER clob,
	FORUM_ID number(18) not null,
	TOPIC_ID number(20) not null,
	ATTACH_HITS number(10) default '0' not null,
	ATTACH_TYPE varchar(128) null,
	ATTACH_FILE varchar(255) null,
	NEW_TOPIC char(1) default 'N' not null,
	APPROVED char(1) default 'Y' not null,
	POST_MESSAGE_CHECK char(32),
	GUEST_ID NUMBER(18) NULL,
	AUTHOR_REAL_IP VARCHAR2(128) NULL,
	ATTACH_IMG NUMBER(18) NULL,
   XML_ID VARCHAR2(255) NULL,
	PRIMARY KEY (ID),
	CONSTRAINT FK_B_FORUM_MESSAGE_B_USER FOREIGN KEY (AUTHOR_ID) REFERENCES B_USER(ID),
	CONSTRAINT FK_B_FORUM_MESSAGE_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID),
	CONSTRAINT FK_FORUM_MESS_FORUM_TOPIC FOREIGN KEY (TOPIC_ID) REFERENCES B_FORUM_TOPIC(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_MESSAGE INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE ORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_MESSAGE_insert
BEFORE INSERT
ON B_FORUM_MESSAGE
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_MESSAGE.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX ix_forum_message_forum ON B_FORUM_MESSAGE(FORUM_ID)
/
CREATE INDEX ix_forum_message_topic ON B_FORUM_MESSAGE(TOPIC_ID, AUTHOR_ID)
/
CREATE INDEX ix_forum_message_author ON B_FORUM_MESSAGE(AUTHOR_ID)
/
CREATE INDEX FR_APPROVED_IX ON B_FORUM_MESSAGE(APPROVED)
/

CREATE TABLE B_FORUM_USER (
	ID number(18) not null,
	USER_ID number(18) not null,
	ALIAS varchar2(64) null,
	DESCRIPTION varchar2(64) null,
	IP_ADDRESS varchar2(128) null,
	AVATAR number(18) null,
	NUM_POSTS number(10) default '0' not null,
	INTERESTS clob,
	LAST_POST number(18),
	ALLOW_POST char(1) default 'Y' not null,
	LAST_VISIT date not null,
	DATE_REG date not null,
	REAL_IP_ADDRESS VARCHAR2(128) NULL,
	SIGNATURE VARCHAR2(255) NULL,
	SHOW_NAME CHAR(1) DEFAULT 'Y' NOT NULL,
	RANK_ID NUMBER(18) NULL,
	POINTS NUMBER(18) DEFAULT 0 NOT NULL,
	HIDE_FROM_ONLINE CHAR(1) DEFAULT 'N' NOT NULL,
	SUBSC_GROUP_MESSAGE char(1) DEFAULT 'N' NOT NULL,
	SUBSC_GET_MY_MESSAGE char(1) DEFAULT 'Y' NOT NULL,
	PRIMARY KEY (ID),
	CONSTRAINT FK_B_FORUM_USER_B_USER FOREIGN KEY (USER_ID) REFERENCES B_USER(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_USER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_USER_insert
BEFORE INSERT
ON B_FORUM_USER
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_USER.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX IX_FORUM_USER_USER6 ON B_FORUM_USER(USER_ID)
/

CREATE TABLE B_FORUM_PERMS
(
	ID number(18) not null,
	FORUM_ID number(18) not null,
	GROUP_ID number(18) not null,
	PERMISSION char(1) default 'M' not null,
	PRIMARY KEY (ID),
	CONSTRAINT FK_B_FORUM_PERMS_B_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID),
	CONSTRAINT FK_B_FORUM_PERMS_B_GROUP FOREIGN KEY (GROUP_ID) REFERENCES B_GROUP(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_PERMS INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_PERMS_insert
BEFORE INSERT
ON B_FORUM_PERMS
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_PERMS.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX ix_forum_perms_forum ON B_FORUM_PERMS(FORUM_ID, GROUP_ID)
/
CREATE INDEX ix_forum_perms_group ON B_FORUM_PERMS(GROUP_ID)
/

CREATE TABLE B_FORUM_SUBSCRIBE (
	ID number(18) not null,
	USER_ID number(18) not null,
	FORUM_ID number(18) not null,
	TOPIC_ID number(18) null,
	START_DATE date not null,
	LAST_SEND number(18) null,
	NEW_TOPIC_ONLY CHAR(1) DEFAULT 'N' NOT NULL,
	SITE_ID CHAR(2) DEFAULT 'ru' NOT NULL,
	PRIMARY KEY (ID),
	CONSTRAINT FK_FORUM_SUBSCRIBE_USER FOREIGN KEY (USER_ID) REFERENCES B_USER(ID),
	CONSTRAINT FK_FORUM_SUBSCRIBE_FORUM FOREIGN KEY (FORUM_ID) REFERENCES B_FORUM(ID),
	CONSTRAINT FK_FORUM_SUB_FORUM_TOPIC FOREIGN KEY (TOPIC_ID) REFERENCES B_FORUM_TOPIC(ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_SUBSCRIBE INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_SUBSCRIBE_insert
BEFORE INSERT
ON B_FORUM_SUBSCRIBE
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_B_FORUM_SUBSCRIBE.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE UNIQUE INDEX FR_SUBSC_IX ON B_FORUM_SUBSCRIBE(USER_ID, FORUM_ID, TOPIC_ID)
/

CREATE TABLE B_FORUM_RANK
(
  ID NUMBER(18) NOT NULL,
  CODE VARCHAR2(100) NULL,
  MIN_NUM_POSTS NUMBER(18) DEFAULT 0 NOT NULL,
  PRIMARY KEY (ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_RANK INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_RANK_INSERT
BEFORE INSERT
ON B_FORUM_RANK
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_RANK.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_RANK_LANG
(
  ID NUMBER(18) NOT NULL,
  RANK_ID NUMBER(18) NOT NULL,
  LID CHAR(2) NOT NULL,
  NAME VARCHAR2(100) NOT NULL,
  PRIMARY KEY (ID)
)
/

CREATE UNIQUE INDEX IX_FORUM_RANK ON B_FORUM_RANK_LANG(RANK_ID, LID)
/

CREATE SEQUENCE SQ_B_FORUM_RANK_LANG INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_RANK_LANG_INSERT
BEFORE INSERT
ON B_FORUM_RANK_LANG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_RANK_LANG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_GROUP
(
  ID NUMBER(18) NOT NULL,
  SORT NUMBER(18) DEFAULT 150 NOT NULL,
  XML_ID VARCHAR2(255) NULL,
  PRIMARY KEY (ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_GROUP INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_GROUP_INSERT
BEFORE INSERT
ON B_FORUM_GROUP
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_GROUP.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_GROUP_LANG
(
  ID NUMBER(18) NOT NULL,
  FORUM_GROUP_ID NUMBER(18) NOT NULL,
  LID CHAR(2) NOT NULL,
  NAME VARCHAR2(255) NOT NULL,
  DESCRIPTION VARCHAR2(255) NULL,
  PRIMARY KEY (ID)
)
/

CREATE UNIQUE INDEX FORUM_GROUP_IX ON B_FORUM_GROUP_LANG(FORUM_GROUP_ID, LID)
/

CREATE SEQUENCE SQ_B_FORUM_GROUP_LANG INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_GROUP_LANG_INSERT
BEFORE INSERT
ON B_FORUM_GROUP_LANG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_GROUP_LANG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_SMILE_LANG
(
  ID NUMBER(18) NOT NULL,
  SMILE_ID NUMBER(18) NOT NULL,
  LID CHAR(2) NOT NULL,
  NAME VARCHAR2(255) NOT NULL,
  PRIMARY KEY (ID)
)
/

CREATE UNIQUE INDEX IX_SMILE_K ON B_FORUM_SMILE_LANG(SMILE_ID, LID)
/

CREATE SEQUENCE SQ_B_FORUM_SMILE_LANG INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_SMILE_LANG_INSERT
BEFORE INSERT
ON B_FORUM_SMILE_LANG
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_SMILE_LANG.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_POINTS
(
  ID NUMBER(18) NOT NULL,
  MIN_POINTS NUMBER(18) NOT NULL,
  CODE VARCHAR2(100) NULL,
  VOTES NUMBER(18) NOT NULL,
  PRIMARY KEY (ID)
)
/

CREATE SEQUENCE SQ_B_FORUM_POINTS INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE UNIQUE INDEX IX_FORUM_P_MP ON B_FORUM_POINTS(MIN_POINTS)
/

CREATE OR REPLACE TRIGGER B_FORUM_POINTS_INSERT
BEFORE INSERT
ON B_FORUM_POINTS
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_POINTS.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_POINTS_LANG
(
  POINTS_ID NUMBER(18) NOT NULL,
  LID CHAR(2) NOT NULL,
  NAME VARCHAR2(250) NULL,
  PRIMARY KEY (POINTS_ID, LID)
)
/

CREATE TABLE B_FORUM_POINTS2POST
(
  ID NUMBER(18) NOT NULL,
  MIN_NUM_POSTS NUMBER(18) NOT NULL,
  POINTS_PER_POST NUMBER(18, 4) DEFAULT 0 NOT NULL,
  PRIMARY KEY (ID)
)
/

CREATE UNIQUE INDEX IX_FORUM_P2P_MNP ON B_FORUM_POINTS2POST(MIN_NUM_POSTS)
/

CREATE SEQUENCE SQ_B_FORUM_POINTS2POST INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_FORUM_POINTS2POST_INSERT
BEFORE INSERT
ON B_FORUM_POINTS2POST
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
		SELECT SQ_B_FORUM_POINTS2POST.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_USER_POINTS
(
  FROM_USER_ID NUMBER(18) NOT NULL,
  TO_USER_ID NUMBER(18) NOT NULL,
  POINTS NUMBER(18) DEFAULT 0 NOT NULL,
  DATE_UPDATE DATE NULL,
  PRIMARY KEY (FROM_USER_ID, TO_USER_ID)
)
/

CREATE TABLE B_FORUM2SITE
(
  FORUM_ID NUMBER(18) NOT NULL,
  SITE_ID CHAR(2) NOT NULL,
  PATH2FORUM_MESSAGE VARCHAR2(250) NULL,
  PRIMARY KEY (FORUM_ID, SITE_ID)
)
/
CREATE TABLE B_FORUM_PM_FOLDER 
(	
	ID NUMBER(11), 
	TITLE VARCHAR2(255 CHAR), 
	USER_ID NUMBER(11), 
	SORT NUMBER(11), 
 	PRIMARY KEY (ID) 
)
/
CREATE SEQUENCE SQ_B_FORUM_PM_FOLDER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_PM_FOLDER_INSERT
BEFORE INSERT
ON B_FORUM_PM_FOLDER
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_PM_FOLDER.NEXTVAL INTO :NEW.ID FROM DUAL;
	END IF;
END;
/
CREATE UNIQUE INDEX UX_B_FORUM_PM_FOLDER ON B_FORUM_PM_FOLDER (TITLE, USER_ID)
/
CREATE INDEX IU_B_FORUM_PM_FOLDER ON B_FORUM_PM_FOLDER (USER_ID)
/
CREATE TABLE B_FORUM_PRIVATE_MESSAGE 
(	
ID NUMBER(11), 
AUTHOR_ID NUMBER(11), 
POST_DATE DATE, 
POST_SUBJ VARCHAR2(4000 CHAR), 
POST_MESSAGE CLOB, 
USER_ID NUMBER(11), 
RECIPIENT_ID NUMBER(11), 
FOLDER_ID NUMBER(7), 
IS_READ VARCHAR2(1 CHAR), 
USE_SMILES VARCHAR2(1 CHAR), 
PRIMARY KEY (ID)
)
/
CREATE SEQUENCE SQ_B_FORUM_PRIVATE_MESSAGE INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_PRIVATE_MESSAGE_INSERT
BEFORE INSERT
ON B_FORUM_PRIVATE_MESSAGE
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_PRIVATE_MESSAGE.NEXTVAL INTO :NEW.ID FROM DUAL;
	END IF;
END;
/
CREATE INDEX IF_B_FORUM_PM_FOLDER ON B_FORUM_PRIVATE_MESSAGE (FOLDER_ID)
/
CREATE INDEX IF_B_FORUM_PM_USER ON B_FORUM_PRIVATE_MESSAGE (USER_ID)
/
CREATE TABLE B_FORUM_FILTER (
	ID NUMBER(11),
	DICTIONARY_ID NUMBER(11),
	WORDS VARCHAR2(255),
   	PATTERN CLOB,
   	REPLACEMENT VARCHAR2(255),
   	DESCRIPTION CLOB,
   	USE_IT CHAR(1),
   	PATTERN_CREATE VARCHAR2(5),
   	PRIMARY KEY (ID))
/
CREATE SEQUENCE SQ_B_FORUM_FILTER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_FILTER_INSERT
BEFORE INSERT
ON B_FORUM_FILTER
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_FILTER.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE INDEX IX_B_FORUM_FILTER_2 ON B_FORUM_FILTER(USE_IT)
/
CREATE INDEX IX_B_FORUM_FILTER_3 ON B_FORUM_FILTER(PATTERN_CREATE)
/
CREATE TABLE B_FORUM_DICTIONARY (
	ID NUMBER(11),
   	TITLE VARCHAR2(50),
   	TYPE CHAR(1),
   	PRIMARY KEY(ID)
   	)
/
CREATE SEQUENCE SQ_B_FORUM_DICTIONARY START WITH 5 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_DICTIONARY_INSERT
BEFORE INSERT
ON B_FORUM_DICTIONARY
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_DICTIONARY.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE TABLE B_FORUM_LETTER (
	ID NUMBER(11),
   	DICTIONARY_ID  NUMBER(11),
   	LETTER VARCHAR2(50),
   	REPLACEMENT VARCHAR2(255),
   	PRIMARY KEY (ID)
   	)
/
CREATE SEQUENCE SQ_B_FORUM_LETTER INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_LETTER_INSERT
BEFORE INSERT
ON B_FORUM_LETTER
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_LETTER.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
INSERT INTO b_option(SITE_ID, MODULE_ID, NAME, VALUE, DESCRIPTION) VALUES (NULL, 'forum', 'FILTER', 'N', '')
/


CREATE TABLE B_FORUM_USER_TOPIC (
	ID NUMBER(18),
	TOPIC_ID NUMBER(11),
   	USER_ID  NUMBER(11),
   	FORUM_ID NUMBER(11),
   	LAST_VISIT DATE, 
   	PRIMARY KEY (TOPIC_ID, USER_ID)
   	)
/
CREATE SEQUENCE SQ_B_FORUM_USER_TOPIC INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_USER_TOPIC_INSERT
BEFORE INSERT
ON B_FORUM_USER_TOPIC
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_USER_TOPIC.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE TABLE B_FORUM_USER_FORUM (
	ID NUMBER(18),
   	USER_ID  NUMBER(11),
   	FORUM_ID NUMBER(11),
   	LAST_VISIT DATE, 
   	MAIN_LAST_VISIT DATE
)
/
CREATE INDEX IX_B_FORUM_USER_FORUM_ID ON B_FORUM_USER_FORUM(USER_ID)
/
CREATE SEQUENCE SQ_B_FORUM_USER_FORUM INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_USER_FORUM_INSERT
BEFORE INSERT
ON B_FORUM_USER_FORUM
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_USER_FORUM.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE B_FORUM_STAT (
  ID NUMBER(18),
  USER_ID NUMBER(10),
  IP_ADDRESS varchar(128),
  PHPSESSID varchar(255),
  LAST_VISIT date,
  FORUM_ID NUMBER(5),
  TOPIC_ID NUMBER(10),
  SHOW_NAME varchar(101)
)
/
CREATE INDEX IX_B_FORUM_STAT_TOPIC_ID ON B_FORUM_STAT(TOPIC_ID)
/
CREATE INDEX IX_B_FORUM_STAT_FORUM_ID ON B_FORUM_STAT(FORUM_ID)
/
CREATE INDEX IX_B_FORUM_STAT_PHPSESSID ON B_FORUM_STAT(PHPSESSID)
/
CREATE SEQUENCE SQ_B_FORUM_STAT INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER B_FORUM_STAT_INSERT
BEFORE INSERT
ON B_FORUM_STAT
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_FORUM_STAT.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/