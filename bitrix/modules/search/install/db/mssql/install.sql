CREATE TABLE b_search_content
(
	ID		INT		NOT NULL IDENTITY(1,1)
	,DATE_CHANGE	DATETIME	NOT NULL
	,MODULE_ID	VARCHAR(200)	NOT NULL
	,ITEM_ID	VARCHAR(255)	NOT NULL
	,LID		CHAR(2)		NOT NULL
	,CUSTOM_RANK	INT		NOT NULL
	,URL		TEXT		NULL
	,TITLE		TEXT		NULL
	,BODY		TEXT		NULL
	,SEARCHABLE_CONTENT TEXT	NULL
	,PARAM1		VARCHAR(1000)	NULL
	,PARAM2		VARCHAR(1000)	NULL
	,UPD		VARCHAR(32)	NULL
	,DATE_FROM	DATETIME	NULL
	,DATE_TO	DATETIME	NULL
)
GO
ALTER TABLE b_search_content ADD CONSTRAINT PK_B_SEARCH_CONTENT PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT ON b_search_content (MODULE_ID, ITEM_ID)
GO
ALTER TABLE b_search_content ADD CONSTRAINT DF_B_SEARCH_CONTENT_RANK DEFAULT 0 FOR CUSTOM_RANK
GO
CREATE TABLE b_search_content_group
(
	SEARCH_CONTENT_ID	INT	NOT NULL
	,GROUP_ID		INT	NOT NULL
)
GO
CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT_GROUP ON b_search_content_group (SEARCH_CONTENT_ID, GROUP_ID)
GO
CREATE TABLE b_search_content_site
(
	SEARCH_CONTENT_ID	INT	NOT NULL
	,SITE_ID			CHAR(2)	NOT NULL
	,URL			TEXT	NULL
)
GO
ALTER TABLE b_search_content_site ADD CONSTRAINT PK_B_SRCH_CONT_SITE PRIMARY KEY (SEARCH_CONTENT_ID, SITE_ID)
GO
CREATE TABLE b_search_content_stem
(
	SEARCH_CONTENT_ID	INT		NOT NULL
	,LANGUAGE_ID		CHAR(2)		NOT NULL
	,STEM			VARCHAR(50)	NOT NULL
	,TF			FLOAT		NOT NULL
)
GO
CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT_STEM ON b_search_content_stem (STEM, LANGUAGE_ID, SEARCH_CONTENT_ID)
GO
CREATE INDEX IND_B_SEARCH_CONTENT_STEM ON b_search_content_stem (SEARCH_CONTENT_ID)
GO
CREATE TABLE b_search_content_freq
(
	LANGUAGE_ID	CHAR(2)		NOT NULL
	,STEM		VARCHAR(50)	NOT NULL
	,FREQ		FLOAT		NOT NULL,
)
GO
CREATE UNIQUE INDEX UX_B_SEARCH_CONTENT_FREQ ON b_search_content_freq (LANGUAGE_ID, STEM)
GO
CREATE TABLE b_search_custom_rank
(
	ID			INT		NOT NULL IDENTITY(1,1),
	SITE_ID			CHAR(2)		NOT NULL,
	MODULE_ID		VARCHAR(200)	NOT NULL,
	PARAM1			VARCHAR(2000)	NULL,
	PARAM2			VARCHAR(2000)	NULL,
	ITEM_ID			VARCHAR(255)	NULL,
	RANK			INT		NOT NULL,
	APPLIED			CHAR(1)		NOT NULL,
	CONSTRAINT PK_B_SEARCH_CUSTOM_RANK PRIMARY KEY (ID)
)
GO
ALTER TABLE b_search_custom_rank ADD CONSTRAINT DF_B_SEARCH_CUSTOM_RANK_RANK DEFAULT 0 FOR RANK
GO
ALTER TABLE b_search_custom_rank ADD CONSTRAINT DF_B_SEARCH_CUSTOM_RANK_APPLIED DEFAULT 'N' FOR APPLIED
GO
CREATE INDEX IND_B_SEARCH_CUSTOM_RANK ON b_search_custom_rank (SITE_ID,MODULE_ID)
GO
