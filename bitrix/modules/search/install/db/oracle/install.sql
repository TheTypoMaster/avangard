CREATE TABLE b_search_content
(
	ID			NUMBER(18)	NOT NULL,
	DATE_CHANGE		DATE		DEFAULT SYSDATE NOT NULL,
	MODULE_ID		VARCHAR2(200)	NOT NULL,
	ITEM_ID			VARCHAR2(255)	NOT NULL,
	LID			CHAR(2)		NOT NULL,
	CUSTOM_RANK		NUMBER(18)	DEFAULT 0 NOT NULL,
	URL			VARCHAR2(2000)	NULL,
	TITLE			VARCHAR2(2000)	NULL,
	BODY			CLOB		NULL,
	SEARCHABLE_CONTENT	CLOB		NULL,
	PARAM1			VARCHAR2(2000)	NULL,
	PARAM2			VARCHAR2(2000)	NULL,
	UPD			VARCHAR2(32)	NULL,
	DATE_FROM 		DATE		NULL,
	DATE_TO 		DATE		NULL,
	CONSTRAINT PK_B_SEARCH_CONTENT PRIMARY KEY (ID),
	CONSTRAINT FK_B_SEARCH_MODULE FOREIGN KEY (MODULE_ID) REFERENCES B_MODULE (ID),
	CONSTRAINT FK_B_SEARCH_CONTENT_LID FOREIGN KEY (LID) REFERENCES B_LANG (LID)
)
/

CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT ON b_search_content(MODULE_ID, ITEM_ID)
/

CREATE SEQUENCE sq_b_search_content
/

CREATE OR REPLACE TRIGGER b_search_content_insert
BEFORE INSERT
ON b_search_content
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_search_content.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/

CREATE TABLE b_search_content_group
(
	SEARCH_CONTENT_ID	NUMBER(18)	NOT NULL,
	GROUP_ID		NUMBER(18)	NOT NULL,
	CONSTRAINT FK_B_SEARCH_CONTENT_GROUP FOREIGN KEY (GROUP_ID) REFERENCES B_GROUP (ID)
)
/

CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT_GROUP ON b_search_content_group(SEARCH_CONTENT_ID, GROUP_ID)
/

CREATE TABLE b_search_content_site
(
  SEARCH_CONTENT_ID   NUMBER(18) NOT NULL,
  SITE_ID             CHAR(2) NOT NULL,
  URL                 VARCHAR2(2000),
  CONSTRAINT PK_B_SEARCH_CONTENT_SITE PRIMARY KEY (SEARCH_CONTENT_ID, SITE_ID),
  CONSTRAINT FK_B_SEARCH_CONTENT_SITE_ID FOREIGN KEY (SITE_ID) REFERENCES B_LANG (LID)
)
/

CREATE TABLE b_search_content_stem (
	SEARCH_CONTENT_ID	NUMBER		NOT NULL,
	LANGUAGE_ID		CHAR(2)		NOT NULL,
	STEM			VARCHAR2(50)	NOT NULL,
	TF			NUMBER		NOT NULL,
	CONSTRAINT PK_B_SEARCH_CONTENT_STEM
		PRIMARY KEY (STEM, LANGUAGE_ID, SEARCH_CONTENT_ID)
) 
/

CREATE INDEX IND_B_SEARCH_CONTENT_STEM ON b_search_content_stem (SEARCH_CONTENT_ID)
/

CREATE OR REPLACE TYPE T_STEM AS OBJECT (STEM VARCHAR2(50), TF NUMBER)
/

CREATE OR REPLACE TYPE TT_STEM AS TABLE OF T_STEM
/

create or replace function f_stem(p_raw in varchar2)  return tt_stem
pipelined is
	l varchar2(2000);
	p integer;
	f integer;
begin
	p:=1;
	loop
		f:=instr(p_raw, ' ', p);
		exit when f=0;
		l:=substr(p_raw, p, f-p);
		pipe row (t_stem(substr(l, 1, instr(l, ';')-1), substr(l, instr(l, ';')+1)));
		p:=f+1;
	end loop;
		l:=substr(p_raw, p);
	pipe row (t_stem(substr(l, 1, instr(l, ';')-1), substr(l, instr(l, ';')+1)));
	return;
end;
/

CREATE TABLE b_search_content_freq (
	LANGUAGE_ID char(2) NOT NULL,
	STEM varchar2(50) NOT NULL,
	FREQ number NOT NULL,
	CONSTRAINT PK_B_SEARCH_CONTENT_FREQ  PRIMARY KEY (LANGUAGE_ID,STEM)
)
/
CREATE TABLE b_search_custom_rank
(
	ID			NUMBER(18)	NOT NULL,
	SITE_ID			CHAR(2)		NOT NULL,
	MODULE_ID		VARCHAR2(200)	NOT NULL,
	PARAM1			VARCHAR2(2000)	NULL,
	PARAM2			VARCHAR2(2000)	NULL,
	ITEM_ID			VARCHAR2(255)	NULL,
	RANK			NUMBER(18)	DEFAULT 0 NOT NULL,
	APPLIED			CHAR(1)		DEFAULT 'N' NOT NULL,
	CONSTRAINT PK_B_SEARCH_CUSTOM_RANK PRIMARY KEY (ID)
)
/

CREATE INDEX IND_B_SEARCH_CUSTOM_RANK ON b_search_custom_rank (SITE_ID,MODULE_ID)
/

CREATE SEQUENCE sq_b_search_custom_rank
/

CREATE OR REPLACE TRIGGER b_search_custom_rank_insert
BEFORE INSERT
ON b_search_custom_rank
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_search_custom_rank.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;
/
