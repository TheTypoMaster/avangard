CREATE TABLE b_sec_filter_mask
(
	ID INT(11) NOT NULL auto_increment,
	SORT INT(11) NOT NULL DEFAULT 10,
	SITE_ID CHAR(2),
	FILTER_MASK VARCHAR(250),
	LIKE_MASK VARCHAR(250),
	PREG_MASK VARCHAR(250),
	PRIMARY KEY(ID)
);

CREATE TABLE b_sec_iprule
(
	ID INT(11) NOT NULL auto_increment,
	RULE_TYPE CHAR(1) NOT NULL DEFAULT 'M',
	ACTIVE CHAR(1) NOT NULL default 'Y',
	ADMIN_SECTION CHAR(1) NOT NULL DEFAULT 'Y',
	SITE_ID CHAR(2),
	SORT INT(11) NOT NULL DEFAULT 500,
	ACTIVE_FROM datetime,
	ACTIVE_FROM_TIMESTAMP INT(11),
	ACTIVE_TO datetime,
	ACTIVE_TO_TIMESTAMP INT(11),
	NAME VARCHAR(250),
	PRIMARY KEY(ID)
);
CREATE INDEX ix_b_sec_iprule_active_to on b_sec_iprule(ACTIVE_TO);

CREATE TABLE b_sec_iprule_incl_mask
(
	IPRULE_ID INT(11) NOT NULL REFERENCES b_sec_iprule(ID),
	RULE_MASK VARCHAR(250),
	SORT INT(11) NOT NULL DEFAULT 500,
	LIKE_MASK VARCHAR(250),
	PREG_MASK VARCHAR(250),
	PRIMARY KEY(IPRULE_ID, RULE_MASK)
);

CREATE TABLE b_sec_iprule_excl_mask
(
	IPRULE_ID INT(11) NOT NULL REFERENCES b_sec_iprule(ID),
	RULE_MASK VARCHAR(250),
	SORT INT(11) NOT NULL DEFAULT 500,
	LIKE_MASK VARCHAR(250),
	PREG_MASK VARCHAR(250),
	PRIMARY KEY(IPRULE_ID, RULE_MASK)
);

CREATE TABLE b_sec_iprule_incl_ip
(
	IPRULE_ID INT(11) NOT NULL REFERENCES b_sec_iprule(ID),
	RULE_IP VARCHAR(50) NOT NULL,
	SORT INT(11) NOT NULL DEFAULT 500,
	IP_START bigint(18),
	IP_END bigint(18),
	PRIMARY KEY(IPRULE_ID, RULE_IP)
);

CREATE TABLE b_sec_iprule_excl_ip
(
	IPRULE_ID INT(11) NOT NULL REFERENCES b_sec_iprule(ID),
	RULE_IP VARCHAR(50) NOT NULL,
	SORT INT(11) NOT NULL DEFAULT 500,
	IP_START bigint(18),
	IP_END bigint(18),
	PRIMARY KEY(IPRULE_ID, RULE_IP)
);

CREATE TABLE b_sec_session
(
	SESSION_ID VARCHAR(250) NOT NULL,
	TIMESTAMP_X TIMESTAMP NOT NULL,
	SESSION_DATA LONGTEXT,
	PRIMARY KEY(SESSION_ID)
);
CREATE INDEX ix_b_sec_session_time on b_sec_session(TIMESTAMP_X);

CREATE TABLE b_sec_user
(
	USER_ID INT(11) NOT NULL REFERENCES b_user(ID),
	ACTIVE CHAR(1) NOT NULL DEFAULT 'N',
	SECRET VARCHAR(64) NOT NULL,
	COUNTER INT(11) NOT NULL,
	PRIMARY KEY (USER_ID)
);

CREATE TABLE b_sec_redirect_url
(
	IS_SYSTEM CHAR(1) NOT NULL DEFAULT 'Y',
	SORT INT(11) NOT NULL DEFAULT 500,
	URL VARCHAR(250) NOT NULL,
	PARAMETER_NAME VARCHAR(250) NOT NULL
);
insert into b_sec_redirect_url values ('Y', 10, '/bitrix/redirect.php', 'goto');
insert into b_sec_redirect_url values ('Y', 20, '/bitrix/rk.php', 'goto');
insert into b_sec_redirect_url values ('Y', 30, '/bitrix/click.php', 'goto');

CREATE TABLE b_sec_white_list
(
	ID INT(11) NOT NULL,
	WHITE_SUBSTR VARCHAR(250) NOT NULL,
	PRIMARY KEY(ID)
);

CREATE TABLE b_sec_virus
(
	ID VARCHAR(32) NOT NULL,
	TIMESTAMP_X DATETIME NOT NULL,
	SITE_ID CHAR(2),
	SENT CHAR(1) NOT NULL DEFAULT 'N',
	INFO LONGTEXT NOT NULL,
	PRIMARY KEY(ID)
);

CREATE TABLE b_sec_frame_mask
(
	ID INT(11) NOT NULL auto_increment,
	SORT INT(11) NOT NULL DEFAULT 10,
	SITE_ID CHAR(2),
	FRAME_MASK VARCHAR(250),
	LIKE_MASK VARCHAR(250),
	PREG_MASK VARCHAR(250),
	PRIMARY KEY(ID)
);