<?
define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$MESS = array();
$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/lang/".LANGUAGE_ID."/show_file.php");
include_once($path);
$MESS1 =& $MESS;
$GLOBALS["MESS"] = $MESS1 + $GLOBALS["MESS"];

if(!CModule::IncludeModule("blog"))
	return

$arParams = Array();
$arParams["WIDTH"] = (isset($_REQUEST['width']) && intval($_REQUEST['width'])>0) ? intval($_REQUEST['width']) : 0;
$arParams["HEIGHT"] = (isset($_REQUEST['height']) && intval($_REQUEST['height'])>0) ? intval($_REQUEST['height']) : 0;
$arParams["FILE_ID"] = IntVal($_REQUEST["fid"]);
$arParams["PERMISSION"] = false;

$arResult["MESSAGE"] = array();
$arResult["FILE"] = array();

$arError = array();
if (intVal($arParams["FILE_ID"]) > 0)
{
	if ($res = CBlogImage::GetByID($arParams["FILE_ID"]))
	{
		$arResult["FILE_INFO"] = $res;
		$arResult["FILE"] = CFile::GetFileArray($arResult["FILE_INFO"]["FILE_ID"]);
	}
}

if (empty($arResult["FILE"]))
{
	$arError = array(
		"code" => "EMPTY FILE",
		"title" => GetMessage("F_EMPTY_FID"));
}
elseif (intVal($arResult["FILE_INFO"]["POST_ID"]) > 0)
{
	if (isset($GLOBALS["BLOG_IMAGE"]["BLOG_IMAGE_POST_CACHE_".$arResult["FILE_INFO"]["POST_ID"]]))
	{
		$arParams["PERMISSION"] = $GLOBALS["BLOG_IMAGE"]["BLOG_IMAGE_POST_CACHE_".$arResult["FILE_INFO"]["POST_ID"]];
	}
	else
	{
		$dbPost = CBlogPost::GetList(array(), array("ID" => $arResult["FILE_INFO"]["POST_ID"], "BLOG_ID" => $arResult["FILE_INFO"]["BLOG_ID"]), false, false, array("ID", "BLOG_ID", "BLOG_OWNER_ID", "BLOG_SOCNET_GROUP_ID", "BLOG_USE_SOCNET", "AUTHOR_ID", "MICRO"));
		if($arResult["POST"] = $dbPost->Fetch())
		{
			$feature = "blog";
			if($arResult["POST"]["MICRO"] == "Y")
				$feature = "microblog";
			$user_id = IntVal($USER->GetID());
			if($arResult["POST"]["BLOG_USE_SOCNET"] == "Y")
			{
				if(!CModule::IncludeModule("socialnetwork"))
					return;
				$arParams["PERMISSION"] = BLOG_PERMS_DENY;
				if(IntVal($arResult["POST"]["BLOG_SOCNET_GROUP_ID"]) > 0)
				{
					if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arResult["POST"]["BLOG_SOCNET_GROUP_ID"], $feature, "full_post", CSocNetUser::IsCurrentUserModuleAdmin()))
						$arParams["PERMISSION"] = BLOG_PERMS_FULL;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arResult["POST"]["BLOG_SOCNET_GROUP_ID"], $feature, "moderate_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_MODERATE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arResult["POST"]["BLOG_SOCNET_GROUP_ID"], $feature, "write_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_WRITE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arResult["POST"]["BLOG_SOCNET_GROUP_ID"], $feature, "premoderate_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_PREMODERATE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arResult["POST"]["BLOG_SOCNET_GROUP_ID"], $feature, "view_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_READ;
				}
				else
				{
					if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arResult["POST"]["BLOG_OWNER_ID"], $feature, "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W" || $arResult["POST"]["BLOG_OWNER_ID"] == $user_id)
						$arParams["PERMISSION"] = BLOG_PERMS_FULL;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arResult["POST"]["BLOG_OWNER_ID"], $feature, "moderate_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_MODERATE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arResult["POST"]["BLOG_OWNER_ID"], $feature, "write_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_WRITE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arResult["POST"]["BLOG_OWNER_ID"], $feature, "premoderate_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_PREMODERATE;
					elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arResult["POST"]["BLOG_OWNER_ID"], $feature, "view_post"))
						$arParams["PERMISSION"] = BLOG_PERMS_READ;
				}
			}
			else
			{
				$arParams["PERMISSION"] = CBlogPost::GetBlogUserPostPerms($arResult["POST"]["ID"], $user_id);
			}
		}
		$GLOBALS["BLOG_IMAGE"]["BLOG_IMAGE_POST_CACHE_".$arResult["FILE_INFO"]["POST_ID"]] = $arParams["PERMISSION"];
	}
	if (empty($arResult["POST"]))
	{
		$arError = array(
			"code" => "EMPTY POST",
			"title" => GetMessage("F_EMPTY_MID"));
	}
	elseif ($arParams["PERMISSION"])
	{
		if ($arParams["PERMISSION"] < BLOG_PERMS_READ)
			$arError = array(
				"code" => "NOT RIGHT",
				"title" => GetMessage("F_NOT_RIGHT"));
	}
}


if (!empty($arError))
{
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_after.php");
	echo ShowError((!empty($arError["title"]) ? $arError["title"] : $arError["code"]));
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog.php");
	die();
}
// *************************/Default params*************************************************************

set_time_limit(0);

if (
	strpos($arResult["FILE"]["CONTENT_TYPE"], "image/")!==false
	&& strpos($arResult["FILE"]["CONTENT_TYPE"], "html")===false
	&& (
		(
			file_exists($_SERVER["DOCUMENT_ROOT"].$arResult["FILE"]["SRC"])
			&& GetImageSize($_SERVER["DOCUMENT_ROOT"].$arResult["FILE"]["SRC"])
		) || (
			$arResult["FILE"]["WIDTH"] > 0
			&& $arResult["FILE"]["HEIGHT"] > 0
		)
	)
)
{
	if ($arResult["FILE"]["WIDTH"] > $arParams['WIDTH'] || $arResult["FILE"]["HEIGHT"] > $arParams['HEIGHT'])
	{
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["FILE"],
			array("width" => $arParams["WIDTH"], "height" => $arParams["HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);

		CFile::ViewByUser(array(
			'ORIGINAL_NAME' => $arResult['FILE']['ORIGINAL_NAME'],
			'FILE_SIZE' => $arFileTmp['size'],
			'SRC' => $arFileTmp['src'],
		), array(
			"content_type" => $arResult['FILE']["CONTENT_TYPE"],
		));
	}
	else
	{
		CFile::ViewByUser($arResult["FILE"], array("content_type" => $arResult["FILE"]["CONTENT_TYPE"]));
	}
}
else
{
	$ct = strtolower($arResult["FILE"]["CONTENT_TYPE"]);
	if (strpos($ct, "excel") !== false)
		CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/vnd.ms-excel"));
	elseif (strpos($ct, "word") !== false)
		CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/msword"));
	elseif (strpos($ct, "flash") !== false)
		CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/octet-stream"));
	else
	{
		switch($ct)
		{
			case "text/xml":
				CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/octet-stream", "force_download" => true));
				break;
			case "application/pdf":
				CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/octet-stream"));
				break;
			case "pdf":
				CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/octet-stream"));
				break;
			default:
				CFile::ViewByUser($arResult["FILE"], array("content_type" => "application/octet-stream", "specialchars" => true));
		}
	}
}
// *****************************************************************************************
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_after.php");
echo ShowError(GetMessage("F_ATTACH_NOT_FOUND"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog.php");
// *****************************************************************************************
?>
