<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

if (!$USER->CanDoOperation('fileman_view_file_structure'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
IncludeModuleLangFile(__FILE__);

$strWarning = "";
$site = CFileMan::__CheckSite($site);
$DOC_ROOT = CSite::GetSiteDocRoot($site);

$io = CBXVirtualIo::GetInstance();

$path = $io->CombinePath("/", $path);
$arPath = Array($site, $path);
$arParsedPath = CFileMan::ParsePath(Array($site, htmlspecialcharsex($path)));
$abs_path = $DOC_ROOT.$path;

if(!$USER->CanDoFileOperation('fm_download_file', $arPath))
	$strWarning = GetMessage("ACCESS_DENIED");
else if(!$io->FileExists($abs_path))
	$strWarning = GetMessage("FILEMAN_FILENOT_FOUND")." ";
elseif(!$USER->CanDoOperation('edit_php') && (in_array(CFileman::GetFileExtension($path), CFileMan::GetScriptFileExt()) || substr(CFileman::GetFileName($path), 0, 1)=="."))
	$strWarning .= GetMessage("FILEMAN_FILE_DOWNLOAD_PHPERROR")."\n";

if(strlen($strWarning) <= 0)
{
	$flTmp = $io->GetFile($abs_path);
	$fsize = $flTmp->GetFileSize();
	header("Content-Type: application/force-download; name=\"".$io->GetPhysicalName($arParsedPath["LAST"])."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$fsize);
	header("Content-Disposition: attachment; filename=\"".$io->GetPhysicalName($arParsedPath["LAST"])."\"");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	$flTmp->ReadFile();
	die();
}

$APPLICATION->SetTitle(GetMessage("FILEMAN_FILEDOWNLOAD")." \"".$arParsedPath["LAST"]."\"");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<font class="text"><?=$arParsedPath["HTML"]?></font><br><br>
<?
ShowError($strWarning);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
