CREATE TABLE b_file
(
    ID           NUMBER(18)    NOT NULL,
    TIMESTAMP_X  DATE          DEFAULT SYSDATE NOT NULL,
    MODULE_ID    VARCHAR2(50)      NULL,
    HEIGHT       NUMBER(18)        NULL,
    WIDTH        NUMBER(18)        NULL,
    FILE_SIZE    NUMBER(18)    NOT NULL,
    CONTENT_TYPE VARCHAR2(255) DEFAULT ('IMAGE')     NULL,
    SUBDIR       VARCHAR2(255)     NULL,
    FILE_NAME    VARCHAR2(255) NOT NULL,
	 ORIGINAL_NAME varchar2(255) null,
	 DESCRIPTION varchar2(255) null,
    PRIMARY KEY (ID)
)
/

CREATE OR REPLACE PROCEDURE DELFILE
(
	FILE_ID_OLD IN NUMBER,
	FILE_ID_NEW IN NUMBER
)
AS
BEGIN
	IF FILE_ID_OLD IS NOT NULL AND NVL(FILE_ID_OLD, 0) <> NVL(FILE_ID_NEW, 0) THEN
		DELETE FROM b_file WHERE ID=FILE_ID_OLD;
	END IF;
END;
/

CREATE TABLE b_lang 
(
    LID             CHAR(2)       NOT NULL,
    SORT            NUMBER(18)    DEFAULT ('100') NOT NULL,
    DEF             CHAR(1)       DEFAULT ('N') NOT NULL,
    ACTIVE          CHAR(1)       DEFAULT ('Y') NOT NULL,
    NAME            VARCHAR2(50)  NOT NULL,
    DIR             VARCHAR2(50)  NOT NULL,
    FORMAT_DATE     VARCHAR2(50)  NOT NULL,
    FORMAT_DATETIME VARCHAR2(50)  NOT NULL,
    CHARSET         VARCHAR2(255)     NULL,
	 LANGUAGE_ID char(2) default 'en' not null,
	 DOC_ROOT varchar2(255),
	 DOMAIN_LIMITED char(1) default 'N' not null,
	 SERVER_NAME varchar2(255) null,
	 SITE_NAME varchar2(255) null,
	 EMAIL varchar2(255),
    PRIMARY KEY (LID)
)
/

create table b_site_template
(
  ID       		NUMBER(18)     	not null,
  SITE_ID      	char(2)     	not null,
  CONDITION     varchar2(255)	null,
  SORT       	NUMBER(18)     	default 500 not null,
  TEMPLATE    	varchar2(255)  	not null,
  primary key (ID)
)
/

CREATE UNIQUE INDEX UX_B_SITE_TEMPLATE ON b_site_template(SITE_ID, CONDITION, TEMPLATE)
/

CREATE SEQUENCE SQ_b_site_template START WITH 1 INCREMENT BY 1 NOMINVALUE NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_site_template_insert
BEFORE INSERT
ON b_site_template
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_site_template.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

create table b_event_message_site
(
   EVENT_MESSAGE_ID NUMBER(18) not null,
   SITE_ID char(2) not null,
   primary key(EVENT_MESSAGE_ID, SITE_ID)
)
/

create table b_language 
(
	LID				char(2) 	not null,
	SORT				NUMBER(18) 	default '100' not null,
	DEF				char(1) 	default 'N' not null,
	ACTIVE			char(1) 	default 'Y' not null,
	NAME				varchar2(50) not null,
	FORMAT_DATE		varchar2(50) not null,
	FORMAT_DATETIME 	varchar2(50) not null,
	CHARSET 			varchar2(255),
	DIRECTION 		char(1) default 'Y' not null,
	primary key (LID)
)
/

create table b_lang_domain 
(
	LID				char(2) 	not null,
	DOMAIN	 		varchar2(255)not null,
	primary key (LID, DOMAIN)
)
/

CREATE TABLE b_event_type
(
    ID          NUMBER(18)    NOT NULL,
    LID         CHAR(2)       NOT NULL,
    EVENT_NAME  VARCHAR2(50)  NOT NULL,
    NAME        VARCHAR2(100)     NULL,
    DESCRIPTION CLOB              NULL,
    SORT        NUMBER(18)    DEFAULT '150' NOT NULL,
    PRIMARY KEY (ID)
)
/
CREATE UNIQUE INDEX ux_1 ON b_event_type(EVENT_NAME, LID)
/
CREATE SEQUENCE sq_b_event_type INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER b_event_type_insert
BEFORE INSERT
ON b_event_type
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_event_type.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE b_event_message
(
    ID          NUMBER(18)    NOT NULL,
    TIMESTAMP_X DATE          DEFAULT SYSDATE NOT NULL,
    EVENT_NAME  VARCHAR2(50)  NOT NULL,
    LID         CHAR(2)       NULL,
    ACTIVE      CHAR(1)       DEFAULT 'Y' NOT NULL,
    EMAIL_FROM  VARCHAR2(255) DEFAULT '#EMAIL_FROM#' NOT NULL,
    EMAIL_TO    VARCHAR2(255) DEFAULT '#EMAIL_TO#' NOT NULL,
    SUBJECT     VARCHAR2(255)     NULL,
    MESSAGE     CLOB              NULL,
    BODY_TYPE   VARCHAR2(4)   DEFAULT 'text' NOT NULL,
    BCC         VARCHAR2(255)     NULL,
    PRIMARY KEY (ID),
    CONSTRAINT fk_b_event_mess_lid FOREIGN KEY (LID) REFERENCES b_lang(LID)
)
/
CREATE SEQUENCE sq_b_event_message INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER b_event_message_insert
BEFORE INSERT
ON b_event_message
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_event_message.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER b_event_message_update
BEFORE UPDATE 
ON b_event_message
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	END IF;
END;
/

CREATE TABLE b_event
(
    ID           NUMBER(18)   NOT NULL,
    EVENT_NAME   VARCHAR2(50) NOT NULL,
    MESSAGE_ID   NUMBER(18)       NULL,
    LID          CHAR(201)      NOT NULL,
    C_FIELDS     CLOB             NULL,
    DATE_INSERT  DATE         DEFAULT SYSDATE NOT NULL,
    DATE_EXEC    DATE             NULL,
    SUCCESS_EXEC CHAR(1)      DEFAULT 'N' NOT NULL,
	 DUPLICATE CHAR(1)      DEFAULT 'Y' NOT NULL,
    PRIMARY KEY (ID)
)
/
CREATE INDEX ix_success ON b_event(SUCCESS_EXEC)
/
CREATE SEQUENCE sq_b_event INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER b_event_insert
BEFORE INSERT
ON b_event
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_event.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE b_group
(
    ID          NUMBER(18)    NOT NULL,
    TIMESTAMP_X DATE          DEFAULT SYSDATE NOT NULL,
    ACTIVE      CHAR(1)       DEFAULT 'Y' NOT NULL,
    C_SORT      NUMBER(18)    DEFAULT 100 NOT NULL,
    ANONYMOUS   CHAR(1)       DEFAULT 'N' NOT NULL,
    NAME        VARCHAR2(50)  NOT NULL,
    DESCRIPTION VARCHAR2(255)     NULL,
    SECURITY_POLICY clob null,
    PRIMARY KEY (ID)
)
/
CREATE SEQUENCE sq_b_group START WITH 3 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER b_group_insert
BEFORE INSERT
ON b_group
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_group.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER b_group_update
BEFORE UPDATE 
ON b_group
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	END IF;
END;
/

CREATE TABLE b_user
(
    ID                  NUMBER(18)     NOT NULL,
    TIMESTAMP_X         DATE           DEFAULT SYSDATE NOT NULL,
    LOGIN               VARCHAR2(50)   NOT NULL,
    "PASSWORD"          VARCHAR2(50)   NOT NULL,
    CHECKWORD           VARCHAR2(50)       NULL,
    ACTIVE              CHAR(1)        DEFAULT 'Y' NOT NULL,
    NAME                VARCHAR2(50)       NULL,
    LAST_NAME           VARCHAR2(50)       NULL,
    EMAIL               VARCHAR2(255)  NOT NULL,
    LAST_LOGIN          DATE               NULL,
    DATE_REGISTER       DATE           DEFAULT SYSDATE NOT NULL,
    LID                 CHAR(2)            NULL,
    PERSONAL_PROFESSION VARCHAR2(255)      NULL,
    PERSONAL_WWW        VARCHAR2(255)      NULL,
    PERSONAL_ICQ        VARCHAR2(255)      NULL,
    PERSONAL_GENDER     CHAR(1)            NULL,
    PERSONAL_BIRTHDATE  VARCHAR2(50)       NULL,
    PERSONAL_PHOTO      NUMBER(18)         NULL,
    PERSONAL_PHONE      VARCHAR2(255)      NULL,
    PERSONAL_FAX        VARCHAR2(255)      NULL,
    PERSONAL_MOBILE     VARCHAR2(255)      NULL,
    PERSONAL_PAGER      VARCHAR2(255)      NULL,
    PERSONAL_STREET     VARCHAR2(2000)     NULL,
    PERSONAL_MAILBOX    VARCHAR2(255)      NULL,
    PERSONAL_CITY       VARCHAR2(255)      NULL,
    PERSONAL_STATE      VARCHAR2(255)      NULL,
    PERSONAL_ZIP        VARCHAR2(255)      NULL,
    PERSONAL_COUNTRY    VARCHAR2(255)      NULL,
    PERSONAL_NOTES      VARCHAR2(2000)     NULL,
    WORK_COMPANY        VARCHAR2(255)      NULL,
    WORK_DEPARTMENT     VARCHAR2(255)      NULL,
    WORK_POSITION       VARCHAR2(255)      NULL,
    WORK_WWW            VARCHAR2(255)      NULL,
    WORK_PHONE          VARCHAR2(255)      NULL,
    WORK_FAX            VARCHAR2(255)      NULL,
    WORK_PAGER          VARCHAR2(255)      NULL,
    WORK_STREET         VARCHAR2(2000)     NULL,
    WORK_MAILBOX        VARCHAR2(255)      NULL,
    WORK_CITY           VARCHAR2(255)      NULL,
    WORK_STATE          VARCHAR2(255)      NULL,
    WORK_ZIP            VARCHAR2(255)      NULL,
    WORK_COUNTRY        VARCHAR2(255)      NULL,
    WORK_PROFILE        VARCHAR2(2000)     NULL,
    WORK_LOGO           NUMBER(18)         NULL,
    WORK_NOTES          VARCHAR2(2000)     NULL,
    ADMIN_NOTES         VARCHAR2(2000)     NULL,
    STORED_HASH varchar2(32) null,
    XML_ID varchar2(255) null,    
    PERSONAL_BIRTHDAY DATE NULL,
    EXTERNAL_AUTH_ID varchar2(255) null,
    CHECKWORD_TIME date null,    
    SECOND_NAME           VARCHAR2(50)       NULL,
    PRIMARY KEY (ID)
)
/
CREATE UNIQUE INDEX ux_login ON b_user(LOGIN, EXTERNAL_AUTH_ID)
/

CREATE SEQUENCE sq_b_user START WITH 2 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_USER_DELETE
BEFORE DELETE 
ON B_USER
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
    DELFILE(:OLD.PERSONAL_PHOTO, NULL);
    DELFILE(:OLD.WORK_LOGO, NULL);
END;
/

CREATE OR REPLACE TRIGGER b_user_insert
BEFORE INSERT
ON b_user
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_event.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE OR REPLACE TRIGGER b_user_update
BEFORE UPDATE 
ON b_user
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL AND :NEW.LAST_LOGIN=:OLD.LAST_LOGIN THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	END IF;
	DELFILE(:OLD.PERSONAL_PHOTO, :NEW.PERSONAL_PHOTO);
	DELFILE(:OLD.WORK_LOGO, :NEW.WORK_LOGO);
END;
/

CREATE TABLE b_user_group 
(
    USER_ID  NUMBER(18) NOT NULL,
    GROUP_ID NUMBER(18) NOT NULL,
    DATE_ACTIVE_FROM DATE NULL,
    DATE_ACTIVE_TO DATE NULL,
	 CONSTRAINT fk_b_ug_user FOREIGN KEY (USER_ID) REFERENCES B_USER (ID),
    CONSTRAINT fk_b_ug_group FOREIGN KEY (GROUP_ID) REFERENCES B_GROUP (ID)
)
/
CREATE UNIQUE INDEX ux_user_group ON b_user_group (USER_ID, GROUP_ID)
/

CREATE TABLE b_module
(
    ID          VARCHAR2(50) NOT NULL,
    DATE_ACTIVE DATE         DEFAULT SYSDATE NOT NULL,
    PRIMARY KEY (ID)
)
/
CREATE TABLE b_agent
(
    ID             NUMBER(18)    NOT NULL,
    MODULE_ID      VARCHAR2(50)      NULL,
    SORT           NUMBER(18)    DEFAULT 100 NOT NULL,
    NAME           VARCHAR2(255) NOT NULL,
    ACTIVE         CHAR(1)       DEFAULT 'Y' NOT NULL,
    LAST_EXEC      DATE              NULL,
    NEXT_EXEC      DATE          NOT NULL,
    DATE_CHECK     DATE              NULL,
    AGENT_INTERVAL NUMBER(18)    DEFAULT '86400'     NULL,
    IS_PERIOD      CHAR(1)       DEFAULT 'Y'     NULL,
    USER_ID        NUMBER(18,0)  DEFAULT NULL,
    PRIMARY KEY (ID),
    CONSTRAINT fk_b_agent_module FOREIGN KEY (MODULE_ID) REFERENCES b_module(ID)
)
/
CREATE INDEX ix_act_next_exec ON b_agent(ACTIVE, NEXT_EXEC)
/
CREATE INDEX ix_agent_user_id ON b_agent(USER_ID)
/
CREATE SEQUENCE sq_b_agent INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER b_agent_insert
BEFORE INSERT
ON b_agent
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_agent.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/


CREATE TABLE b_option
(
    MODULE_ID   VARCHAR2(50)       NULL,
    NAME        VARCHAR2(50)   NOT NULL,
    VALUE       VARCHAR2(2000)     NULL,
    DESCRIPTION VARCHAR2(255)      NULL,
    SITE_ID     CHAR(2)        DEFAULT NULL

)
/
CREATE UNIQUE INDEX ix_option ON b_option(MODULE_ID, NAME, SITE_ID)
/

CREATE TABLE B_MODULE_TO_MODULE 
(
    ID             NUMBER(18)   NULL,
    TIMESTAMP_X    DATE         DEFAULT SYSDATE NOT NULL,
    SORT           NUMBER(18)   DEFAULT 100 NOT NULL,
    FROM_MODULE_ID VARCHAR2(50) NOT NULL,
    MESSAGE_ID     VARCHAR2(50) NOT NULL,
    TO_MODULE_ID   VARCHAR2(50) NOT NULL,
    TO_PATH        VARCHAR2(255)    NULL,
    TO_CLASS       VARCHAR2(50)     NULL,
    TO_METHOD      VARCHAR2(50)     NULL,
    CONSTRAINT PK_B_MODULE_TO_MODULE
    PRIMARY KEY (ID)
)
/
CREATE INDEX IX_MODULE_TO_MODULE ON B_MODULE_TO_MODULE(FROM_MODULE_ID,MESSAGE_ID,TO_MODULE_ID,TO_CLASS,TO_METHOD)
/
CREATE SEQUENCE SQ_B_MODULE_TO_MODULE INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER B_MODULE_TO_MODULE_INSERT
BEFORE INSERT 
ON B_MODULE_TO_MODULE
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT SQ_B_MODULE_TO_MODULE.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER B_MODULE_TO_MODULE_UPDATE
BEFORE UPDATE 
ON B_MODULE_TO_MODULE
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	END IF;
END;

/

CREATE SEQUENCE sq_b_file INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_file_insert
BEFORE INSERT 
ON b_file
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN     
	IF :NEW.ID IS NULL THEN      
		SELECT sq_b_file.NEXTVAL INTO :NEW.ID FROM dual;     
	END IF;     
END;
/

CREATE OR REPLACE TRIGGER b_file_update
BEFORE UPDATE 
ON b_file
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	END IF;
END;
/

CREATE TABLE b_file_action 
(
    ID           NUMBER(18)    NOT NULL,
    FILE_NAME    VARCHAR2(255) NOT NULL,
    SUBDIR       VARCHAR2(255)     NULL,
    FILE_ACTION  VARCHAR2(50)      NULL,
    DATE_INSERT  DATE          DEFAULT SYSDATE NOT NULL,
    DATE_EXEC    DATE              NULL,
    SUCCESS_EXEC CHAR(1)           NULL,
    DATE_REQUEST DATE              NULL,
    PRIMARY KEY (ID)
)
/

CREATE SEQUENCE sq_b_file_action INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_file_action_insert
BEFORE INSERT 
ON b_file_action
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN     
	IF :NEW.ID IS NULL THEN      
		SELECT sq_b_file_action.NEXTVAL INTO :NEW.ID FROM dual;     
	END IF;     
END;
/

CREATE OR REPLACE TRIGGER b_file_delete
BEFORE DELETE 
ON b_file
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	INSERT INTO b_file_action (FILE_NAME, SUBDIR, FILE_ACTION)
	VALUES(:OLD.FILE_NAME, :OLD.SUBDIR, 'DELETE');
END;
/


CREATE TABLE B_MODULE_GROUP 
(
    ID        NUMBER(18)    NOT NULL,
    MODULE_ID VARCHAR2(50)  NOT NULL,
    GROUP_ID  NUMBER(18)    NOT NULL,
    G_ACCESS  VARCHAR2(255) NOT NULL,
    PRIMARY KEY (ID)
)
/
CREATE UNIQUE INDEX UK_GROUP_MODULE ON B_MODULE_GROUP(MODULE_ID,GROUP_ID)
/
CREATE SEQUENCE SQ_B_MODULE_GROUP START WITH 1 INCREMENT BY 1 NOMINVALUE NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE TABLE B_FAVORITE 
(
    ID          NUMBER(18)     NOT NULL,
    TIMESTAMP_X DATE               NULL,
    DATE_CREATE DATE               NULL,
    C_SORT      NUMBER(18)     DEFAULT 100 NOT NULL,
    MODIFIED_BY NUMBER(18)         NULL,
    CREATED_BY  NUMBER(18)         NULL,
    MODULE_ID   VARCHAR2(50)   DEFAULT NULL  NULL,
    NAME        VARCHAR2(255)      NULL,
    URL         VARCHAR2(2000)     NULL,
    COMMENTS    VARCHAR2(2000)     NULL,
	LANGUAGE_ID CHAR(2) NULL,
	USER_ID NUMBER(18) NULL,
	COMMON CHAR(1) DEFAULT 'Y' NOT NULL,
    PRIMARY KEY (ID)
)
/
CREATE SEQUENCE SQ_B_FAVORITE START WITH 1 INCREMENT BY 1 NOMINVALUE NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

INSERT INTO b_lang(LID, SORT, DEF, ACTIVE, NAME, DIR, FORMAT_DATE, FORMAT_DATETIME, CHARSET) VALUES ('ru', 100, 'Y', 'Y', 'Russian', '/ru/', 'DD.MM.YYYY', 'DD.MM.YYYY HH:MI:SS', 'windows-1251') 
/
INSERT INTO b_lang(LID, SORT, DEF, ACTIVE, NAME, DIR, FORMAT_DATE, FORMAT_DATETIME, CHARSET) VALUES ('en', 200, 'N', 'Y', 'English', '/en/', 'DD/MM/YYYY', 'DD/MM/YYYY HH:MI:SS', 'windows-1251') 
/

INSERT INTO b_module(ID) VALUES('main') 
/

insert into b_group(ID, ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values(1, 'Y', 100, 'N', 'Administrators', 'full access') 
/
insert into b_group(ID, ACTIVE, C_SORT, ANONYMOUS, NAME, DESCRIPTION) values(2, 'Y', 200, 'Y', 'Anonymous', 'applied to everyone by default') 
/

INSERT INTO b_agent(NAME, ACTIVE, NEXT_EXEC, AGENT_INTERVAL, IS_PERIOD) VALUES ('CEvent::CleanUpAgent();', 'Y', TO_DATE(TO_CHAR(SYSDATE+1, 'DD.MM.YYYY'), 'DD.MM.YYYY'), 86400, 'Y')
/
COMMIT
/


create table b_user_stored_auth
(
    ID          NUMBER(18)    NOT NULL,
	USER_ID		NUMBER(18)		not null,
	DATE_REG	DATE			not null,
	LAST_AUTH	DATE			not null,
	STORED_HASH	varchar2(32) 	not null,
	TEMP_HASH	char(1) default ('N') not null ,
	IP_ADDR		NUMBER(18) not null,
	primary key(id)
)
/
CREATE SEQUENCE sq_b_user_stored_auth START WITH 1 INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE OR REPLACE TRIGGER sq_b_user_stored_auth_insert
BEFORE INSERT
ON b_user_stored_auth
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_user_stored_auth.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE INDEX ux_user_hash ON b_user_stored_auth(USER_ID)
/

CREATE TABLE B_USER_OPTION
(
	ID NUMBER(18) NOT NULL,
	CATEGORY VARCHAR2(50) NOT NULL,
	NAME VARCHAR2(255) NOT NULL,
	USER_ID NUMBER(18) NULL,
	VALUE CLOB,
	COMMON char(1) default ('N') not null
)
/
ALTER TABLE B_USER_OPTION ADD CONSTRAINT PK_USER_OPTION PRIMARY KEY(ID) 
/
CREATE INDEX IX_USER_OPTION_PARAM ON B_USER_OPTION (CATEGORY, NAME) 
/
CREATE SEQUENCE SQ_B_USER_OPTION INCREMENT BY 1 START WITH 1
/

CREATE TABLE B_CAPTCHA
(
	ID VARCHAR2(32) NOT NULL,
	CODE VARCHAR2(20) NOT NULL,
	IP VARCHAR2(15) NOT NULL,
	DATE_CREATE DATE NOT NULL
)
/
CREATE UNIQUE INDEX UX_B_CAPTCHA ON B_CAPTCHA(ID)
/