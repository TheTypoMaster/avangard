<?
if(!defined("CACHED_b_sec_iprule")) define("CACHED_b_sec_iprule", 36000);
if(!defined("CACHED_b_sec_filter_mask")) define("CACHED_b_sec_filter_mask", 36000);
if(!defined("CACHED_b_sec_frame_mask")) define("CACHED_b_sec_frame_mask", 36000);
if(!defined("CACHED_b_sec_redirect_url")) define("CACHED_b_sec_redirect_url", 36000);

global $DB, $DBSQLServerType;

if($DBSQLServerType == "NATIVE")
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/classes/".strtolower($DB->type)."/database_ms.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/classes/".strtolower($DB->type)."/database.php");

CModule::AddAutoloadClasses(
	"security",
	array(
		"CSecurityIPRule" => "classes/general/iprule.php",
		"CSecurityFilter" => "classes/general/filter.php",
		"CSecurityFilterBaseAuditor" => "classes/general/auditors/base_auditor.php",
		"CSecurityFilterXssAuditor" => "classes/general/auditors/xss_auditor.php",
		"CSecurityFilterSqlAuditor" => "classes/general/auditors/sql_auditor.php",
		"CSecurityFilterPathAuditor" => "classes/general/auditors/path_auditor.php",
		"CSecurityHtmlEntity" => "classes/general/html_entity.php",
		"CSecurityFilterMask" => "classes/general/filter.php",
		"CSecurityXSSDetect" => "classes/general/post_filter.php",
		"CSecurityXSSDetectVariable" => "classes/general/post_filter.php",
		"CSecuritySessionDB" => "classes/general/session_db.php",
		"CSecuritySessionMC" => "classes/general/session_mc.php",
		"CSecuritySession" => "classes/general/session.php",
		"CSecurityUser" => "classes/general/user.php",
		"CSecurityRedirect" => "classes/general/redirect.php",
		"CSecurityAntiVirus" => "classes/general/antivirus.php",
		"CSecurityFrame" => "classes/general/frame.php",
		"CSecurityFrameMask" => "classes/general/frame.php",
		"CSecurityEvent" => "classes/general/event.php",
	)
);
?>
