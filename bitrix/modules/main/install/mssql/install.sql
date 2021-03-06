create procedure DELFILE
(
	@FILE_ID_OLD int, 
	@FILE_ID_NEW int
) 
as
if @FILE_ID_OLD is not null and isnull(@FILE_ID_OLD, 0)<>isnull(@FILE_ID_NEW, 0)
begin
	DELETE FROM B_FILE WHERE ID=@FILE_ID_OLD
end

if @@error <>0 
begin
	raiserror ('Procedure DELFILE Error',16,1)
end
GO


CREATE TABLE B_AGENT
(
	ID int NOT NULL IDENTITY (1, 1),
	MODULE_ID varchar(255) NULL,
	SORT int NOT NULL,
	NAME varchar(255) NULL,
	ACTIVE char(1) NOT NULL,
	LAST_EXEC datetime NULL,
	NEXT_EXEC datetime NOT NULL,
	DATE_CHECK datetime NULL,
	AGENT_INTERVAL int NULL,
	IS_PERIOD char(1) NOT NULL,
	USER_ID int NULL
)
GO
ALTER TABLE B_AGENT ADD CONSTRAINT PK_B_AGENT PRIMARY KEY (ID)
GO
CREATE INDEX IX_AGENT_USER_ID ON B_AGENT (USER_ID)
GO
ALTER TABLE B_AGENT ADD CONSTRAINT DF_B_AGENT_SORT DEFAULT 100 FOR SORT
GO
ALTER TABLE B_AGENT ADD CONSTRAINT DF_B_AGENT_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_AGENT ADD CONSTRAINT DF_B_AGENT_AGENT_INTERVAL DEFAULT 86400 FOR AGENT_INTERVAL
GO
ALTER TABLE B_AGENT ADD CONSTRAINT DF_B_AGENT_IS_PERIOD DEFAULT 'Y' FOR IS_PERIOD
GO
ALTER TABLE B_AGENT ADD CONSTRAINT DF_B_AGENT_USER_ID DEFAULT NULL FOR USER_ID
GO


CREATE TABLE B_EVENT
(
	ID int NOT NULL IDENTITY (1, 1),
	EVENT_NAME varchar(50) NOT NULL,
	MESSAGE_ID int NULL,
	LID varchar(201) NOT NULL,
	C_FIELDS text NULL,
	DATE_INSERT datetime NULL,
	DATE_EXEC datetime NULL,
	SUCCESS_EXEC char(1) NOT NULL,
	DUPLICATE char(1) NOT NULL
)
GO
ALTER TABLE B_EVENT ADD CONSTRAINT PK_B_EVENT PRIMARY KEY (ID)
GO
ALTER TABLE B_EVENT ADD CONSTRAINT DF_B_EVENT_SUCCESS_EXEC DEFAULT 'N' FOR SUCCESS_EXEC
GO
ALTER TABLE B_EVENT ADD CONSTRAINT DF_B_EVENT_DUPLICATE DEFAULT 'Y' FOR DUPLICATE
GO
CREATE INDEX IX_B_EVENT_SUCCESS_EXEC ON B_EVENT (SUCCESS_EXEC)
GO


CREATE TABLE B_EVENT_MESSAGE
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	LID char(2) NULL,
	EVENT_NAME varchar(50) NOT NULL,
	ACTIVE char(1) NOT NULL,
	EMAIL_FROM varchar(255) NOT NULL,
	EMAIL_TO varchar(255) NOT NULL,
	SUBJECT varchar(255) NULL,
	MESSAGE text NULL,
	BODY_TYPE varchar(50) NOT NULL,
	BCC text NULL
)
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT PK_B_EVENT_MESSAGE PRIMARY KEY (ID)
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT DF_B_EVENT_MESSAGE_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT DF_B_EVENT_MESSAGE_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT DF_B_EVENT_MESSAGE_EMAIL_FROM DEFAULT '#EMAIL_FROM#' FOR EMAIL_FROM
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT DF_B_EVENT_MESSAGE_EMAIL_TO DEFAULT '#EMAIL_TO#' FOR EMAIL_TO
GO
ALTER TABLE B_EVENT_MESSAGE ADD CONSTRAINT DF_B_EVENT_MESSAGE_BODY_TYPE DEFAULT 'text' FOR BODY_TYPE
GO

create trigger B_EVENT_MESSAGE_UPDATE on B_EVENT_MESSAGE for update as
if (not update(TIMESTAMP_X))
begin
	UPDATE B_EVENT_MESSAGE SET 
		TIMESTAMP_X = GETDATE()
	FROM 
		B_EVENT_MESSAGE U,
		INSERTED I,
		DELETED D
	WHERE 
		U.ID = I.ID 
	and U.ID = D.ID
end

if @@error <>0 
begin
	raiserror ('Trigger B_EVENT_MESSAGE_UPDATE Error',16,1)
end
GO


CREATE TABLE B_EVENT_MESSAGE_SITE
(
	EVENT_MESSAGE_ID int NOT NULL,
	SITE_ID char(2) NOT NULL
)
GO
ALTER TABLE B_EVENT_MESSAGE_SITE ADD CONSTRAINT PK_B_EVENT_MESSAGE_SITE PRIMARY KEY (EVENT_MESSAGE_ID, SITE_ID)
GO


CREATE TABLE B_EVENT_TYPE
(
	ID int NOT NULL IDENTITY (1, 1),
	LID char(2) NOT NULL,
	EVENT_NAME varchar(50) NOT NULL,
	NAME varchar(100),
	DESCRIPTION text NULL,
	SORT int NOT NULL
)
GO
ALTER TABLE B_EVENT_TYPE ADD CONSTRAINT PK_B_EVENT_TYPE PRIMARY KEY (ID)
GO
ALTER TABLE B_EVENT_TYPE ADD CONSTRAINT DF_B_EVENT_TYPE_SORT DEFAULT 150 FOR SORT
GO
CREATE UNIQUE INDEX UX_B_EVENT_TYPE_EVENT_NAME_LID ON B_EVENT_TYPE (EVENT_NAME, LID)
GO


CREATE TABLE B_FAVORITE 
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	DATE_CREATE datetime NULL,
	MODIFIED_BY int NULL,
	CREATED_BY int NULL,
	MODULE_ID varchar(50) NULL,
	NAME varchar(255) NULL,
	URL varchar(8000) NULL,
	COMMENTS varchar(8000) NULL,
	C_SORT int NOT NULL,
	LANGUAGE_ID CHAR(2) NULL,
	USER_ID INT NULL,
	COMMON CHAR(1) NOT NULL
)
GO
ALTER TABLE B_FAVORITE ADD CONSTRAINT PK_B_FAVORITE PRIMARY KEY (ID)
GO
ALTER TABLE B_FAVORITE ADD CONSTRAINT DF_B_FAVORITE_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_FAVORITE ADD CONSTRAINT DF_B_FAVORITE_C_SORT DEFAULT 100 FOR C_SORT
GO
ALTER TABLE B_FAVORITE ADD CONSTRAINT DF_B_FAVORITE_COMMON DEFAULT 'Y' FOR COMMON
GO


CREATE TABLE B_FAVORITE_LANG
(
	ID int NOT NULL IDENTITY (1, 1),
	FAVORITE_ID int NOT NULL,
	LID char(2) NOT NULL,
	MENU varchar(255) NOT NULL
)
GO
ALTER TABLE B_FAVORITE_LANG ADD CONSTRAINT PK_B_FAVORITE_LANG PRIMARY KEY (ID)
GO


CREATE TABLE B_FILE
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	MODULE_ID varchar(50) NULL,
	HEIGHT int NULL,
	WIDTH int NULL,
	FILE_SIZE int NOT NULL,
	CONTENT_TYPE varchar(255) NULL,
	SUBDIR varchar(255) NULL,
	FILE_NAME varchar(255) NOT NULL,
	ORIGINAL_NAME varchar(255) NULL,
	DESCRIPTION varchar(255) NULL
)
GO
ALTER TABLE B_FILE ADD CONSTRAINT PK_B_FILE PRIMARY KEY (ID)
GO
ALTER TABLE B_FILE ADD CONSTRAINT DF_B_FILE_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_FILE ADD CONSTRAINT DF_B_FILE_FILE_SIZE DEFAULT 0 FOR FILE_SIZE
GO
ALTER TABLE B_FILE ADD CONSTRAINT DF_B_FILE_CONTENT_TYPE DEFAULT 'IMAGE' FOR CONTENT_TYPE
GO

create trigger B_FILE_UPDATE on B_FILE for update as
if (not update(TIMESTAMP_X))
begin
	UPDATE B_FILE SET 
		TIMESTAMP_X = GETDATE()
	FROM 
		B_FILE U,
		INSERTED I,
		DELETED D
	WHERE 
		U.ID = I.ID 
	and U.ID = D.ID
end

if @@error <>0 
begin
	raiserror ('Trigger B_FILE_UPDATE Error',16,1)
end
GO

create trigger B_FILE_DELETE on B_FILE for delete as
begin
	INSERT INTO B_FILE_ACTION (
		FILE_NAME, 
		SUBDIR, 
		FILE_ACTION) 
	SELECT
		D.FILE_NAME,
		D.SUBDIR,
		'DELETE'
	FROM
		DELETED D

	if @@error <>0 
	begin
		raiserror ('Trigger B_FILE_DELETE Error',16,1)
	end
end
GO


CREATE TABLE B_FILE_ACTION 
(
	ID int NOT NULL IDENTITY (1, 1),
	FILE_NAME varchar(255) NOT NULL,
	SUBDIR varchar(255) NULL,
	FILE_ACTION varchar(50) NULL,
	DATE_INSERT datetime NOT NULL,
	DATE_EXEC datetime NULL,
	SUCCESS_EXEC char(1) NULL,
	DATE_REQUEST datetime NULL
)
GO
ALTER TABLE B_FILE_ACTION ADD CONSTRAINT PK_B_FILE_ACTION PRIMARY KEY (ID)
GO
ALTER TABLE B_FILE_ACTION ADD CONSTRAINT DF_B_FILE_ACTION_DATE_INSERT DEFAULT GETDATE() FOR DATE_INSERT
GO


CREATE TABLE B_GROUP 
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	ACTIVE char(1) NOT NULL,
	C_SORT int NOT NULL,
	ANONYMOUS char(1) NOT NULL,
	NAME varchar(50) NOT NULL,
	DESCRIPTION varchar(255) NULL,
   SECURITY_POLICY text null
)
GO
ALTER TABLE B_GROUP ADD CONSTRAINT PK_B_GROUP PRIMARY KEY (ID)
GO
ALTER TABLE B_GROUP ADD CONSTRAINT DF_B_GROUP_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_GROUP ADD CONSTRAINT DF_B_GROUP_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_GROUP ADD CONSTRAINT DF_B_GROUP_C_SORT DEFAULT 100 FOR C_SORT
GO
ALTER TABLE B_GROUP ADD CONSTRAINT DF_B_GROUP_ANONYMOUS DEFAULT 'N' FOR ANONYMOUS
GO

create trigger B_GROUP_UPDATE on B_GROUP for update as
if (not update(TIMESTAMP_X))
begin
	UPDATE B_GROUP SET 
		TIMESTAMP_X = GETDATE()
	FROM 
		B_GROUP U,
		INSERTED I,
		DELETED D
	WHERE 
		U.ID = I.ID 
	and U.ID = D.ID
end

if @@error <>0 
begin
	raiserror ('Trigger B_GROUP_UPDATE Error',16,1)
end
GO


CREATE TABLE B_LANG 
(
	LID char(2) NOT NULL,
	SORT int NOT NULL,
	DEF char(1) NOT NULL,
	ACTIVE char(1) NOT NULL,
	NAME varchar(50) NOT NULL,
	DIR varchar(50) NOT NULL,
	FORMAT_DATE varchar(50) NOT NULL,
	FORMAT_DATETIME varchar(50) NOT NULL,
	CHARSET varchar(255) NOT NULL,
	LANGUAGE_ID char(2) NOT NULL,
	DOC_ROOT varchar(255) NULL,
	DOMAIN_LIMITED char(1) NOT NULL,
	SERVER_NAME varchar(255) NULL,
	SITE_NAME varchar(255) NULL,
	EMAIL varchar(255) NULL
)
GO
ALTER TABLE B_LANG ADD CONSTRAINT PK_B_LANG PRIMARY KEY (LID)
GO
ALTER TABLE B_LANG ADD CONSTRAINT DF_B_LANG_SORT DEFAULT 100 FOR SORT
GO
ALTER TABLE B_LANG ADD CONSTRAINT DF_B_LANG_DEF DEFAULT 'N' FOR DEF
GO
ALTER TABLE B_LANG ADD CONSTRAINT DF_B_LANG_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_LANG ADD CONSTRAINT DF_B_LANG_DOMAIN_LIMITED DEFAULT 'N' FOR DOMAIN_LIMITED
GO


CREATE TABLE B_LANG_DOMAIN 
(
	LID char(2) NOT NULL,
	DOMAIN varchar(255) NOT NULL
)
GO
ALTER TABLE B_LANG_DOMAIN ADD CONSTRAINT PK_B_LANG_DOMAIN PRIMARY KEY (LID, DOMAIN)
GO


CREATE TABLE B_LANGUAGE
(
	LID char(2) NOT NULL,
	SORT int NOT NULL,
	DEF char(1) NOT NULL,
	ACTIVE char(1) NOT NULL,
	NAME varchar(50) NOT NULL,
	FORMAT_DATE varchar(50) NOT NULL,
	FORMAT_DATETIME varchar(50) NOT NULL,
	CHARSET varchar(255) NULL,
	DIRECTION char(1) NOT NULL
)
GO
ALTER TABLE B_LANGUAGE ADD CONSTRAINT PK_B_LANGUAGE PRIMARY KEY (LID)
GO
ALTER TABLE B_LANGUAGE ADD CONSTRAINT DF_B_LANGUAGE_SORT DEFAULT 100 FOR SORT
GO
ALTER TABLE B_LANGUAGE ADD CONSTRAINT DF_B_LANGUAGE_DEF DEFAULT 'N' FOR DEF
GO
ALTER TABLE B_LANGUAGE ADD CONSTRAINT DF_B_LANGUAGE_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
ALTER TABLE B_LANGUAGE ADD CONSTRAINT DF_B_LANGUAGE_DIRECTION DEFAULT 'Y' FOR DIRECTION
GO

CREATE TABLE B_MODULE
(
	ID varchar(50) NOT NULL,
	DATE_ACTIVE datetime NULL
)
GO
ALTER TABLE B_MODULE ADD CONSTRAINT PK_B_MODULE PRIMARY KEY (ID)
GO


CREATE TABLE B_MODULE_GROUP 
(
	ID int NOT NULL IDENTITY (1, 1),
	MODULE_ID varchar(50) NOT NULL,
	GROUP_ID int NOT NULL,
	G_ACCESS varchar(255) NOT NULL
)
GO
ALTER TABLE B_MODULE_GROUP ADD CONSTRAINT PK_B_MODULE_GROUP PRIMARY KEY (ID)
GO
CREATE UNIQUE INDEX UX_B_MODULE_GROUP_MODULE_ID_GROUP_ID ON B_MODULE_GROUP (MODULE_ID, GROUP_ID)
GO


CREATE TABLE B_MODULE_TO_MODULE
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	SORT int NOT NULL,
	FROM_MODULE_ID varchar(50) NOT NULL,
	MESSAGE_ID varchar(50) NOT NULL,
	TO_MODULE_ID varchar(50) NOT NULL,
	TO_PATH varchar(255) NULL,
	TO_CLASS varchar(50) NULL,
	TO_METHOD varchar(50) NULL
)
GO
ALTER TABLE B_MODULE_TO_MODULE ADD CONSTRAINT PK_B_MODULE_TO_MODULE PRIMARY KEY (ID)
GO
ALTER TABLE B_MODULE_TO_MODULE ADD CONSTRAINT DF_B_MODULE_TO_MODULE_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_MODULE_TO_MODULE ADD CONSTRAINT DF_B_MODULE_TO_MODULE_SORT DEFAULT 100 FOR SORT
GO
CREATE INDEX IX_B_MODULE_TO_MODULE ON B_MODULE_TO_MODULE (FROM_MODULE_ID, MESSAGE_ID, TO_MODULE_ID, TO_CLASS, TO_METHOD)
GO

CREATE TABLE B_OPTION 
(
	MODULE_ID varchar(50) NULL,
	NAME varchar(50) NOT NULL,
	VALUE text NULL,
	DESCRIPTION varchar(255) NULL,
	SITE_ID char(2) NULL
)
GO
CREATE UNIQUE INDEX UX_B_OPTION_MODULE_ID_NAME ON B_OPTION (MODULE_ID, NAME, SITE_ID)
GO
ALTER TABLE B_OPTION ADD CONSTRAINT DF_B_OPTION_SITE_ID DEFAULT NULL FOR SITE_ID
GO


CREATE TABLE B_SITE_TEMPLATE
(
	ID int NOT NULL IDENTITY (1, 1),
	SITE_ID char(2) NOT NULL,
	CONDITION varchar(255) NULL,
	SORT int NOT NULL,
	TEMPLATE varchar(255) NOT NULL
)
GO
ALTER TABLE B_SITE_TEMPLATE ADD CONSTRAINT PK_B_SITE_TEMPLATE PRIMARY KEY (ID)
GO
ALTER TABLE B_SITE_TEMPLATE ADD CONSTRAINT DF_B_SITE_TEMPLATE_SORT DEFAULT 500 FOR SORT
GO
CREATE UNIQUE INDEX UX_B_SITE_TEMPLATE ON b_site_template (SITE_ID, CONDITION, TEMPLATE)
GO

CREATE TABLE B_USER
(
	ID int NOT NULL IDENTITY (1, 1),
	TIMESTAMP_X datetime NULL,
	LOGIN varchar(50) NOT NULL,
	PASSWORD varchar(50) NOT NULL,
	CHECKWORD varchar(50),
	ACTIVE char(1) NOT NULL,
	NAME varchar(50) NULL,
	LAST_NAME varchar(50) NULL,
	EMAIL varchar(255) NULL,
	LAST_LOGIN datetime NULL,
	DATE_REGISTER datetime NOT NULL,
	PERSONAL_PROFESSION varchar(255) NULL,
	PERSONAL_WWW varchar(255) NULL,
	PERSONAL_ICQ varchar(255) NULL,
	PERSONAL_GENDER char(1) NULL,
	PERSONAL_BIRTHDATE varchar(50) NULL,
	PERSONAL_PHOTO int NULL,
	PERSONAL_PHONE varchar(255) NULL,
	PERSONAL_FAX varchar(255) NULL,
	PERSONAL_MOBILE varchar(255) NULL,
	PERSONAL_PAGER varchar(255) NULL,
	PERSONAL_STREET text NULL,
	PERSONAL_MAILBOX varchar(255) NULL,
	PERSONAL_CITY varchar(255) NULL,
	PERSONAL_STATE varchar(255) NULL,
	PERSONAL_ZIP varchar(255) NULL,
	PERSONAL_COUNTRY varchar(255) NULL,
	PERSONAL_NOTES text NULL,
	WORK_COMPANY varchar(255) NULL,
	WORK_DEPARTMENT varchar(255) NULL,
	WORK_POSITION varchar(255) NULL,
	WORK_WWW varchar(255) NULL,
	WORK_PHONE varchar(255) NULL,
	WORK_FAX varchar(255) NULL,
	WORK_PAGER varchar(255) NULL,
	WORK_STREET text NULL,
	WORK_MAILBOX varchar(255) NULL,
	WORK_CITY varchar(255) NULL,
	WORK_STATE varchar(255) NULL,
	WORK_ZIP varchar(255) NULL,
	WORK_COUNTRY varchar(255) NULL,
	WORK_PROFILE text NULL,
	WORK_LOGO int NULL,
	WORK_NOTES text NULL,
	ADMIN_NOTES text NULL,
	LID char(2) NULL,
	STORED_HASH varchar(32) NULL,
	XML_ID varchar(255) NULL,
	PERSONAL_BIRTHDAY datetime NULL,
	EXTERNAL_AUTH_ID varchar(255) NULL,
	CHECKWORD_TIME datetime NULL,
	SECOND_NAME varchar(50) NULL,
)
GO
ALTER TABLE B_USER ADD CONSTRAINT PK_B_USER PRIMARY KEY (ID)
GO
ALTER TABLE B_USER ADD CONSTRAINT DF_B_USER_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X
GO
ALTER TABLE B_USER ADD CONSTRAINT DF_B_USER_ACTIVE DEFAULT 'Y' FOR ACTIVE
GO
CREATE UNIQUE INDEX UX_B_USER_LOGIN_EXTERNAL_AUTH_ID ON B_USER (LOGIN, EXTERNAL_AUTH_ID)
GO

create trigger B_USER_UPDATE on B_USER for update as
if (not update(TIMESTAMP_X))
begin
	UPDATE B_USER SET 
		TIMESTAMP_X = GETDATE()
	FROM 
		B_USER U,
		INSERTED I,
		DELETED D
	WHERE 
		U.ID = I.ID 
	and U.ID = D.ID
end

if (update(PERSONAL_PHOTO) or update(WORK_LOGO))
begin

	declare 
		@PERSONAL_PHOTO_NEW int, 
		@WORK_LOGO_NEW int,
		@PERSONAL_PHOTO_OLD int, 
		@WORK_LOGO_OLD int

	declare cCursor cursor for
		SELECT
			I.PERSONAL_PHOTO	PERSONAL_PHOTO_NEW,
			I.WORK_LOGO			WORK_LOGO_NEW,
			D.PERSONAL_PHOTO	PERSONAL_PHOTO_OLD,
			D.WORK_LOGO			WORK_LOGO_OLD
		FROM
			INSERTED I,
			DELETED D
		WHERE 
			I.ID = D.ID
			
	open cCursor

	while 1=1
	begin
		fetch next from cCursor into 
			@PERSONAL_PHOTO_NEW, 
			@WORK_LOGO_NEW, 
			@PERSONAL_PHOTO_OLD, 
			@WORK_LOGO_OLD

		if @@fetch_status<>0
			break

		exec DELFILE @PERSONAL_PHOTO_OLD, @PERSONAL_PHOTO_NEW
		exec DELFILE @WORK_LOGO_OLD, @WORK_LOGO_NEW

	end
	close cCursor
	deallocate cCursor
end

if @@error <>0 
begin
	raiserror ('Trigger B_USER_UPDATE Error',16,1)
end
GO

create trigger B_USER_DELETE on B_USER for delete as

declare 
	@PERSONAL_PHOTO int, 
	@WORK_LOGO int

declare cCursor cursor for
	SELECT
		D.PERSONAL_PHOTO,
		D.WORK_LOGO
	FROM
		DELETED D

open cCursor

while 1=1
begin
	fetch next from cCursor into 
		@PERSONAL_PHOTO, 
		@WORK_LOGO

	if @@fetch_status<>0
		break

	exec DELFILE @PERSONAL_PHOTO, null
	exec DELFILE @WORK_LOGO, null

end
close cCursor
deallocate cCursor

if @@error <>0 
begin
	raiserror ('Trigger B_USER_DELETE Error',16,1)
end
GO

CREATE TABLE B_USER_GROUP
(
	USER_ID int NOT NULL,
	GROUP_ID int NOT NULL,
	DATE_ACTIVE_FROM datetime NULL,
	DATE_ACTIVE_TO datetime NULL
)
GO
CREATE UNIQUE INDEX UX_B_USER_GROUP_USER_ID_GROUP_ID ON B_USER_GROUP (USER_ID, GROUP_ID)
GO

create table B_USER_STORED_AUTH
(
    ID          int					NOT NULL IDENTITY (1, 1),
	USER_ID		int					not null,
	DATE_REG	DATETIME			not null,
	LAST_AUTH	DATETIME			not null,
	STORED_HASH	varchar(32) 		not null,
	TEMP_HASH	char(1) 			not null,
	IP_ADDR		decimal(18,0)		not null
)
GO

ALTER TABLE B_USER_STORED_AUTH ADD CONSTRAINT PK_B_USER_STORED_AUTH PRIMARY KEY (ID)
GO

CREATE INDEX ux_user_hash ON B_USER_STORED_AUTH(USER_ID, STORED_HASH)
GO

CREATE TABLE B_USER_OPTION
(
	ID int NOT NULL IDENTITY (1, 1),
	USER_ID int NULL,
	CATEGORY varchar(50) NOT NULL,
	NAME varchar(255) NOT NULL,
	VALUE text NULL,
	COMMON char(1) NOT NULL DEFAULT 'N'
)
GO
CREATE INDEX IX_B_USER_OPTION ON B_USER_OPTION
(
	CATEGORY,
	NAME
)
GO

CREATE TABLE B_CAPTCHA
(
	ID VARCHAR(32) NOT NULL,
	CODE VARCHAR(20) NOT NULL,
	IP VARCHAR(15) NOT NULL,
	DATE_CREATE DATETIME NOT NULL,
)
GO

CREATE UNIQUE INDEX UX_B_CAPTCHA ON B_CAPTCHA(ID)
GO

