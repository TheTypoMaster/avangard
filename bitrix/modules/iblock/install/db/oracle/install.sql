CREATE TABLE b_iblock_type
(
	ID 					VARCHAR2(50)				not null,
	SECTIONS			CHAR(1)			DEFAULT('Y')not null,
	EDIT_FILE_BEFORE 	varchar2(255),
	EDIT_FILE_AFTER 	varchar2(255),
	IN_RSS 				char(1) 		default 'Y' not null,
	SORT 				NUMBER(18) 		DEFAULT 500 NOT NULL,
	primary key (ID)
)
/

CREATE TABLE b_iblock_type_lang
(
	IBLOCK_TYPE_ID	VARCHAR2(50)			not null,
	LID				CHAR(2)					not null,
	NAME			VARCHAR2(100)			not null,
	SECTION_NAME	VARCHAR2(100)			null,
	ELEMENT_NAME	VARCHAR2(100)			null
)
/

CREATE TABLE b_iblock
(
	ID 				number(18) 						not null,
	TIMESTAMP_X 	date			DEFAULT SYSDATE not null,
	IBLOCK_TYPE_ID 	varchar2(50) 					not null,
	LID				char(2)							not null,
	CODE 			varchar2(50),
	NAME 			varchar2(255)					not null,
	ACTIVE 			char(1) 		DEFAULT 'Y' 	not null,
	SORT		 	number(18) 		DEFAULT '500'	not null,
	LIST_PAGE_URL 	VARCHAR2(255)     NULL,
	SECTION_PAGE_URL varchar2(255) 	  null,  
	DETAIL_PAGE_URL VARCHAR2(255)     NULL,
	PICTURE				NUMBER(18),
	DESCRIPTION			CLOB,
	DESCRIPTION_TYPE	char(4) 	DEFAULT 'text' not null,
	RSS_TTL NUMBER(18) DEFAULT 24 NOT NULL,
	RSS_ACTIVE CHAR(1) DEFAULT 'Y' NOT NULL,
	RSS_FILE_ACTIVE CHAR(1) DEFAULT 'N' NOT NULL,
	RSS_FILE_LIMIT NUMBER(18) NULL,
	RSS_FILE_DAYS NUMBER(18) NULL,
	RSS_YANDEX_ACTIVE CHAR(1) DEFAULT 'N' NOT NULL,
	XML_ID varchar2(255),
	TMP_ID varchar2(40),
	INDEX_ELEMENT char(1) default 'Y' not null ,
	INDEX_SECTION char(1) default 'N' not null,
	VERSION number(2) default 1 not null,
	LAST_CONV_ELEMENT number(18) default 0 not null,
	EDIT_FILE_BEFORE 	varchar2(255),
	EDIT_FILE_AFTER 	varchar2(255),
	PRIMARY KEY(ID),
	CONSTRAINT fk_b_iblock FOREIGN KEY (IBLOCK_TYPE_ID) REFERENCES b_iblock_type(ID),
	CONSTRAINT fk_b_iblock1 FOREIGN KEY (LID) REFERENCES b_lang(LID)
)
/

CREATE INDEX ix_iblock ON b_iblock(IBLOCK_TYPE_ID, LID, ACTIVE)
/

CREATE SEQUENCE sq_b_iblock INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_iblock_insert
BEFORE INSERT
ON b_iblock
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER b_iblock_update
BEFORE UPDATE 
ON b_iblock
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	ELSE
		:NEW.TIMESTAMP_X := :OLD.TIMESTAMP_X;
	END IF;
END;
/

CREATE TABLE b_iblock_site
(  
	IBLOCK_ID	NUMBER(18) NOT NULL,
	SITE_ID		CHAR(2) NOT NULL,
	CONSTRAINT	PK_B_IBLOCK_SITE PRIMARY KEY (IBLOCK_ID, SITE_ID)
)
/


CREATE TABLE b_iblock_property
(
	ID 				number(18)					not null,
	TIMESTAMP_X 	date		DEFAULT SYSDATE not null,
	IBLOCK_ID 		number(18) 					not null,
	NAME 			varchar2(100) 				not null,
	ACTIVE			char(1) 	default 'Y' 	not null,
	CODE			varchar2(50),
	SORT 			number(18) 	default '500' 	not null,
	DEFAULT_VALUE	varchar2(255),
	PROPERTY_TYPE	char(1) 	default 'S' 	not null,
	ROW_COUNT		number(18) 	default '1' 	not null,
	COL_COUNT		number(18) 	default '30' 	not null,
	LIST_TYPE 		char(1) 	default 'L' 	not null,
	MULTIPLE 		char(1) 	default 'N' 	not null,
	XML_ID 			varchar2(100),
	FILE_TYPE 		varchar2(200),
	MULTIPLE_CNT 	number(18),
	TMP_ID 			varchar2(40),
	LINK_IBLOCK_ID 	NUMBER(18),
	WITH_DESCRIPTION CHAR(1),
	SEARCHABLE 		char(1) 	default 'N' not null,
	FILTRABLE 		char(1) 	default 'N' not null,
	VERSION			number(2) default 1 not null,
	USER_TYPE		varchar2(255),
	PRIMARY KEY (ID),
	CONSTRAINT fk_b_iblock_property	FOREIGN KEY (IBLOCK_ID) REFERENCES b_iblock(ID)
)
/

CREATE INDEX ix_iblock_property_1 ON b_iblock_property(IBLOCK_ID)
/

CREATE SEQUENCE sq_b_iblock_property INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/


CREATE OR REPLACE TRIGGER b_iblock_property_insert
BEFORE INSERT
ON b_iblock_property
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock_property.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER b_iblock_property_update
BEFORE UPDATE 
ON b_iblock_property
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	ELSE
		:NEW.TIMESTAMP_X := :OLD.TIMESTAMP_X;
	END IF;
END;
/


create table b_iblock_property_enum
(
  ID number(18) not null,
  PROPERTY_ID number(18) not null,  
  VALUE varchar2(255) not null,  
  DEF char(1) default 'N' not null,
  SORT number(18) default '500' not null,
  XML_ID varchar2(200)  not null,
  TMP_ID varchar2(40),
  primary key (ID),  
  CONSTRAINT fk_b_iblock_propenum FOREIGN KEY (PROPERTY_ID) REFERENCES b_iblock_property(ID)
)
/

CREATE SEQUENCE sq_b_iblock_property_enum INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_iblock_property_enum_insert
BEFORE INSERT
ON b_iblock_property_enum
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock_property_enum.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

create unique index ux_iblock_property_enum on b_iblock_property_enum(PROPERTY_ID, XML_ID)
/


CREATE TABLE b_iblock_section
(
	ID 					number(18) 							not null,
	TIMESTAMP_X 		date			DEFAULT SYSDATE 	not null,
	IBLOCK_ID 			number(18) 							not null,
	IBLOCK_SECTION_ID 	number(18),
	ACTIVE 				char(1) 		DEFAULT 'Y' 		not null,
	GLOBAL_ACTIVE 		char(1) 		DEFAULT 'Y' 		not null,
	SORT 				number(18) 		DEFAULT '500' 		not null,
	NAME 				varchar2(255)						not null,
	PICTURE				number(18),
	LEFT_MARGIN			number(18),
	RIGHT_MARGIN		number(18),
	DEPTH_LEVEL			number(18),
	DESCRIPTION			CLOB,
	DESCRIPTION_TYPE	char(4) 		DEFAULT 'text' 		not null,
	SEARCHABLE_CONTENT  CLOB,
	XML_ID 				varchar2(255),
	TMP_ID 				varchar2(40),
    CODE 				varchar2(255),
    DETAIL_PICTURE 		number(18) NULL,
	PRIMARY KEY (ID),
	CONSTRAINT fk_b_iblock_section FOREIGN KEY (IBLOCK_ID) REFERENCES b_iblock(ID),
	CONSTRAINT fk_b_iblock_section1 FOREIGN KEY (IBLOCK_SECTION_ID) REFERENCES b_iblock_section(ID)
)
/
CREATE INDEX ix_iblock_section_1 ON b_iblock_section(IBLOCK_ID, IBLOCK_SECTION_ID)
/
CREATE INDEX ux_iblock_section_1 ON b_iblock_section(IBLOCK_ID, LEFT_MARGIN, RIGHT_MARGIN)
/

CREATE SEQUENCE sq_b_iblock_section INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_iblock_section_insert
BEFORE INSERT
ON b_iblock_section
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock_section.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE OR REPLACE TRIGGER b_iblock_section_update
BEFORE UPDATE 
ON b_iblock_section
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	IF :NEW.LEFT_MARGIN=:OLD.LEFT_MARGIN AND :NEW.RIGHT_MARGIN=:OLD.RIGHT_MARGIN AND :NEW.GLOBAL_ACTIVE=:OLD.GLOBAL_ACTIVE THEN
		DELFILE(:OLD.PICTURE, :NEW.PICTURE);
		DELFILE(:OLD.DETAIL_PICTURE, :NEW.DETAIL_PICTURE);
	END IF;
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	ELSE
		:NEW.TIMESTAMP_X := :OLD.TIMESTAMP_X;
	END IF;
END;
/

CREATE OR REPLACE TRIGGER b_iblock_section_delete
BEFORE DELETE 
ON b_iblock_section
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	DELFILE(:OLD.PICTURE, NULL);
	DELFILE(:OLD.DETAIL_PICTURE, NULL);
END;
/



CREATE TABLE b_iblock_element
(
    ID                  NUMBER(18)     NOT NULL,
    TIMESTAMP_X         DATE           DEFAULT SYSDATE NULL,
    MODIFIED_BY         NUMBER(18)         NULL,
    DATE_CREATE         DATE               NULL,
    CREATED_BY          NUMBER(18)         NULL,
    IBLOCK_ID           NUMBER(18)     NOT NULL,
    IBLOCK_SECTION_ID   NUMBER(18)         NULL,
    ACTIVE              CHAR(1)        DEFAULT 'Y' NOT NULL,
    ACTIVE_FROM         DATE               NULL,
    ACTIVE_TO           DATE               NULL,
    SORT                NUMBER(18)     DEFAULT '500' NOT NULL,
    NAME                VARCHAR2(255)  NOT NULL,
    PREVIEW_PICTURE     NUMBER(18)         NULL,
    PREVIEW_TEXT        VARCHAR2(2000)     NULL,
    PREVIEW_TEXT_TYPE   CHAR(4)        DEFAULT 'text' NOT NULL,
    DETAIL_PICTURE      NUMBER(18)         NULL,
    DETAIL_TEXT         CLOB               NULL,
    DETAIL_TEXT_TYPE    CHAR(4)        DEFAULT 'text' NOT NULL,
    SEARCHABLE_CONTENT  CLOB               NULL,
    WF_STATUS_ID        NUMBER(18)     DEFAULT '1'     NULL,
    WF_PARENT_ELEMENT_ID NUMBER(18)         NULL,
    WF_NEW              CHAR(1)            NULL,
    WF_LOCKED_BY        NUMBER(18)         NULL,
    WF_DATE_LOCK        DATE               NULL,
    WF_COMMENTS         VARCHAR2(2000)     NULL,
    IN_SECTIONS  		char(1) 	default 'N' not null,
    XML_ID 				varchar2(255),
    TMP_ID 				varchar2(40),
    CODE 				varchar2(255),
    WF_LAST_HISTORY_ID	NUMBER(18),
    SHOW_COUNTER		NUMBER(18),
    SHOW_COUNTER_START 	DATE,
    PRIMARY KEY (ID),
    CONSTRAINT fk_b_iblock_element FOREIGN KEY (IBLOCK_ID) REFERENCES b_iblock(ID),
    CONSTRAINT fk_b_iblock_element1 FOREIGN KEY (IBLOCK_SECTION_ID) REFERENCES b_iblock_section(ID)
)
/
CREATE INDEX ix_iblock_element_1 ON b_iblock_element(IBLOCK_ID, IBLOCK_SECTION_ID)
/
CREATE INDEX IX_IBLOCK_ELEMENT_41 ON B_IBLOCK_ELEMENT(IBLOCK_ID, XML_ID, WF_PARENT_ELEMENT_ID)
/
CREATE INDEX ix_iblock_element_3 ON B_IBLOCK_ELEMENT(WF_PARENT_ELEMENT_ID)
/
CREATE INDEX ix_iblock_element_sec ON b_iblock_element(IBLOCK_SECTION_ID)
/
CREATE INDEX IX_IBLOCK_ELEMENT_4 ON B_IBLOCK_ELEMENT(ACTIVE_FROM, IBLOCK_ID)
/
CREATE INDEX IX_IBLOCK_ELEMENT_PUB ON B_IBLOCK_ELEMENT(IBLOCK_ID, WF_STATUS_ID, WF_PARENT_ELEMENT_ID, ACTIVE_FROM)
/

CREATE SEQUENCE sq_b_iblock_element INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE OR REPLACE TRIGGER b_iblock_element_insert
BEFORE INSERT
ON b_iblock_element
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock_element.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE OR REPLACE TRIGGER b_iblock_element_delete
BEFORE DELETE 
ON b_iblock_element
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	DELFILE(:OLD.PREVIEW_PICTURE, NULL);
	DELFILE(:OLD.DETAIL_PICTURE, NULL);
END;
/

CREATE OR REPLACE TRIGGER b_iblock_element_update
BEFORE UPDATE 
ON b_iblock_element
REFERENCING OLD AS OLD NEW AS NEW
FOR EACH ROW 
BEGIN
	DELFILE(:OLD.PREVIEW_PICTURE, :NEW.PREVIEW_PICTURE);
	DELFILE(:OLD.DETAIL_PICTURE, :NEW.DETAIL_PICTURE);
	IF :NEW.TIMESTAMP_X IS NOT NULL THEN
		:NEW.TIMESTAMP_X := SYSDATE;
	ELSE
		:NEW.TIMESTAMP_X := :OLD.TIMESTAMP_X;
	END IF;
END;
/



create table B_IBLOCK_SECTION_ELEMENT
(
	IBLOCK_SECTION_ID number(18) not null,
	IBLOCK_ELEMENT_ID number(18) not null,
	ADDITIONAL_PROPERTY_ID NUMBER(18) NULL
)
/

CREATE UNIQUE INDEX ux_iblock_section_element ON B_IBLOCK_SECTION_ELEMENT(IBLOCK_SECTION_ID, IBLOCK_ELEMENT_ID, ADDITIONAL_PROPERTY_ID)
/
CREATE INDEX UX_IBLOCK_SECTION_ELEMENT2 ON B_IBLOCK_SECTION_ELEMENT(IBLOCK_ELEMENT_ID)
/

ALTER TABLE B_IBLOCK_SECTION_ELEMENT ADD CONSTRAINT fk_b_iblock_sect_el_el FOREIGN KEY (IBLOCK_ELEMENT_ID) REFERENCES b_iblock_element(ID) ON DELETE CASCADE
/
ALTER TABLE B_IBLOCK_SECTION_ELEMENT ADD CONSTRAINT fk_b_iblock_sect_el_sec FOREIGN KEY (IBLOCK_SECTION_ID) REFERENCES b_iblock_section(ID) ON DELETE CASCADE
/

CREATE TABLE b_iblock_element_property
(
	ID 					number(18) 							not null,
	IBLOCK_PROPERTY_ID	number(18) 							not null,
	IBLOCK_ELEMENT_ID 	number(18) 							not null,
	VALUE				VARCHAR2(2000)						null,
	VALUE_TYPE			char(4) 		DEFAULT 'text' 		not null,
	VALUE_ENUM 			NUMBER(18),
	VALUE_NUM 			NUMBER(18,4),
	DESCRIPTION 		VARCHAR2(255) 						NULL,
	PRIMARY KEY (ID),
	CONSTRAINT fk_b_iblock_element_property FOREIGN KEY (IBLOCK_PROPERTY_ID) REFERENCES b_iblock_property(ID),
	CONSTRAINT fk_b_iblock_element_property1 FOREIGN KEY (IBLOCK_ELEMENT_ID) REFERENCES b_iblock_element(ID)
)
/

CREATE SEQUENCE sq_b_iblock_element_property INCREMENT BY 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER
/
CREATE INDEX ix_iblock_element_property_1 ON b_iblock_element_property(IBLOCK_ELEMENT_ID, IBLOCK_PROPERTY_ID)
/
CREATE INDEX IX_IBLOCK_ELEMENT_PROP_ENUM ON B_IBLOCK_ELEMENT_PROPERTY(VALUE_ENUM,IBLOCK_PROPERTY_ID)
/
CREATE INDEX ix_iblock_element_property_2 ON B_IBLOCK_ELEMENT_PROPERTY(IBLOCK_PROPERTY_ID)
/

CREATE OR REPLACE TRIGGER b_iblock_element_prop_insert
BEFORE INSERT
ON b_iblock_element_property
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_iblock_element_property.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE b_iblock_group
(
	IBLOCK_ID	number(18) 							not null,
	GROUP_ID 	number(18) 							not null,
	PERMISSION	char(1) 							not null,
	CONSTRAINT fk_b_iblock_group FOREIGN KEY (IBLOCK_ID) REFERENCES b_iblock(ID),
	CONSTRAINT fk_b_iblock_group1 FOREIGN KEY (GROUP_ID) REFERENCES b_group(ID)
)
/

CREATE UNIQUE INDEX ux_iblock_group_1 ON b_iblock_group(IBLOCK_ID, GROUP_ID)
/

CREATE TABLE B_IBLOCK_RSS
(
	ID         NUMBER(18)        NOT NULL,
	IBLOCK_ID  NUMBER(18)        NOT NULL,
	NODE       VARCHAR2(50)  NOT NULL,
	NODE_VALUE VARCHAR2(250) NOT NULL
)
/

ALTER TABLE B_IBLOCK_RSS ADD PRIMARY KEY (ID)
/

ALTER TABLE B_IBLOCK_RSS ADD CONSTRAINT FK_IBLOCK_IBLOCK_RSS
	FOREIGN KEY (IBLOCK_ID)
	REFERENCES B_IBLOCK (ID)
/

CREATE SEQUENCE SQ_IBLOCK_RSS START WITH 1 INCREMENT BY 1 NOMINVALUE
	NOMAXVALUE NOCYCLE NOCACHE NOORDER
/

CREATE TABLE B_IBLOCK_CACHE 
(
    CACHE_KEY  VARCHAR2(35) NOT NULL,
    CACHE      CLOB         NOT NULL,
    CACHE_DATE DATE         NOT NULL
)
/

ALTER TABLE B_IBLOCK_CACHE 
    ADD CONSTRAINT SYS_C0027470
PRIMARY KEY (CACHE_KEY)
/
