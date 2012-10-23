<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("BLOG_MODULE_NOT_INSTALL"));
	return;
}
$feature = "blog";
$arParams["MICROBLOG"] = $arParams["MICROBLOG"] === "Y";
if($arParams["MICROBLOG"])
	$feature = "microblog";
$arParams["SOCNET_GROUP_ID"] = IntVal($arParams["SOCNET_GROUP_ID"]);
$arResult["bSoNet"] = false;
$arResult["bGroupMode"] = false;
if (CModule::IncludeModule("socialnetwork") && (IntVal($arParams["SOCNET_GROUP_ID"]) > 0 || IntVal($arParams["USER_ID"]) > 0))
{
	$arResult["bSoNet"] = true;
	if(IntVal($arParams["SOCNET_GROUP_ID"]) > 0)
	{
		$arResult["bGroupMode"] = true;

		if (method_exists("CSocNetGroup", "GetSite"))
		{
			$arGroupSites = array();
			$rsGroupSite = CSocNetGroup::GetSite($arParams["SOCNET_GROUP_ID"]);
			while($arGroupSite = $rsGroupSite->Fetch())
				$arGroupSites[] = $arGroupSite["LID"];
		}
	}

	if($arResult["bGroupMode"])
	{
		if($arGroupSoNet = CSocNetGroup::GetByID($arParams["SOCNET_GROUP_ID"]))
		{
			if(!CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], $feature))
			{
				if(!$arParams["MICROBLOG"])
					ShowError(GetMessage("BLOG_SONET_MODULE_NOT_AVAIBLE"));
				return;
			}
		}
		else
			return;
	}
	else
	{
		$rsUser = CUser::GetByID($arParams["USER_ID"]);
		if($arUser = $rsUser->Fetch())
		{
			if (!CSocNetFeatures::IsActiveFeature(SONET_ENTITY_USER, $arParams["USER_ID"], $feature))
			{
				if(!$arParams["MICROBLOG"])
					ShowError(GetMessage("BLOG_SONET_MODULE_NOT_AVAIBLE"));
				return;
			}
		}
		else
			return;
	}
}
$arParams["ID"] = IntVal($arParams["ID"]);
$arParams["BLOG_URL"] = preg_replace("/[^a-zA-Z0-9_-]/is", "", Trim($arParams["BLOG_URL"]));
if(!is_array($arParams["GROUP_ID"]))
	$arParams["GROUP_ID"] = array($arParams["GROUP_ID"]);
foreach($arParams["GROUP_ID"] as $k=>$v)
	if(IntVal($v) <= 0)
		unset($arParams["GROUP_ID"][$k]);
		
if(strLen($arParams["BLOG_VAR"])<=0)
	$arParams["BLOG_VAR"] = "blog";
if(strLen($arParams["PAGE_VAR"])<=0)
	$arParams["PAGE_VAR"] = "page";
if(strLen($arParams["USER_VAR"])<=0)
	$arParams["USER_VAR"] = "id";
if(strLen($arParams["POST_VAR"])<=0)
	$arParams["POST_VAR"] = "id";
	
$arParams["PATH_TO_BLOG"] = trim($arParams["PATH_TO_BLOG"]);
if(strlen($arParams["PATH_TO_BLOG"])<=0)
	$arParams["PATH_TO_BLOG"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=blog&".$arParams["BLOG_VAR"]."=#blog#");

$arParams["PATH_TO_POST"] = trim($arParams["PATH_TO_POST"]);
if(strlen($arParams["PATH_TO_POST"])<=0)
	$arParams["PATH_TO_POST"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=post&".$arParams["BLOG_VAR"]."=#blog#&".$arParams["POST_VAR"]."=#post_id#");

$arParams["PATH_TO_POST_EDIT"] = trim($arParams["PATH_TO_POST_EDIT"]);
if(strlen($arParams["PATH_TO_POST_EDIT"])<=0)
	$arParams["PATH_TO_POST_EDIT"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=post_edit&".$arParams["BLOG_VAR"]."=#blog#&".$arParams["POST_VAR"]."=#post_id#");

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if(strlen($arParams["PATH_TO_USER"])<=0)
	$arParams["PATH_TO_USER"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");

$arParams["PATH_TO_DRAFT"] = trim($arParams["PATH_TO_DRAFT"]);
if(strlen($arParams["PATH_TO_DRAFT"])<=0)
	$arParams["PATH_TO_DRAFT"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=draft&".$arParams["BLOG_VAR"]."=#blog#");

$arParams["PATH_TO_GROUP_BLOG"] = trim($arParams["PATH_TO_GROUP_BLOG"]);
if(strlen($arParams["PATH_TO_GROUP_BLOG"])<=0)
{
	$arParams["PATH_TO_GROUP_BLOG"] = "/workgroups/group/#group_id#/blog/";
	if($arParams["MICROBLOG"])
		$arParams["PATH_TO_GROUP_BLOG"] = "/workgroups/group/#group_id#/microblog/";
}
if(strlen($arParams["PATH_TO_GROUP_POST"])<=0)
{
	$arParams["PATH_TO_GROUP_POST"] = "/workgroups/group/#group_id#/blog/#post_id#/";
	if($arParams["MICROBLOG"])
		$arParams["PATH_TO_GROUP_POST"] = "/workgroups/group/#group_id#/microblog/#post_id#/";
}
if(strlen($arParams["PATH_TO_GROUP_POST_EDIT"])<=0)
{
	$arParams["PATH_TO_GROUP_POST_EDIT"] = "/workgroups/group/#group_id#/blog/edit/#post_id#/";
	if($arParams["MICROBLOG"])
		$arParams["PATH_TO_GROUP_POST_EDIT"] = "/workgroups/group/#group_id#/microblog/edit/#post_id#/?microblog=Y";
}
if(strlen($arParams["PATH_TO_GROUP_DRAFT"])<=0)
	$arParams["PATH_TO_GROUP_DRAFT"] = "/workgroups/group/#group_id#/blog/draft/";

	
$arParams["PATH_TO_SMILE"] = strlen(trim($arParams["PATH_TO_SMILE"]))<=0 ? false : trim($arParams["PATH_TO_SMILE"]);
$arParams["DATE_TIME_FORMAT"] = trim(empty($arParams["DATE_TIME_FORMAT"]) ? $DB->DateFormatToPHP(CSite::GetDateFormat("FULL")) : $arParams["DATE_TIME_FORMAT"]);

$arParams["SMILES_COUNT"] = IntVal($arParams["SMILES_COUNT"]);
if(IntVal($arParams["SMILES_COUNT"]) <= 0)
	$arParams["SMILES_COUNT"] = 5;

$arParams["ALLOW_POST_MOVE"] = ($arParams["ALLOW_POST_MOVE"] == "Y") ? "Y" : "N";

$arParams["IMAGE_MAX_WIDTH"] = IntVal($arParams["IMAGE_MAX_WIDTH"]);
$arParams["IMAGE_MAX_HEIGHT"] = IntVal($arParams["IMAGE_MAX_HEIGHT"]);

$arParams["EDITOR_RESIZABLE"] = $arParams["EDITOR_RESIZABLE"] !== "N";
$arParams["EDITOR_CODE_DEFAULT"] = $arParams["EDITOR_CODE_DEFAULT"] === "Y";
$arParams["EDITOR_DEFAULT_HEIGHT"] = intVal($arParams["EDITOR_DEFAULT_HEIGHT"]);
if(IntVal($arParams["EDITOR_DEFAULT_HEIGHT"]) <= 0)
	$arParams["EDITOR_DEFAULT_HEIGHT"] = 300;

$user_id = $USER->GetID();
$arResult["UserID"] = $user_id;
$arResult["enable_trackback"] = COption::GetOptionString("blog","enable_trackback", "Y");
$arResult["allowHTML"] = COption::GetOptionString("blog","allow_html", "N");
$arResult["allowVideo"] = COption::GetOptionString("blog","allow_video", "Y");
$blogModulePermissions = $APPLICATION->GetGroupRight("blog");

$arParams["ALLOW_POST_CODE"] = $arParams["ALLOW_POST_CODE"] !== "N";
$arParams["USE_GOOGLE_CODE"] = $arParams["USE_GOOGLE_CODE"] === "Y";

if($arParams["MICROBLOG"])
{
	$feature = "microblog";
	if($arResult["bGroupMode"])
	{
		$arGroupSoNet = CSocNetGroup::GetByID($arParams["SOCNET_GROUP_ID"]);
		if(!empty($arGroupSoNet))
		{
			if($arResult["bGroupMode"] && CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], $feature))
			{
				$arResult["SONET_GROUP"] = $arGroupSoNet;
				if ($arParams["SET_NAV_CHAIN"] != "N" || $arParams["SET_TITLE"] != "N")
				{

					$arEntityActiveFeatures = CSocNetFeatures::GetActiveFeaturesNames(SONET_ENTITY_GROUP, $arGroupSoNet["ID"]);		
					$strFeatureTitle = ((array_key_exists($feature, $arEntityActiveFeatures) && StrLen($arEntityActiveFeatures[$feature]) > 0) ? $arEntityActiveFeatures[$feature] : GetMessage("BM_BLOG_CHAIN_GROUP_MICRO"));
				}
			
				if ($arParams["SET_NAV_CHAIN"] != "N")
				{
					$APPLICATION->AddChainItem($arGroupSoNet["NAME"], CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP"], array("group_id" => $arGroupSoNet["ID"])));
					$APPLICATION->AddChainItem($strFeatureTitle, CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("group_id" => $arParams["SOCNET_GROUP_ID"])));
				}
				if ($arParams["SET_TITLE"] != "N")
					$APPLICATION->SetTitle($arGroupSoNet["NAME"].": ".$strFeatureTitle);
			}
		}
	}
	elseif(IntVal($arParams["USER_ID"]) > 0)
	{
		if(!empty($arUser))
		{
			if(CSocNetFeatures::IsActiveFeature(SONET_ENTITY_USER, $arParams["USER_ID"], $feature))
			{
				if ($arParams["SET_TITLE"] != "N" || $arParams["SET_NAV_CHAIN"] != "N")
				{
					$arEntityActiveFeatures = CSocNetFeatures::GetActiveFeaturesNames(SONET_ENTITY_USER, $arParams["USER_ID"]);		
					$strFeatureTitle = ((array_key_exists($feature, $arEntityActiveFeatures) && StrLen($arEntityActiveFeatures[$feature]) > 0) ? $arEntityActiveFeatures[$feature] : GetMessage("BM_BLOG_CHAIN_USER_MICRO"));
				
					if (strlen($arParams["NAME_TEMPLATE"]) <= 0)		
						$arParams["NAME_TEMPLATE"] = '#NOBR##NAME# #LAST_NAME##/NOBR#';
								
					$arParams["TITLE_NAME_TEMPLATE"] = str_replace(
						array("#NOBR#", "#/NOBR#"), 
						array("", ""), 
						$arParams["NAME_TEMPLATE"]
					);

					$bUseLogin = $arParams['SHOW_LOGIN'] != "N" ? true : false;
					$strTitleFormatted = CUser::FormatName($arParams['TITLE_NAME_TEMPLATE'], $arUser, $bUseLogin);	
				}

				if ($arParams["SET_NAV_CHAIN"] != "N")
				{
					$APPLICATION->AddChainItem($strTitleFormatted, CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_USER"], array("user_id" => $arUser["ID"])));
					$APPLICATION->AddChainItem($strFeatureTitle, CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("user_id" => $arUser["ID"])));
				}
			
				if ($arParams["SET_TITLE"] != "N")
					$APPLICATION->SetTitle($strTitleFormatted.": ".$strFeatureTitle);
			}
		}
	}

}
	
$arResult["preview"] = (strlen($_POST["preview"]) > 0) ? "Y" : "N";
if($arResult["bSoNet"])
{
		$arFilterblg = Array(
		        "ACTIVE" => "Y",
//				"GROUP_ID" => $arParams["GROUP_ID"],
//				"GROUP_SITE_ID" => SITE_ID,
				"USE_SOCNET" => "Y",
			);

	if($arResult["bGroupMode"])
	{
		$arFilterblg["SOCNET_GROUP_ID"] = $arParams["SOCNET_GROUP_ID"];
		$arResult["perms"] = BLOG_PERMS_DENY;
		
		if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W")
			$arResult["perms"] = BLOG_PERMS_FULL;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "moderate_post"))
			$arResult["perms"] = BLOG_PERMS_MODERATE;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "write_post"))
			$arResult["perms"] = BLOG_PERMS_WRITE;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "premoderate_post"))
			$arResult["perms"] = BLOG_PERMS_PREMODERATE;		
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arParams["SOCNET_GROUP_ID"], "blog", "view_post"))
			$arResult["perms"] = BLOG_PERMS_READ;
	}
	else
	{
		$arFilterblg["GROUP_ID"] = $arParams["GROUP_ID"];
		$arFilterblg["GROUP_SITE_ID"] = SITE_ID;
				
		$arFilterblg["OWNER_ID"] = $arParams["USER_ID"];
		$arResult["perms"] = BLOG_PERMS_DENY;
		if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W" || $arParams["USER_ID"] == $user_id)
			$arResult["perms"] = BLOG_PERMS_FULL;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "moderate_post"))
			$arResult["perms"] = BLOG_PERMS_MODERATE;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "write_post"))
			$arResult["perms"] = BLOG_PERMS_WRITE;
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "premoderate_post"))
			$arResult["perms"] = BLOG_PERMS_PREMODERATE;		
		elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arParams["USER_ID"], "blog", "view_post"))
			$arResult["perms"] = BLOG_PERMS_READ;
	}

	$dbBl = CBlog::GetList(Array(), $arFilterblg);
	$arBlog = $dbBl ->Fetch();
	
	if($arBlog["OWNER_ID"] == $user_id && IntVal($arParams["ID"]) <= 0)
	{
		$arResult["CAN_POST_SONET_GROUP"] = true;
		$rsGroups = CSocNetGroup::GetList(array("NAME" => "ASC"), array("SITE_ID" => SITE_ID, "ACTIVE" => "Y"), false, Array("nTopCount" => 1), array("ID", "SITE_ID", "ACTIVE"));
		if(!$rsGroups->Fetch())
			$arResult["CAN_POST_SONET_GROUP"] = false;
	}
}
else
{
	$arBlog = CBlog::GetByUrl($arParams["BLOG_URL"], $arParams["GROUP_ID"]);
	if(IntVal($arParams["ID"]) > 0)
		$arResult["perms"] = CBlogPost::GetBlogUserPostPerms($arParams["ID"], $arResult["UserID"]);
	else
		$arResult["perms"] = CBlog::GetBlogUserPostPerms($arBlog["ID"], $arResult["UserID"]);
}

if((!empty($arBlog) && $arBlog["ACTIVE"] == "Y") || ($arResult["bSoNet"] && empty($arBlog)))
{
	$arGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);
	if($arGroup["SITE_ID"] == SITE_ID || ($arResult["bSoNet"] && empty($arBlog)) || $arResult["bGroupMode"])
	{
		if($arResult["allowHTML"] == "Y" && $arBlog["ALLOW_HTML"] == "Y")
			$arResult["allow_html"] = "Y";
		$arResult["Blog"] = $arBlog;
		if($arResult["bGroupMode"])
			$arResult["urlToBlog"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
		else
			$arResult["urlToBlog"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));

		if(IntVal($arParams["ID"])>0 && $arPost = CBlogPost::GetByID($arParams["ID"]))
		{
			$arPost = CBlogTools::htmlspecialcharsExArray($arPost);
			$arResult["Post"] = $arPost;
			if($arParams["SET_TITLE"]=="Y" && !$arParams["MICROBLOG"])
				$APPLICATION->SetTitle(str_replace("#BLOG#", $arBlog["NAME"], "".GetMessage("BLOG_POST_EDIT").""));
		}
		else
		{
			$arParams["ID"] = 0;
			if($arParams["SET_TITLE"]=="Y" && !$arParams["MICROBLOG"])
				$APPLICATION->SetTitle(str_replace("#BLOG#", $arBlog["NAME"], "".GetMessage("BLOG_NEW_MESSAGE").""));
		}
		if (($arResult["perms"] >= BLOG_PERMS_MODERATE || ($arResult["perms"] >= BLOG_PERMS_PREMODERATE && ($arParams["ID"]==0 || $arPost["AUTHOR_ID"] == $arResult["UserID"]))) && (IntVal($arParams["ID"]) <= 0 || $arPost["BLOG_ID"]==$arBlog["ID"]))
		{
			if(IntVal($arParams["ID"]) > 0 && $arPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_READY && $arResult["perms"] == BLOG_PERMS_PREMODERATE)
			{
				$arResult["UTIL_MESSAGE"] = GetMessage("BPE_HIDDEN_POSTED");
			}
			else
			{
				if (($_POST["apply"] || $_POST["save"] || $_POST["do_upload"] || $_POST["draft"]) && $arResult["preview"] != "Y")
				{
					if(check_bitrix_sessid())
					{
						if($arResult["bSoNet"] && empty($arBlog))
						{
							if(!empty($arParams["GROUP_ID"]))
							{
								$arFields = array(
									"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
									"GROUP_ID" => (is_array($arParams["GROUP_ID"])) ? IntVal($arParams["GROUP_ID"][0]) : IntVal($arParams["GROUP_ID"]),
									"ACTIVE" => "Y",
									"ENABLE_COMMENTS" => "Y",
									"ENABLE_IMG_VERIF" => "Y",
									"EMAIL_NOTIFY" => "Y",
									"ENABLE_RSS" => "Y",
									"ALLOW_HTML" => "N",
									"ENABLE_TRACKBACK" => "N",
									"SEARCH_INDEX" => "Y",
									"USE_SOCNET" => "Y",
									"=DATE_CREATE" => $DB->CurrentTimeFunction(),
									"PERMS_POST" => Array( 
										1 => "I",
										2 => "I" ),
									"PERMS_COMMENT" => Array( 
										1 => "P",
										2 => "P" ),
								);
								
								$bRights = false;
								if($arResult["bGroupMode"])
								{
									if($arGroupSoNet = CSocNetGroup::GetByID($arParams["SOCNET_GROUP_ID"]))
									{
										if(strlen($arGroupSoNet["~NAME"]) <= 0)
											$arFields["NAME"] = GetMessage("BLG_GRP_NAME")." ".$arGroupSoNet["ID"];
										else
											$arFields["NAME"] = GetMessage("BLG_GRP_NAME")." ".$arGroupSoNet["~NAME"];
										$arFields["URL"] = $arParams["SOCNET_GROUP_ID"]."-blog";
										$arFields["SOCNET_GROUP_ID"] = $arParams["SOCNET_GROUP_ID"];
										$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_GROUP, $arFields["SOCNET_GROUP_ID"], "blog", "view_post");
										if ($featureOperationPerms == SONET_ROLES_ALL)
											$bRights = true;
									}
								}
								else
								{
									$rsUser = CUser::GetByID($arParams["USER_ID"]);
									$arUser = $rsUser->Fetch();
									if(strlen($arUser["NAME"]."".$arUser["LAST_NAME"]) <= 0)
										$arFields["NAME"] = GetMessage("BLG_NAME")." ".$arUser["LOGIN"];
									else
										$arFields["NAME"] = GetMessage("BLG_NAME")." ".$arUser["NAME"]." ".$arUser["LAST_NAME"];
										
									$arFields["URL"] = str_replace(" ", "_", $arUser["LOGIN"])."-blog-".SITE_ID;
									$arFields["OWNER_ID"] = $arParams["USER_ID"];
									
									$urlCheck = preg_replace("/[^a-zA-Z0-9_-]/is", "", $arFields["URL"]);
									if ($urlCheck != $arFields["URL"])
									{
										$arFields["URL"] = "u".$arParams["USER_ID"]."-blog-".SITE_ID;
									}
									
									if(CBlog::GetByUrl($arFields["URL"]))
									{
										$uind = 0;
										do
										{
											$uind++;
											$arFields["URL"] = $arFields["URL"].$uind;
										}
										while (CBlog::GetByUrl($arFields["URL"]));
									}
									
									$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_USER, $arFields["OWNER_ID"], "blog", "view_post");
									if ($featureOperationPerms == SONET_RELATIONS_TYPE_ALL)
										$bRights = true;
								}
								$arFields["PATH"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arFields["URL"], "user_id" => $arFields["OWNER_ID"], "group_id" => $arFields["SOCNET_GROUP_ID"]));
								
								$blogID = CBlog::Add($arFields);
								if($bRights)
									CBlog::AddSocnetRead($blogID);
								$arBlog = CBlog::GetByID($blogID, $arParams["GROUP_ID"]);
								
								if($arResult["bGroupMode"] && $arGroupSites)
									$arSites = $arGroupSites;
								else
									$arSites = array(SITE_ID);

								foreach ($arSites as $site_id_tmp)
								{
									BXClearCache(True, "/".$site_id_tmp."/blog/sonet/");
									BXClearCache(True, "/".$site_id_tmp."/blog/sonet_extranet/");
								}
							}
						}
					}
					else
						$arResult["ERROR_MESSAGE"] = GetMessage("BPE_SESS");
				}

				if ($_GET["image_upload_frame"] == "Y" || $_GET["image_upload"] || $_POST["do_upload"])
				{
					$arResult["imageUploadFrame"] = "Y";
					$arResult["imageUpload"] = "Y";
					$APPLICATION->RestartBuffer();
					header("Pragma: no-cache");

					if(check_bitrix_sessid() || strlen($_REQUEST["sessid"]) <= 0)
					{
						$arFields = Array();
						if ($_FILES["BLOG_UPLOAD_FILE"]["size"] > 0)
						{
							$arFields = array(
								"BLOG_ID"	=> $arBlog["ID"],
								"POST_ID"	=> $arParams["ID"],
								"USER_ID"	=> $arResult["UserID"],
								"=TIMESTAMP_X"	=> $DB->GetNowFunction(),
								"TITLE"		=> $_POST["IMAGE_TITLE"],
								"IMAGE_SIZE"	=> $_FILES["BLOG_UPLOAD_FILE"]["size"]
							);
							$arImage=array_merge(
								$_FILES["BLOG_UPLOAD_FILE"],
								array(
									"MODULE_ID" => "blog",
									"del" => "Y"
								)
							);
							$arFields["FILE_ID"] = $arImage;
						}
						elseif ($_POST["do_upload"] && $_FILES["FILE_ID"]["size"] > 0)
						{
							$arFields = array(
								"BLOG_ID"	=> $arBlog["ID"],
								"POST_ID"	=> $arParams["ID"],
								"USER_ID"	=> $arResult["UserID"],
								"=TIMESTAMP_X"	=> $DB->GetNowFunction(),
								"TITLE"		=> $_POST["IMAGE_TITLE"],
								"IMAGE_SIZE"	=> $_FILES["FILE_ID"]["size"]
							);
							$arImage=array_merge(
								$_FILES["FILE_ID"],
								array(
									"MODULE_ID" => "blog",
									"del" => "Y"
								)
							);
							$arFields["FILE_ID"] = $arImage;
						}
						if(!empty($arFields))
						{
							if ($imgID = CBlogImage::Add($arFields))
							{
								$aImg = CBlogImage::GetByID($imgID);
								$aImg = CBlogTools::htmlspecialcharsExArray($aImg);
								
								$iMaxW = 100;
								$iMaxH = 100;
								$aImg["PARAMS"] = CFile::_GetImgParams($aImg["FILE_ID"]);
								$intWidth = $aImg["PARAMS"]['WIDTH'];
								$intHeight = $aImg["PARAMS"]['HEIGHT'];
								if(
									$iMaxW > 0 && $iMaxH > 0
									&& ($intWidth > $iMaxW || $intHeight > $iMaxH)
								)
								{
									$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
									$iHeight = intval(roundEx($intHeight/$coeff));
									$iWidth = intval(roundEx($intWidth/$coeff));
								}
								else
								{
									$coeff = 1;
									$iHeight = $intHeight;
									$iWidth = $intWidth;
								}

								$file = "<img src=\"".$aImg["PARAMS"]["SRC"]."\" width=\"".$iWidth."\" height=\"".$iHeight."\" id=\"".$aImg["ID"]."\" border=\"0\" style=\"cursor:pointer\" onclick=\"InsertBlogImage('".$aImg["ID"]."', '".$aImg["PARAMS"]['WIDTH']."');\" title=\"".GetMessage("BLOG_P_INSERT")."\">";
								
								//$file = CFile::ShowImage($aImg["FILE_ID"], 100, 100, "id='".$aImg["ID"]."' border=0 style=cursor:pointer onclick=\"InsertBlogImage('".$aImg["ID"]."');\" title='".GetMessage("BLOG_P_INSERT")."'");
								$file = str_replace("'","\'",$file);
								$file = str_replace("\r"," ",$file);
								$file = str_replace("\n"," ",$file);
								$arResult["ImageModified"] = $file;
								$arResult["Image"] = $aImg;
							}
							else
							{
								if ($ex = $APPLICATION->GetException())
									$arResult["ERROR_MESSAGE"] = $ex->GetString();
							}
						}
					}
				}
				else
				{
					if (($_POST["apply"] || $_POST["save"]) && $arResult["preview"] != "Y" && empty($_POST["reset"])) // Save on button click
					{
						if(check_bitrix_sessid())
						{
							if(IntVal($_POST["SONETGROUP"]) > 0 && $arResult["bSoNet"] && $arResult["CAN_POST_SONET_GROUP"])
							{
								//find sonet group blog
								$arOldBlog = $arBlog;
								if($arGroupSoNet = CSocNetGroup::GetByID($_POST["SONETGROUP"]))
								{
									if(CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $_POST["SONETGROUP"], $feature))
									{
										$arFilterblg = Array(
												"ACTIVE" => "Y",
												"USE_SOCNET" => "Y",
												"SOCNET_GROUP_ID" => $_POST["SONETGROUP"],
											);
										$arResult["perms"] = BLOG_PERMS_DENY;
										
										if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $_POST["SONETGROUP"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W")
											$arResult["perms"] = BLOG_PERMS_FULL;
										elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $_POST["SONETGROUP"], "blog", "moderate_post"))
											$arResult["perms"] = BLOG_PERMS_MODERATE;
										elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $_POST["SONETGROUP"], "blog", "write_post"))
											$arResult["perms"] = BLOG_PERMS_WRITE;
										elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $_POST["SONETGROUP"], "blog", "premoderate_post"))
											$arResult["perms"] = BLOG_PERMS_PREMODERATE;		
										elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $_POST["SONETGROUP"], "blog", "view_post"))
											$arResult["perms"] = BLOG_PERMS_READ;
										if($arResult["perms"] >= BLOG_PERMS_PREMODERATE)
										{
											$dbBl = CBlog::GetList(Array(), $arFilterblg);
											$arBlog = $dbBl ->Fetch();
											if(empty($arBlog))
											{
												if(!empty($arParams["GROUP_ID"]))
												{
													$arFields = array(
														"=DATE_UPDATE" => $DB->CurrentTimeFunction(),
														"GROUP_ID" => (is_array($arParams["GROUP_ID"])) ? IntVal($arParams["GROUP_ID"][0]) : IntVal($arParams["GROUP_ID"]),
														"ACTIVE" => "Y",
														"ENABLE_COMMENTS" => "Y",
														"ENABLE_IMG_VERIF" => "Y",
														"EMAIL_NOTIFY" => "Y",
														"ENABLE_RSS" => "Y",
														"ALLOW_HTML" => "N",
														"ENABLE_TRACKBACK" => "N",
														"SEARCH_INDEX" => "Y",
														"USE_SOCNET" => "Y",
														"=DATE_CREATE" => $DB->CurrentTimeFunction(),
														"PERMS_POST" => Array( 
															1 => "I",
															2 => "I" ),
														"PERMS_COMMENT" => Array( 
															1 => "P",
															2 => "P" ),
														"SOCNET_GROUP_ID" => $_POST["SONETGROUP"],
														"URL" => $_POST["SONETGROUP"]."-blog",
													);
													
													$bRights = false;
													if(strlen($arGroupSoNet["~NAME"]) <= 0)
														$arFields["NAME"] = GetMessage("BLG_GRP_NAME")." ".$arGroupSoNet["ID"];
													else
														$arFields["NAME"] = GetMessage("BLG_GRP_NAME")." ".$arGroupSoNet["~NAME"];

													$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_GROUP, $arFields["SOCNET_GROUP_ID"], "blog", "view_post");
													if ($featureOperationPerms == SONET_ROLES_ALL)
														$bRights = true;

													$arFields["PATH"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("blog" => $arFields["URL"], "group_id" => $_POST["SONETGROUP"]));
													
													$blogID = CBlog::Add($arFields);
													if($bRights)
														CBlog::AddSocnetRead($blogID);
													$arBlog = CBlog::GetByID($blogID, $arParams["GROUP_ID"]);
													
													if (method_exists("CSocNetGroup", "GetSite"))
													{
														$arGroupSites = array();
														$rsGroupSite = CSocNetGroup::GetSite($arParams["SOCNET_GROUP_ID"]);
														while($arGroupSite = $rsGroupSite->Fetch())
															$arGroupSites[] = $arGroupSite["LID"];
													}

													if($arResult["bGroupMode"] && $arGroupSites)
														$arSites = $arGroupSites;
													else
														$arSites = array(SITE_ID);
														
													foreach ($arSites as $site_id_tmp)
													{
														BXClearCache(True, "/".$site_id_tmp."/blog/sonet/");
														BXClearCache(True, "/".$site_id_tmp."/blog/sonet_extranet/");
													}
												}
											}
										}
										else
											$arResult["ERROR_MESSAGE"] = GetMessage("B_B_MES_NO_GROUP_RIGHTS");										
									}
									else
										$arResult["ERROR_MESSAGE"] = GetMessage("B_B_MES_NO_GROUP_ACTIVE");									
								}
								else
									$arResult["ERROR_MESSAGE"] = GetMessage("B_B_MES_NO_GROUP");
							}
							
							if(strlen($arResult["ERROR_MESSAGE"]) <= 0)
							{
								$TRACKBACK = trim($_POST["TRACKBACK"]);
								InitBVar($_POST["ENABLE_TRACKBACK"]);
								
								$CATEGORYtmp = Array();
								if(!empty($_POST["TAGS"]))
								{
									$dbCategory = CBlogCategory::GetList(Array(), Array("BLOG_ID" => $arBlog["ID"]));
									while($arCategory = $dbCategory->Fetch())
									{
										$arCatBlog[ToLower($arCategory["NAME"])] = $arCategory["ID"];
									}
									$tags = explode (",", $_POST["TAGS"]);
									foreach($tags as $tg)
									{
										$tg = trim($tg);
										if(!in_array($arCatBlog[ToLower($tg)], $CATEGORYtmp))
										{
											if(IntVal($arCatBlog[ToLower($tg)]) > 0)
												$CATEGORYtmp[] = $arCatBlog[ToLower($tg)];
											else
											{
												$CATEGORYtmp[] = CBlogCategory::Add(array("BLOG_ID" => $arBlog["ID"], "NAME" => $tg));

												if($arResult["bGroupMode"] && $arGroupSites)
													$arSites = $arGroupSites;
												else
													$arSites = array(SITE_ID);

												foreach ($arSites as $site_id_tmp)
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/category/");
											}
										}
									}
								}
								elseif (!empty($_POST["CATEGORY_ID"]))
								{
									foreach($_POST["CATEGORY_ID"] as $v)
									{
										if(substr($v, 0, 4) == "new_")
										{
											$CATEGORYtmp[] = CBlogCategory::Add(array("BLOG_ID"=>$arBlog["ID"],"NAME"=>substr($v, 4
		)));

											if($arResult["bGroupMode"] && $arGroupSites)
												$arSites = $arGroupSites;
											else
												$arSites = array(SITE_ID);

											foreach ($arSites as $site_id_tmp)	
												BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/category/");
										}
										else
											$CATEGORYtmp[] = $v;
									}
								}
								else
									$CATEGORY_ID = "";
								$CATEGORY_ID = implode(",", $CATEGORYtmp);
								
								$DATE_PUBLISH = "";
								if(strlen($_POST["DATE_PUBLISH_DEF"]) > 0)
									$DATE_PUBLISH = $_POST["DATE_PUBLISH_DEF"];
								elseif (strlen($_POST["DATE_PUBLISH"])<=0)
									$DATE_PUBLISH = ConvertTimeStamp(time()+CTimeZone::GetOffset(), "FULL");
								else
									$DATE_PUBLISH = $_POST["DATE_PUBLISH"];

								if(strlen($_POST["draft"]) > 0 || $_POST["PUBLISH_STATUS"] == "D")
									$PUBLISH_STATUS = BLOG_PUBLISH_STATUS_DRAFT;
								elseif($arResult["perms"] == BLOG_PERMS_PREMODERATE)
									$PUBLISH_STATUS = BLOG_PUBLISH_STATUS_READY;
								elseif(strlen($_POST["PUBLISH_STATUS"]) <= 0 || $_POST["PUBLISH_STATUS"] == "P")
									$PUBLISH_STATUS = BLOG_PUBLISH_STATUS_PUBLISH;

								$arFields=array(
									"TITLE"			=> trim($_POST["POST_TITLE"]),
									"DETAIL_TEXT"		=> trim(($_POST["POST_MESSAGE_TYPE"] == "html")? $_POST["POST_MESSAGE_HTML"] : $_POST["POST_MESSAGE"]),
									"DETAIL_TEXT_TYPE"	=> ($arResult["allowHTML"] == "Y" && $arBlog["ALLOW_HTML"] == "Y") ? $_POST["POST_MESSAGE_TYPE"] : "text",
									"DATE_PUBLISH"		=> $DATE_PUBLISH,
									"PUBLISH_STATUS"	=> $PUBLISH_STATUS,
									"ENABLE_TRACKBACK"	=> $_POST["ENABLE_TRACKBACK"],
									"ENABLE_COMMENTS"	=> ($_POST["ENABLE_COMMENTS"] == "N") ? "N" : "Y",
									"CATEGORY_ID"		=> $CATEGORY_ID,
									"FAVORITE_SORT" => (IntVal($_POST["FAVORITE_SORT"]) > 0) ? IntVal($_POST["FAVORITE_SORT"]) : 0,
									"ATTACH_IMG" => "",
									"PATH" => CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_POST"]), array("blog" => $arBlog["URL"], "post_id" => "#post_id#", "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"])),
									"URL" => $arBlog["URL"],
								);
								if(IntVal($_POST["SONETGROUP"]) > 0 && $arResult["bSoNet"] && $arResult["CAN_POST_SONET_GROUP"])
									$arFields["PATH"] = CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_GROUP_POST"]), array("post_id" => "#post_id#", "group_id" => $_POST["SONETGROUP"]));

								if($arParams["ALLOW_POST_CODE"] && strlen(trim($_POST["CODE"])) > 0)
								{
									$arFields["CODE"] = trim($_POST["CODE"]);
									$arPCFilter = array("BLOG_ID" => $arBlog["ID"], "CODE" => $arFields["CODE"]);
									if(IntVal($arParams["ID"]) > 0)
										$arPCFilter["!ID"] = $arParams["ID"];
									$db = CBlogPost::GetList(Array(), $arPCFilter, false, Array("nTopCount" => 1), Array("ID", "CODE", "BLOG_ID"));
									if($db->Fetch())
									{
										$uind = 0;
										do
										{
											$uind++;
											$arFields["CODE"] = $arFields["CODE"].$uind;
											$arPCFilter["CODE"]  = $arFields["CODE"];
											$db = CBlogPost::GetList(Array(), $arPCFilter, false, Array("nTopCount" => 1), Array("ID", "CODE", "BLOG_ID"));
										}
										while ($db->Fetch());
									}
								}
								if($_POST["POST_MESSAGE_TYPE"] == "html" && strlen($_POST["POST_MESSAGE_HTML"]) <= 0)
									$arFields["DETAIL_TEXT"] = $_POST["POST_MESSAGE"];
								if ($_POST["blog_perms"]==1)
								{
									if ($_POST["perms_p"][1] > BLOG_PERMS_READ)
										$_POST["perms_p"][1] = BLOG_PERMS_READ;
									//if ($_POST["perms_c"][1] > BLOG_PERMS_READ)
										//$_POST["perms_c"][1] = BLOG_PERMS_READ;

									$arFields["PERMS_POST"] = $_POST["perms_p"];
									$arFields["PERMS_COMMENT"] = $_POST["perms_c"];
								}
								else
								{
									$arFields["PERMS_POST"] = array();
									$arFields["PERMS_COMMENT"] = array();
								}
								if($arParams["MICROBLOG"])
								{
									$arFields["MICRO"] = "Y";
									$arFields["TITLE"] = trim(blogTextParser::killAllTags($arFields["DETAIL_TEXT"]));
									if(strlen($arFields["TITLE"]) <= 0)
										$arFields["TITLE"] = "empty";
									//$arFields["ENABLE_COMMENTS"] = "N";
								}

								if(is_array($_POST["IMAGE_ID_title"]))
								{
									foreach($_POST["IMAGE_ID_title"] as $imgID => $imgTitle)
									{
										$aImg = CBlogImage::GetByID($imgID);
										$aImg = CBlogTools::htmlspecialcharsExArray($aImg);
										if (($aImg["BLOG_ID"]==$arBlog["ID"] || $aImg["BLOG_ID"]==$arOldBlog["ID"]) && $aImg["POST_ID"]==$arParams["ID"])
										{
											if ($_POST["IMAGE_ID_del"][$imgID])
											{
												CBlogImage::Delete($imgID);
												$arFields["DETAIL_TEXT"] = str_replace("[IMG ID=$imgID]","",$arFields["DETAIL_TEXT"]);
											}
											else
											{
												CBlogImage::Update($imgID, array("TITLE"=>$imgTitle));
											}
										}
									}
								}
								
								if (count($arParams["POST_PROPERTY"]) > 0)
								{
									$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("BLOG_POST", $arFields);
								}

								$bAdd = false;
								if ($arParams["ID"] > 0)
								{
									$arOldPost = CBlogPost::GetByID($arParams["ID"]);
									if($_POST["apply"] && strlen($_POST["PUBLISH_STATUS"]) <= 0)
										$arFields["PUBLISH_STATUS"] = $arOldPost["PUBLISH_STATUS"];
									$newID = CBlogPost::Update($arParams["ID"], $arFields);

									if (
										$arResult["bSoNet"]
										&& intval($newID) > 0
										&& $arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_DRAFT
										&& $arOldPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
									)
										CBlogPost::DeleteLog($newID, $arParams["MICROBLOG"]);
									elseif (
										$arResult["bSoNet"]
										&& intval($newID) > 0
										&& $arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
										&& $arOldPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH
									)
									{
										$arParamsUpdateLog = Array(
												"allowHTML" 		=> $arResult["allowHTML"],
												"allowVideo" 		=> $arResult["allowVideo"],
												"PATH_TO_SMILE" 	=> $arParams["PATH_TO_SMILE"],
												"MICROBLOG" 		=> ($arParams["MICROBLOG"] ? "Y" : "N")
											);
										CBlogPost::UpdateLog($newID, $arFields, $arBlog, $arParamsUpdateLog);
									}
								}
								else
								{
									$arFields["=DATE_CREATE"] = $DB->GetNowFunction();
									$arFields["AUTHOR_ID"] = $arResult["UserID"];
									$arFields["BLOG_ID"] = $arBlog["ID"];
									
									if($_POST["apply"] && strlen($_POST["PUBLISH_STATUS"]) <= 0)
										$arFields["PUBLISH_STATUS"] = BLOG_PUBLISH_STATUS_DRAFT;
									
									$newID = CBlogPost::Add($arFields);
									$bAdd = true;
									$bNeedMail = false;
								}
								if(IntVal($newID) > 0)
								{
									CBlogPostCategory::DeleteByPostID($newID);
									foreach($CATEGORYtmp as $v)
										CBlogPostCategory::Add(Array("BLOG_ID" => $arBlog["ID"], "POST_ID" => $newID, "CATEGORY_ID"=>$v));
								
									if(IntVal($_POST["SONETGROUP"]) > 0 && $arResult["bSoNet"] && $arResult["CAN_POST_SONET_GROUP"])
										$DB->Query("UPDATE b_blog_image SET POST_ID=".$newID.", BLOG_ID=".$arBlog["ID"]." WHERE BLOG_ID=".$arOldBlog["ID"]." AND POST_ID=0", true);
									else
										$DB->Query("UPDATE b_blog_image SET POST_ID=".$newID." WHERE BLOG_ID=".$arBlog["ID"]." AND POST_ID=0", true);
									
									if (strlen($TRACKBACK)>0)
									{
										$arPingUrls = explode("\n",$TRACKBACK);
										CBlogTrackback::SendPing($newID, $arPingUrls);
									}
								}
								
								//move/copy post to another blog
								if(IntVal($newID) > 0 && IntVal($_POST["move2blog"]) > 0 && $arParams["ALLOW_POST_MOVE"] == "Y") 
								{									
									if($arCopyBlog = CBlog::GetByID($_POST["move2blog"]))
									{
										$copyPerms = BLOG_PERMS_DENY;
										if($arCopyBlog["USE_SOCNET"] != "Y")
										{
											$copyPerms = CBlog::GetBlogUserPostPerms($arCopyBlog["ID"], $user_id);
										}
										else
										{
											if(IntVal($arCopyBlog["SOCNET_GROUP_ID"]) > 0)
											{
												if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arCopyBlog["SOCNET_GROUP_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W")
													$copyPerms = BLOG_PERMS_FULL;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arCopyBlog["SOCNET_GROUP_ID"], "blog", "moderate_post"))
													$copyPerms = BLOG_PERMS_MODERATE;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arCopyBlog["SOCNET_GROUP_ID"], "blog", "write_post"))
													$copyPerms = BLOG_PERMS_WRITE;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_GROUP, $arCopyBlog["SOCNET_GROUP_ID"], "blog", "premoderate_post"))
													$copyPerms = BLOG_PERMS_PREMODERATE;		
											}
											else
											{
												if (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arCopyBlog["OWNER_ID"], "blog", "full_post", CSocNetUser::IsCurrentUserModuleAdmin()) || $APPLICATION->GetGroupRight("blog") >= "W" || $arCopyBlog["OWNER_ID"] == $user_id)
													$copyPerms = BLOG_PERMS_FULL;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arCopyBlog["OWNER_ID"], "blog", "moderate_post"))
													$copyPerms = BLOG_PERMS_MODERATE;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arCopyBlog["OWNER_ID"], "blog", "write_post"))
													$copyPerms = BLOG_PERMS_WRITE;
												elseif (CSocNetFeaturesPerms::CanPerformOperation($user_id, SONET_ENTITY_USER, $arCopyBlog["OWNER_ID"], "blog", "premoderate_post"))
													$copyPerms = BLOG_PERMS_PREMODERATE;		
											}
										}
										if($copyPerms >= BLOG_PERMS_PREMODERATE)
										{
											$arCopyPost = CBlogPost::GetByID($arParams["ID"]);
											$arCopyPost["BLOG_ID"] = $arCopyBlog["ID"];
											unset($arCopyPost["ID"]);
											unset($arCopyPost["ATTACH_IMG"]);
											unset($arCopyPost["VIEWS"]);
											
											$pathTemplate = htmlspecialcharsBack($arParams["PATH_TO_POST"]);
											$pathTemplateEdit = htmlspecialcharsBack($arParams["PATH_TO_POST_EDIT"]);
											$pathTemplateDraft = htmlspecialcharsBack($arParams["PATH_TO_DRAFT"]);
											$pathTemplateBlog = htmlspecialcharsBack($arParams["PATH_TO_BLOG"]);
											if($arCopyBlog["USE_SOCNET"] == "Y")
											{
												$pathTemplate = htmlspecialcharsBack($arParams["PATH_TO_USER_POST"]);
												$pathTemplateEdit = htmlspecialcharsBack($arParams["PATH_TO_USER_POST_EDIT"]);
												$pathTemplateDraft = htmlspecialcharsBack($arParams["PATH_TO_USER_DRAFT"]);
												$pathTemplateBlog = htmlspecialcharsBack($arParams["PATH_TO_USER_BLOG"]);
												if(IntVal($arCopyBlog["SOCNET_GROUP_ID"]) > 0)
												{
													$pathTemplate = htmlspecialcharsBack($arParams["PATH_TO_GROUP_POST"]);
													$pathTemplateEdit = htmlspecialcharsBack($arParams["PATH_TO_GROUP_POST_EDIT"]);
													$pathTemplateDraft = htmlspecialcharsBack($arParams["PATH_TO_GROUP_DRAFT"]);
													$pathTemplateBlog = htmlspecialcharsBack($arParams["PATH_TO_GROUP_BLOG"]);
												}
											}
											else
											{
												//take from new params
												$pathTemplate = htmlspecialcharsBack($arParams["PATH_TO_BLOG_POST"]);
												$pathTemplateEdit = htmlspecialcharsBack($arParams["PATH_TO_BLOG_POST_EDIT"]);
												$pathTemplateDraft = htmlspecialcharsBack($arParams["PATH_TO_BLOG_DRAFT"]);
												$pathTemplateBlog = htmlspecialcharsBack($arParams["PATH_TO_BLOG_BLOG"]);
											}
											
											$arCopyPost["PATH"] = CComponentEngine::MakePathFromTemplate($pathTemplate, array("blog" => $arCopyBlog["URL"], "post_id" => "#post_id#", "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));

											$arCopyPost["PERMS_POST"] = Array();
											$arCopyPost["PERMS_COMMENT"] = Array();
											if($copyPerms == BLOG_PERMS_PREMODERATE)
												$arCopyPost["PUBLISH_STATUS"] = BLOG_PUBLISH_STATUS_READY;
											
											if($copyID = CBlogPost::Add($arCopyPost))
											{
												$arCopyPostUpdate = Array();
												//images
												$arCopyImg = Array();
												$arPat = Array();
												$arRep = Array();

												$arFilter = array(
													"POST_ID"=>$arParams["ID"], 
													"BLOG_ID"=>$arBlog["ID"],
													"IS_COMMENT" => "N",
													);
												$res = CBlogImage::GetList(array("ID"=>"ASC"), $arFilter);
												while($arImg = $res->GetNext())
												{
													$arNewImg = Array("FILE_ID" => CFile::MakeFileArray($arImg["FILE_ID"]));
													$arNewImg["BLOG_ID"] = $arCopyBlog["ID"];
													$arNewImg["POST_ID"] = $copyID;
													$arNewImg["USER_ID"] = $arImg["USER_ID"];
													$arNewImg["=TIMESTAMP_X"] = $DB->GetNowFunction();
													$arNewImg["TITLE"] = $arImg["TITLE"];
													$arNewImg["MODULE_ID"] = "blog";
													
													if($imgID = CBlogImage::Add($arNewImg))
													{
														$arPat[] = "[IMG ID=".$arImg["ID"]."]";
														$arRep[] = "[IMG ID=".$imgID."]";
													}
												}
												if(!empty($arRep))
												{
													$arCopyPostUpdate["DETAIL_TEXT"] = str_replace($arPat, $arRep, $arCopyPost["DETAIL_TEXT"]);
												}

												//tags
												$arCopyCat = Array();
												$dbCategory = CBlogCategory::GetList(Array(), Array("BLOG_ID" => $arCopyBlog["ID"]));
												while($arCategory = $dbCategory->Fetch())
												{
													$arCatBlogCopy[ToLower($arCategory["NAME"])] = $arCategory["ID"];
												}

												$dbCat = CBlogPostCategory::GetList(Array("NAME" => "ASC"), Array("BLOG_ID" => $arBlog["ID"], "POST_ID" => $arParams["ID"]));
												while($arCat = $dbCat->Fetch())
												{
													if(empty($arCatBlogCopy[ToLower($arCat["NAME"])]))
														$v = CBlogCategory::Add(array("BLOG_ID" => $arCopyBlog["ID"], "NAME" => $arCat["NAME"]));
													else
														$v = $arCatBlogCopy[ToLower($arCat["NAME"])];
													CBlogPostCategory::Add(Array("BLOG_ID" => $arCopyBlog["ID"], "POST_ID" => $copyID, "CATEGORY_ID"=>$v));
													$arCopyCat[] = $v;
												}
												if(!empty($arCopyCat))
													$arCopyPostUpdate["CATEGORY_ID"] = implode(",", $arCopyCat);
												
												if($_POST["move2blogcopy"] == "Y")
													$arCopyPostUpdate["NUM_COMMENTS"] = 0;

												if(!empty($arCopyPostUpdate))
												{
													$copyID = CBlogPost::Update($copyID, $arCopyPostUpdate);
													$arCopyPost = CBlogPost::GetByID($copyID);
												}
												
												if($_POST["move2blogcopy"] != "Y")
												{
													if((!$arResult["bSoNet"] && CBlogPost::CanUserDeletePost($arParams["ID"], $user_id)) || ($arResult["bSoNet"] && CBlogSoNetPost::CanUserDeletePost($arParams["ID"], $user_id, $arParams["USER_ID"], $arParams["SOCNET_GROUP_ID"])))
													{
														$dbC = CBlogComment::GetList(Array("ID" => "ASC"), Array("BLOG_ID" => $arBlog["ID"], "POST_ID" => $arParams["ID"]), false, false, Array("PATH" , "PUBLISH_STATUS" , "POST_TEXT" , "TITLE" , "DATE_CREATE" , "AUTHOR_IP1" , "AUTHOR_IP" , "AUTHOR_EMAIL" , "AUTHOR_NAME" , "AUTHOR_ID" , "PARENT_ID" , "POST_ID" , "BLOG_ID" , "ID"));
														while($arC = $dbC->Fetch())
														{
															$arCTmp = Array(
																"BLOG_ID" => $arCopyBlog["ID"],
																"POST_ID" => $copyID,
																);
															CBlogComment::Update($arC["ID"], $arCTmp);
														}
														$arFilter = array(
															"POST_ID"=>$arParams["ID"], 
															"BLOG_ID"=>$arBlog["ID"],
															"IS_COMMENT" => "Y",
															);
														$res = CBlogImage::GetList(array("ID"=>"ASC"), $arFilter);
														while($arImg = $res->GetNext())
														{
															$arNewImg = Array(
																	"BLOG_ID" => $arCopyBlog["ID"],
																	"POST_ID" => $copyID,
																);
															
															CBlogImage::Update($arImg["ID"], $arNewImg);
														}
														
														if(!CBlogPost::Delete($arParams["ID"]))
															$arResult["ERROR_MESSAGE"] = GetMessage("BPE_COPY_DELETE_ERROR");
													}
												}

												if($arResult["bGroupMode"] && $arGroupSites)
													$arSites = $arGroupSites;
												else
													$arSites = array(SITE_ID);

												foreach ($arSites as $site_id_tmp)
												{
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arCopyBlog["URL"]."/first_page/");
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arCopyBlog["URL"]."/calendar/");
													BXClearCache(True, "/".$site_id_tmp."/blog/last_messages/");
													BXClearCache(True, "/".$site_id_tmp."/blog/commented_posts/");
													BXClearCache(True, "/".$site_id_tmp."/blog/popular_posts/");
													BXClearCache(True, "/".$site_id_tmp."/blog/last_comments/");
													BXClearCache(True, "/".$site_id_tmp."/blog/groups/".$arCopyBlog["GROUP_ID"]."/");
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arCopyBlog["URL"]."/rss_out/");
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arCopyBlog["URL"]."/rss_all/");
													BXClearCache(True, "/".$site_id_tmp."/blog/rss_sonet/");
													BXClearCache(True, "/".$site_id_tmp."/blog/rss_all/");
													BXClearCache(True, "/".$site_id_tmp."/blog/".$arCopyBlog["URL"]."/favorite/");
													BXClearCache(True, "/".$site_id_tmp."/blog/last_messages_list_extranet/");
													BXClearCache(True, "/".$site_id_tmp."/blog/last_messages_list/");
												}
												
												if($arResult["bSoNet"] && $arCopyBlog["OWNER_ID"] != $user_id)
												{
													$dbB = CBlog::GetList(Array(), Array("OWNER_ID" => $user_id, "GROUP_ID" => $arParams["GROUP_ID"], "GROUP_SITE_ID" => SITE_ID), false, false, Array("ID", "OWNER_ID", "URL"));
													if($arB = $dbB->Fetch())
													{
														BXClearCache(True, "/".SITE_ID."/blog/".$arB["URL"]."/first_page/");
													}
												}

											}
											else
											{
												$arResult["ERROR_MESSAGE"] = GetMessage("BPE_COPY_ERROR");
												if($ex = $APPLICATION->GetException())
													$arResult["ERROR_MESSAGE"] .= $ex->GetString();
											}
										}
										else
											$arResult["ERROR_MESSAGE"] = GetMessage("BPE_COPY_NO_PERM");
									}
									else
										$arResult["ERROR_MESSAGE"] = GetMessage("BPE_COPY_NO_BLOG");
								}
								
								if(
									(
										($bAdd && $newID && $arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH)
										|| ($arOldPost["PUBLISH_STATUS"] != BLOG_PUBLISH_STATUS_PUBLISH && $arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH)
									) 
									&& (
										IntVal($copyID) <= 0 
										|| (IntVal($copyID) > 0 && $_POST["move2blogcopy"] == "Y")
									)
								)
								{
									$arFields["ID"] = $newID;
									$arParamsNotify = Array(
										"bSoNet" => $arResult["bSoNet"],
										"UserID" => $arResult["UserID"],
										"allowHTML" => $arResult["allowHTML"],
										"allowVideo" => $arResult["allowVideo"],
										"bGroupMode" => $arResult["bGroupMode"],
										"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
										"PATH_TO_POST" => $arParams["PATH_TO_POST"],
										"SOCNET_GROUP_ID" => $arParams["SOCNET_GROUP_ID"],
										"user_id" => $user_id,
										"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
										"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
										"MICROBLOG" => ($arParams["MICROBLOG"]) ? "Y" : "N",
										);
									if(IntVal($_POST["SONETGROUP"]) > 0 && $arResult["bSoNet"] && $arResult["CAN_POST_SONET_GROUP"])
									{
										$arParamsNotify["bGroupMode"] = true;
										$arParamsNotify["SOCNET_GROUP_ID"] = $_POST["SONETGROUP"];
										$arParamsNotify["PATH_TO_POST"] = $arParams["PATH_TO_GROUP_POST"];
									}
									
									CBlogPost::Notify($arFields, $arBlog, $arParamsNotify);
									
									if(COption::GetOptionString("blog","send_blog_ping", "N") == "Y")
									{
										if(strlen($serverName) <= 0)
										{
											$dbSite = CSite::GetByID(SITE_ID);
											$arSite = $dbSite -> Fetch();
											$serverName = htmlspecialcharsEx($arSite["SERVER_NAME"]);
											if (strlen($serverName) <=0)
											{
												if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME)>0)
													$serverName = SITE_SERVER_NAME;
												else
													$serverName = COption::GetOptionString("main", "server_name", "");
												if (strlen($serverName) <=0)
													$serverName = $_SERVER["SERVER_NAME"];
											}
										}

										$blogUrl = "http://".$serverName.CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_BLOG"]), array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arBlog["SOCNET_GROUP_ID"]));
										CBlog::SendPing($arBlog["NAME"], $blogUrl);
									}
								}
								
								if(IntVal($copyID) > 0 && $arCopyPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_PUBLISH)
								{
									$arCopyPost["ID"] = $copyID;
									$arParamsNotify = Array(
										"bSoNet" => ($arCopyBlog["USE_SOCNET"] == "Y") ? true : false,
										"UserID" => $arResult["UserID"],
										"allowHTML" => $arResult["allowHTML"],
										"allowVideo" => $arResult["allowVideo"],
										"bGroupMode" => ($arCopyBlog["USE_SOCNET"] == "Y" && IntVal($arCopyBlog["SOCNET_GROUP_ID"]) >0) ? true : false,
										"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
										"PATH_TO_POST" => $pathTemplate,
										"SOCNET_GROUP_ID" => $arCopyBlog["SOCNET_GROUP_ID"],
										"user_id" => $user_id,
										"MICROBLOG" => ($arParams["MICROBLOG"]) ? "Y" : "N",
										);

									CBlogPost::Notify($arCopyPost, $arCopyBlog, $arParamsNotify);
									
									if(COption::GetOptionString("blog","send_blog_ping", "N") == "Y")
									{
										if(strlen($serverName) <= 0)
										{
											$dbSite = CSite::GetByID(SITE_ID);
											$arSite = $dbSite -> Fetch();
											$serverName = htmlspecialcharsEx($arSite["SERVER_NAME"]);
											if (strlen($serverName) <=0)
											{
												if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME)>0)
													$serverName = SITE_SERVER_NAME;
												else
													$serverName = COption::GetOptionString("main", "server_name", "");
												if (strlen($serverName) <=0)
													$serverName = $_SERVER["SERVER_NAME"];
											}
										}

										$blogUrl = "http://".$serverName.CComponentEngine::MakePathFromTemplate($pathTemplateBlog, array("blog" => $arCopyBlog["URL"], "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));
										CBlog::SendPing($arCopyBlog["NAME"], $blogUrl);
									}
								}
								
								if ($newID > 0 && strlen($arResult["ERROR_MESSAGE"]) <= 0) // Record saved successfully
								{
									$arParams["ID"] = $newID;

									if($arResult["bGroupMode"] && $arGroupSites)
										$arSites = $arGroupSites;
									else
										$arSites = array(SITE_ID);

									foreach ($arSites as $site_id_tmp)
									{
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/first_page/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/calendar/");
										BXClearCache(True, "/".$site_id_tmp."/blog/last_messages/");
										BXClearCache(True, "/".$site_id_tmp."/blog/commented_posts/");
										BXClearCache(True, "/".$site_id_tmp."/blog/popular_posts/");
										BXClearCache(True, "/".$site_id_tmp."/blog/last_comments/");
										BXClearCache(True, "/".$site_id_tmp."/blog/groups/".$arBlog["GROUP_ID"]."/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/rss_out/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/rss_all/");
										BXClearCache(True, "/".$site_id_tmp."/blog/rss_sonet/");
										BXClearCache(True, "/".$site_id_tmp."/blog/rss_all/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/favorite/");
										BXClearCache(True, "/".$site_id_tmp."/blog/last_messages_list_extranet/");
										BXClearCache(True, "/".$site_id_tmp."/blog/last_messages_list/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/comment/".$arParams["ID"]."/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/trackback/".$arParams["ID"]."/");
										BXClearCache(True, "/".$site_id_tmp."/blog/".$arBlog["URL"]."/post/".$arParams["ID"]."/");
									}
									
									if($arResult["bSoNet"] && $arBlog["OWNER_ID"] != $user_id)
									{
										$dbB = CBlog::GetList(Array(), Array("OWNER_ID" => $user_id, "GROUP_ID" => $arParams["GROUP_ID"], "GROUP_SITE_ID" => SITE_ID), false, false, Array("ID", "OWNER_ID", "URL"));
										if($arB = $dbB->Fetch())
										{
											BXClearCache(True, "/".SITE_ID."/blog/".$arB["URL"]."/first_page/");
										}
									}

									if($arResult["bGroupMode"])
										CSocNetGroup::SetLastActivity($arParams["SOCNET_GROUP_ID"]);

									if(IntVal($copyID) > 0 && $_POST["move2blogcopy"] != "Y")
									{
										if (strlen($_POST["apply"])<=0)
										{
											if($arCopyPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_DRAFT || strlen($_POST["draft"]) > 0)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($pathTemplateDraft, array("blog" => $arCopyBlog["URL"], "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));
											elseif($arCopyPost["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_READY)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($pathTemplateEdit, array("blog" => $arCopyBlog["URL"], "post_id" => $copyID, "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));
											else
												$redirectUrl = CComponentEngine::MakePathFromTemplate($pathTemplateBlog, array("blog" => $arCopyBlog["URL"], "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));
										}
										else
											$redirectUrl = CComponentEngine::MakePathFromTemplate($pathTemplateEdit, array("blog" => $arCopyBlog["URL"], "post_id" => $copyID, "user_id" => $arCopyBlog["OWNER_ID"], "group_id" => $arCopyBlog["SOCNET_GROUP_ID"]));
									}
									elseif(IntVal($_POST["SONETGROUP"]) > 0 && $arResult["bSoNet"] && $arResult["CAN_POST_SONET_GROUP"])
									{
										if (strlen($_POST["apply"])<=0)
										{
											if($arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_DRAFT || strlen($_POST["draft"]) > 0)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_DRAFT"], array("group_id" => $arBlog["SOCNET_GROUP_ID"]));
											elseif($arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_READY)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_POST_EDIT"], array("post_id"=>$newID, "group_id" => $arBlog["SOCNET_GROUP_ID"]));
											else
												$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("group_id" => $arBlog["SOCNET_GROUP_ID"]));
										}
										else
											$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_POST_EDIT"], array("post_id"=>$newID, "group_id" => $arBlog["SOCNET_GROUP_ID"]));

									}
									else
									{
										if (strlen($_POST["apply"])<=0)
										{
											if($arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_DRAFT || strlen($_POST["draft"]) > 0)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DRAFT"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
											elseif($arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_READY)
												$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_POST_EDIT"], array("blog" => $arBlog["URL"], "post_id"=>$newID, "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
											else
											{
												if($arResult["bGroupMode"])
													$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
												else
													$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
											}
										}
										else
											$redirectUrl = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_POST_EDIT"], array("blog" => $arBlog["URL"], "post_id"=>$newID, "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
									}
									$as = new CAutoSave();
									LocalRedirect($redirectUrl);
								}
								else
								{
									if(strlen($arResult["ERROR_MESSAGE"]) <= 0)
									{
										if ($ex = $APPLICATION->GetException())
											$arResult["ERROR_MESSAGE"] = $ex->GetString()."<br />";
										else
											$arResult["ERROR_MESSAGE"] = "Error saving data to database.<br />";
									}
								}
							}
						}
						else
							$arResult["ERROR_MESSAGE"] = GetMessage("BPE_SESS");
					}
					elseif($_POST["reset"])
					{
						if($arFields["PUBLISH_STATUS"] == BLOG_PUBLISH_STATUS_DRAFT)
							LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DRAFT"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"])));
						else
						{
							if($arResult["bGroupMode"])
								LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_GROUP_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"])));
							else
								LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_BLOG"], array("blog" => $arBlog["URL"], "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"])));
						}
					}

					if ($arParams["ID"] > 0 && strlen($arResult["ERROR_MESSAGE"])<=0 && $arResult["preview"] != "Y") // Edit post
					{
						$arResult["PostToShow"]["TITLE"] = $arPost["TITLE"];
						$arResult["PostToShow"]["DETAIL_TEXT"] = $arPost["DETAIL_TEXT"];
						$arResult["PostToShow"]["~DETAIL_TEXT"] = $arPost["~DETAIL_TEXT"];
						$arResult["PostToShow"]["DETAIL_TEXT_TYPE"] = $arPost["DETAIL_TEXT_TYPE"];
						$arResult["PostToShow"]["PUBLISH_STATUS"] = $arPost["PUBLISH_STATUS"];
						$arResult["PostToShow"]["ENABLE_TRACKBACK"] = $arPost["ENABLE_TRACKBACK"] == "Y";
						$arResult["PostToShow"]["ENABLE_COMMENTS"] = $arPost["ENABLE_COMMENTS"];
						$arResult["PostToShow"]["ATTACH_IMG"] = $arPost["ATTACH_IMG"];
						$arResult["PostToShow"]["DATE_PUBLISH"] = $arPost["DATE_PUBLISH"];
						$arResult["PostToShow"]["CATEGORY_ID"] = $arPost["CATEGORY_ID"];
						$arResult["PostToShow"]["FAVORITE_SORT"] = $arPost["FAVORITE_SORT"];
						if($arParams["ALLOW_POST_CODE"])
							$arResult["PostToShow"]["CODE"] = $arPost["CODE"];

						$res = CBlogUserGroupPerms::GetList(array("ID"=>"DESC"),array("BLOG_ID"=>$arBlog["ID"],"POST_ID"=>$arParams["ID"]));
						while($arPerms = $res->Fetch())
						{
							if ($arPerms["AUTOSET"]=="N")
								$arResult["PostToShow"]["ExtendedPerms"] = "Y";
							if ($arPerms["PERMS_TYPE"]=="P")
								$arResult["PostToShow"]["arUGperms_p"][$arPerms["USER_GROUP_ID"]] = $arPerms["PERMS"];
							elseif ($arPerms["PERMS_TYPE"]=="C")
								$arResult["PostToShow"]["arUGperms_c"][$arPerms["USER_GROUP_ID"]] = $arPerms["PERMS"];
						}
						
						if($arParams["ALLOW_POST_MOVE"] == "Y")
						{
							//copy or move post to another blog
							if($USER->IsAdmin() || $blogModulePermissions >= "W")
							{
								$arFlt = Array(
										"ACTIVE" => "Y",
										"GROUP_SITE_ID" => SITE_ID,
										"!ID" => $arBlog["ID"],
									);

								$dbBlog = CBlog::GetList(Array("NAME" => "ASC"), $arFlt, false, false, array("ID", "NAME", "OWNER_ID", "URL", "SOCNET_GROUP_ID", "USE_SOCNET", "GROUP_ID", "GROUP_NAME"));
								while($arBlogS = $dbBlog->GetNext())
								{
									$arBlogS["PERMS"] = BLOG_PERMS_FULL;
									$arResult["avBlog"][$arBlogS["ID"]] = $arBlogS;
								}
							}
							else
							{
								$arFlt = Array(
										"USE_SOCNET" => "N",
										">=PERMS" => BLOG_PERMS_PREMODERATE,
										"PERMS_TYPE" => BLOG_PERMS_POST,
										"PERMS_USER_ID" => $user_id,
										"PERMS_POST_ID" => false,
										"ACTIVE" => "Y",
										"GROUP_SITE_ID" => SITE_ID,
										"!ID" => $arBlog["ID"],
									);

								$dbBlog = CBlog::GetList(Array("NAME" => "ASC"), $arFlt, false, false, array("ID", "NAME", "OWNER_ID", "URL", "SOCNET_GROUP_ID", "PERMS", "GROUP_ID", "GROUP_NAME"));
								while($arBlogS = $dbBlog->GetNext())
								{
									$arBlogS["USE_SOCNET"] = "N";
									$arResult["avBlog"][$arBlogS["ID"]] = $arBlogS;
								}
								$arFlt = Array(
										"OWNER_ID" => $user_id,
										"ACTIVE" => "Y",
										"GROUP_SITE_ID" => SITE_ID,
										"!ID" => $arBlog["ID"],
									);

								$dbBlog = CBlog::GetList(Array("NAME" => "ASC"), $arFlt, false, false, array("ID", "NAME", "OWNER_ID", "URL", "SOCNET_GROUP_ID", "USE_SOCNET", "GROUP_ID", "GROUP_NAME"));
								while($arBlogS = $dbBlog->GetNext())
								{
									$arBlogS["PERMS"] = BLOG_PERMS_FULL;
									$arResult["avBlog"][$arBlogS["ID"]] = $arBlogS;
								}
								
								if(CModule::IncludeModule("socialnetwork"))
								{
									$dbRes = CBlog::GetWritableSocnetBlogs($user_id, "G", SITE_ID);
									while($arRes = $dbRes->GetNext())
									{
										$arBlogS = Array(
												"ID" => $arRes["BLOG_ID"],
												"NAME" => $arRes["BLOG_NAME"],
												"URL" => $arRes["BLOG_URL"],
												"USE_SOCNET" => "Y",
												"SOCNET_GROUP_ID" => $arRes["ID"],
											);
										
										if(IntVal($arRes["USER_ID"]) > 0 || in_array($arRes["ROLE"], Array(SONET_ROLES_AUTHORIZED, SONET_ROLES_ALL)))
										{
											if($arRes["OPERATION_ID"] == "premoderate_post")
												$arBlogS["PERMS"] = BLOG_PERMS_PREMODERATE;
											elseif($arRes["OPERATION_ID"] == "write_post")
												$arBlogS["PERMS"] = BLOG_PERMS_WRITE;
											elseif($arRes["OPERATION_ID"] == "moderate_post")
												$arBlogS["PERMS"] = BLOG_PERMS_MODERATE;
											elseif($arRes["OPERATION_ID"] == "full_post")
												$arBlogS["PERMS"] = BLOG_PERMS_FULL;
											if(empty($arResult["avBlog"][$arBlogS["ID"]]) || (!empty($arResult["avBlog"][$arBlogS["ID"]]) && $arResult["avBlog"][$arBlogS["ID"]]["PERMS"] < $arBlogS["PERMS"]))
												$arResult["avBlog"][$arBlogS["ID"]] = $arBlogS;
												
										}
									}
									
									$dbRes = CBlog::GetWritableSocnetBlogs($user_id, "U", SITE_ID);
									while($arRes = $dbRes->GetNext())
									{
										$arBlogS = Array(
												"ID" => $arRes["BLOG_ID"],
												"NAME" => $arRes["BLOG_NAME"],
												"URL" => $arRes["BLOG_URL"],
												"USE_SOCNET" => "Y",
												"OWNER_ID" => $arRes["ID"],
											);
										
										if(IntVal($arRes["FIRST_USER_ID"]) > 0 || IntVal($arRes["SECOND_USER_ID"]) > 0 || in_array($arRes["ROLE"], Array(SONET_RELATIONS_TYPE_AUTHORIZED, SONET_RELATIONS_TYPE_ALL)))
										{
											if($arRes["OPERATION_ID"] == "premoderate_post")
												$arBlogS["PERMS"] = BLOG_PERMS_PREMODERATE;
											elseif($arRes["OPERATION_ID"] == "write_post")
												$arBlogS["PERMS"] = BLOG_PERMS_WRITE;
											elseif($arRes["OPERATION_ID"] == "moderate_post")
												$arBlogS["PERMS"] = BLOG_PERMS_MODERATE;
											elseif($arRes["OPERATION_ID"] == "full_post")
												$arBlogS["PERMS"] = BLOG_PERMS_FULL;
											if(empty($arResult["avBlog"][$arBlogS["ID"]]) || (!empty($arResult["avBlog"][$arBlogS["ID"]]) && $arResult["avBlog"][$arBlogS["ID"]]["PERMS"] < $arBlogS["PERMS"]))
												$arResult["avBlog"][$arBlogS["ID"]] = $arBlogS;
												
										}
									}
								}
							}
							foreach($arResult["avBlog"] as $id => $blog)
							{
								if($blog["USE_SOCNET"] == "Y")
								{
									if(IntVal($blog["SOCNET_GROUP_ID"]) > 0)
										$arResult["avBlogCategory"]["socnet_groups"][$id] = $blog;
									else
										$arResult["avBlogCategory"]["socnet_users"][$id] = $blog;
								
								}
								else
									$arResult["avBlogCategory"]["users_".$blog["GROUP_ID"]][$id] = $blog;
							}
						}
					}
					else
					{
						$arResult["PostToShow"]["TITLE"] = htmlspecialcharsEx($_POST["POST_TITLE"]);
						$arResult["PostToShow"]["CATEGORY_ID"] = $_POST["CATEGORY_ID"];
						$arResult["PostToShow"]["CategoryText"] = htmlspecialcharsEx($_POST["TAGS"]);
						$arResult["PostToShow"]["DETAIL_TEXT_TYPE"] = htmlspecialcharsEx($_POST["POST_MESSAGE_TYPE"]);
						$arResult["PostToShow"]["DETAIL_TEXT"] = (($_POST["POST_MESSAGE_TYPE"] == "html")? $_POST["POST_MESSAGE_HTML"] : htmlspecialcharsEx($_POST["POST_MESSAGE"]));
						$arResult["PostToShow"]["~DETAIL_TEXT"] = (($_POST["POST_MESSAGE_TYPE"] == "html")? $_POST["POST_MESSAGE_HTML"] : $_POST["POST_MESSAGE"]);
						$arResult["PostToShow"]["PUBLISH_STATUS"] = htmlspecialcharsEx($_POST["PUBLISH_STATUS"]);
						$arResult["PostToShow"]["ENABLE_TRACKBACK"] = htmlspecialcharsEx($_POST["ENABLE_TRACKBACK"]);
						$arResult["PostToShow"]["ENABLE_COMMENTS"] = htmlspecialcharsEx($_POST["ENABLE_COMMENTS"]);
						$arResult["PostToShow"]["TRACKBACK"] = htmlspecialcharsEx($_POST["TRACKBACK"]);
						$arResult["PostToShow"]["DATE_PUBLISH"] = $_POST["DATE_PUBLISH"] ? htmlspecialcharsEx($_POST["DATE_PUBLISH"]) : ConvertTimeStamp(time()+CTimeZone::GetOffset(),"FULL");
						$arResult["PostToShow"]["FAVORITE_SORT"] = htmlspecialcharsEx($_POST["FAVORITE_SORT"]);
						if($_POST["POST_MESSAGE_TYPE"] == "html" && strlen($_POST["POST_MESSAGE_HTML"]) <= 0)
						{
							$arResult["PostToShow"]["DETAIL_TEXT"] = htmlspecialcharsEx($_POST["POST_MESSAGE"]);
							$arResult["PostToShow"]["~DETAIL_TEXT"] = $_POST["POST_MESSAGE"];
						}

						if($arParams["ALLOW_POST_CODE"])
							$arResult["PostToShow"]["CODE"] = htmlspecialcharsEx($_POST["CODE"]);
							
						if ($_POST["apply"] || $_POST["save"] || $arResult["preview"] == "Y")
						{
							$arResult["PostToShow"]["arUGperms_p"] = $_POST["perms_p"];
							$arResult["PostToShow"]["arUGperms_c"] = $_POST["perms_c"];
							$arResult["PostToShow"]["ExtendedPerms"] = (IntVal($_POST["blog_perms"])==1 ? "Y" : "N");
						}
						else
						{
							$res = CBlogUserGroupPerms::GetList(array("ID"=>"DESC"),array("BLOG_ID"=>$arBlog["ID"],"POST_ID"=>0));
							while($arPerms = $res->Fetch())
							{
								if ($arPerms["PERMS_TYPE"]=="P")
									$arResult["PostToShow"]["arUGperms_p"][$arPerms["USER_GROUP_ID"]] = $arPerms["PERMS"];
								elseif ($arPerms["PERMS_TYPE"]=="C")
									$arResult["PostToShow"]["arUGperms_c"][$arPerms["USER_GROUP_ID"]] = $arPerms["PERMS"];
							}
						}
					}
					$arResult["BLOG_POST_PERMS"] = $GLOBALS["AR_BLOG_POST_PERMS"];
					$arResult["BLOG_COMMENT_PERMS"] = $GLOBALS["AR_BLOG_COMMENT_PERMS"];
					
					if(!$USER->IsAdmin() && $blogModulePermissions < "W")
					{
						$arResult["post_everyone_max_rights"] = COption::GetOptionString("blog", "post_everyone_max_rights", "");
						$arResult["comment_everyone_max_rights"] = COption::GetOptionString("blog", "comment_everyone_max_rights", "");
						$arResult["post_auth_user_max_rights"] = COption::GetOptionString("blog", "post_auth_user_max_rights", "");
						$arResult["comment_auth_user_max_rights"] = COption::GetOptionString("blog", "comment_auth_user_max_rights", "");
						$arResult["post_group_user_max_rights"] = COption::GetOptionString("blog", "post_group_user_max_rights", "");
						$arResult["comment_group_user_max_rights"] = COption::GetOptionString("blog", "comment_group_user_max_rights", "");
						
						foreach($arResult["BLOG_POST_PERMS"] as  $v)
						{
							if(strlen($arResult["post_everyone_max_rights"]) > 0 && $v <= $arResult["post_everyone_max_rights"])
								$arResult["ar_post_everyone_rights"][] = $v;
							if(strlen($arResult["post_auth_user_max_rights"]) > 0 && $v <= $arResult["post_auth_user_max_rights"])
								$arResult["ar_post_auth_user_rights"][] = $v;
							if(strlen($arResult["post_group_user_max_rights"]) > 0 && $v <= $arResult["post_group_user_max_rights"])
								$arResult["ar_post_group_user_rights"][] = $v;

						}

						foreach($arResult["BLOG_COMMENT_PERMS"] as  $v)
						{
							if(strlen($arResult["comment_everyone_max_rights"]) > 0 && $v <= $arResult["comment_everyone_max_rights"])
								$arResult["ar_comment_everyone_rights"][] = $v;
							if(strlen($arResult["comment_auth_user_max_rights"]) > 0 && $v <= $arResult["comment_auth_user_max_rights"])
								$arResult["ar_comment_auth_user_rights"][] = $v;
							if(strlen($arResult["comment_group_user_max_rights"]) > 0 && $v <= $arResult["comment_group_user_max_rights"])
								$arResult["ar_comment_group_user_rights"][] = $v;
						}
					}

					$arResult["UserGroups"] = array();
					$res = CBlogUserGroup::GetList(array(),$arFilter=array("BLOG_ID"=>$arBlog["ID"]));
					while ($aUGroup = $res->GetNext())
						$arResult["UserGroups"][] = $aUGroup;

					$arSelectFields = array("ID", "SMILE_TYPE", "TYPING", "IMAGE", "DESCRIPTION", "CLICKABLE", "SORT", "IMAGE_WIDTH", "IMAGE_HEIGHT", "LANG_NAME");
					$total = 0;
					$arSmiles = array();
					$res = CBlogSmile::GetList(array("SORT"=>"ASC","ID"=>"DESC"), array("SMILE_TYPE"=>"S", "LANG_LID"=>LANGUAGE_ID), false, false, $arSelectFields);
					while ($arr = $res->GetNext())
					{
						$total++;
						list($type)=explode(" ",$arr["TYPING"]);
						$arr["TYPE"]=str_replace("'","\'",$type);
						$arr["TYPE"]=str_replace("\\","\\\\",$arr["TYPE"]);
						$arSmiles[] = $arr;
					}
					$arResult["Smiles"] = $arSmiles;
					$arResult["SmilesCount"] = $total;
					
					$arResult["Images"] = Array();
					if(!empty($arBlog))
					{
						$arFilter = array(
								"POST_ID" => $arParams["ID"], 
								"BLOG_ID" => $arBlog["ID"],
								"IS_COMMENT" => "N",
							);
						if ($arParams["ID"]==0)
							$arFilter["USER_ID"] = $arResult["UserID"];
					
						$iMaxW = 100;
						$iMaxH = 100;
						$res = CBlogImage::GetList(array("ID"=>"ASC"), $arFilter);
						while($aImg = $res->GetNext())
						{
							$aImg["PARAMS"] = CFile::_GetImgParams($aImg["FILE_ID"]);
							$intWidth = $aImg["PARAMS"]['WIDTH'];
							$intHeight = $aImg["PARAMS"]['HEIGHT'];
							if(
								$iMaxW > 0 && $iMaxH > 0
								&& ($intWidth > $iMaxW || $intHeight > $iMaxH)
							)
							{
								$coeff = ($intWidth/$iMaxW > $intHeight/$iMaxH? $intWidth/$iMaxW : $intHeight/$iMaxH);
								$iHeight = intval(roundEx($intHeight/$coeff));
								$iWidth = intval(roundEx($intWidth/$coeff));
							}
							else
							{
								$coeff = 1;
								$iHeight = $intHeight;
								$iWidth = $intWidth;
							}

							$aImg["FileShow"] = "<img src=\"".$aImg["PARAMS"]["SRC"]."\" width=\"".$iWidth."\" height=\"".$iHeight."\" id=\"".$aImg["ID"]."\" border=\"0\" style=\"cursor:pointer\" onclick=\"InsertBlogImage('".$aImg["ID"]."', '".$aImg["PARAMS"]['WIDTH']."');\" title=\"".GetMessage("BLOG_P_INSERT")."\">";
							$arResult["Images"][] = $aImg;
						}
					}
					
					if(strpos($arResult["PostToShow"]["CATEGORY_ID"], ",")!==false)
						$arResult["PostToShow"]["CATEGORY_ID"] = explode(",", trim($arResult["PostToShow"]["CATEGORY_ID"]));

					$arResult["Category"] = Array();
					
					
					if(strlen($arResult["PostToShow"]["CategoryText"]) <= 0)
					{
						$res = CBlogCategory::GetList(array("NAME"=>"ASC"),array("BLOG_ID"=>$arBlog["ID"]));
						while ($arCategory=$res->GetNext())
						{
							if(is_array($arResult["PostToShow"]["CATEGORY_ID"]))
							{
								if(in_array($arCategory["ID"], $arResult["PostToShow"]["CATEGORY_ID"]))
									$arCategory["Selected"] = "Y";
							}
							else
							{
								if(IntVal($arCategory["ID"])==IntVal($arResult["PostToShow"]["CATEGORY_ID"]))
									$arCategory["Selected"] = "Y";
							}
							if($arCategory["Selected"] == "Y")
								$arResult["PostToShow"]["CategoryText"] .= $arCategory["~NAME"].", ";

							$arResult["Category"][$arCategory["ID"]] = $arCategory;
						}
						$arResult["PostToShow"]["CategoryText"] = substr($arResult["PostToShow"]["CategoryText"], 0, strlen($arResult["PostToShow"]["CategoryText"])-2);
					}
					
					$arResult["POST_PROPERTIES"] = array("SHOW" => "N");
		
					if (!empty($arParams["POST_PROPERTY"]))
					{
						$arPostFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("BLOG_POST", $arParams["ID"], LANGUAGE_ID);
		
						if (count($arParams["POST_PROPERTY"]) > 0)
						{
							foreach ($arPostFields as $FIELD_NAME => $arPostField)
							{
								if (!in_array($FIELD_NAME, $arParams["POST_PROPERTY"]))
									continue;
								$arPostField["EDIT_FORM_LABEL"] = strLen($arPostField["EDIT_FORM_LABEL"]) > 0 ? $arPostField["EDIT_FORM_LABEL"] : $arPostField["FIELD_NAME"];
								$arPostField["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arPostField["EDIT_FORM_LABEL"]);
								$arPostField["~EDIT_FORM_LABEL"] = $arPostField["EDIT_FORM_LABEL"];
								$arResult["POST_PROPERTIES"]["DATA"][$FIELD_NAME] = $arPostField;
							}
						}
						if (!empty($arResult["POST_PROPERTIES"]["DATA"]))
							$arResult["POST_PROPERTIES"]["SHOW"] = "Y";
					}
					$arResult["CUR_PAGE"] = urlencode($APPLICATION->GetCurPageParam());
					
					$serverName = "";
					$dbSite = CSite::GetByID(SITE_ID);
					$arSite = $dbSite->Fetch();
					$serverName = $arSite["SERVER_NAME"];
					if (strLen($serverName) <=0)
					{
						if (defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME)>0)
							$serverName = SITE_SERVER_NAME;
						else
							$serverName = COption::GetOptionString("main", "server_name", "www.bitrixsoft.com");
						if (strLen($serverName) <=0)
							$serverName = $_SERVER["HTTP_HOST"];
					}
					$serverName = "http://".$serverName;

					$arResult["PATH_TO_POST"] = CComponentEngine::MakePathFromTemplate(htmlspecialcharsBack($arParams["PATH_TO_POST"]), array("blog" => $arBlog["URL"], "post_id" => "#post_id#", "user_id" => $arBlog["OWNER_ID"], "group_id" => $arParams["SOCNET_GROUP_ID"]));
					$arResult["PATH_TO_POST1"] = $serverName.substr($arResult["PATH_TO_POST"], 0, strpos($arResult["PATH_TO_POST"], "#post_id#"));
					$arResult["PATH_TO_POST2"] = substr($arResult["PATH_TO_POST"], strpos($arResult["PATH_TO_POST"], "#post_id#") + strlen("#post_id#"));

					if($arResult["preview"] == "Y")
					{
						if(check_bitrix_sessid())
						{
							$arResult["postPreview"]["TITLE"] = $arResult["PostToShow"]["TITLE"];
							$arResult["postPreview"]["CATEGORY_ID"] = $arResult["PostToShow"]["CATEGORY_ID"];
							$arResult["postPreview"]["DETAIL_TEXT"] = (($_POST["POST_MESSAGE_TYPE"] == "html")? $_POST["POST_MESSAGE_HTML"] : ($_POST["POST_MESSAGE"]));
							$arResult["postPreview"]["POST_MESSAGE_TYPE"] = htmlspecialcharsEx($_POST["POST_MESSAGE_TYPE"]);
							$arResult["postPreview"]["DATE_PUBLISH"] = $arResult["PostToShow"]["DATE_PUBLISH"];
							$arResult["postPreview"]["DATE_PUBLISH_FORMATED"] = FormatDate($arParams["DATE_TIME_FORMAT"], MakeTimeStamp($arResult["postPreview"]["DATE_PUBLISH"], CSite::GetDateFormat("FULL")));
							$arResult["postPreview"]["DATE_PUBLISH_DATE"] = ConvertDateTime($arResult["postPreview"]["DATE_PUBLISH"], FORMAT_DATE);
							$arResult["postPreview"]["DATE_PUBLISH_TIME"] = ConvertDateTime($arResult["postPreview"]["DATE_PUBLISH"], "HH:MI");
							$arResult["postPreview"]["DATE_PUBLISH_D"] = ConvertDateTime($arResult["postPreview"]["DATE_PUBLISH"], "DD");
							$arResult["postPreview"]["DATE_PUBLISH_M"] = ConvertDateTime($arResult["postPreview"]["DATE_PUBLISH"], "MM");
							$arResult["postPreview"]["DATE_PUBLISH_Y"] = ConvertDateTime($arResult["postPreview"]["DATE_PUBLISH"], "YYYY");
							$arResult["postPreview"]["FAVORITE_SORT"] = htmlspecialcharsEx($arResult["FAVORITE_SORT"]);
							if($_POST["POST_MESSAGE_TYPE"] == "html" && strlen($_POST["POST_MESSAGE_HTML"]) <= 0)
							{
								$arResult["postPreview"]["DETAIL_TEXT"] = htmlspecialcharsEx($_POST["POST_MESSAGE"]);
								$arResult["postPreview"]["~DETAIL_TEXT"] = $_POST["POST_MESSAGE"];
							}
							
							if (!empty($_POST["CATEGORY_ID"]))
							{
								foreach($_POST["CATEGORY_ID"] as $v)
								{
									
									if(substr($v, 0, 4) == "new_")
										$arResult["Category"][$v] = Array("ID" => $v, "NAME" => substr($v, 4), "Selected" => "Y");
								}
							}

							$p = new blogTextParser(false, $arParams["PATH_TO_SMILE"]);
							$arParserParams = Array(
								"imageWidth" => $arParams["IMAGE_MAX_WIDTH"],
								"imageHeight" => $arParams["IMAGE_MAX_HEIGHT"],
							);

							$res = CBlogImage::GetList(array("ID"=>"ASC"),array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arBlog['ID'], "IS_COMMENT" => "N"));
							while ($arImage = $res->Fetch())
								$arImages[$arImage['ID']] = $arImage['FILE_ID'];
							
							if($arResult["postPreview"]["POST_MESSAGE_TYPE"] == "html" && $arResult["allowHTML"] == "Y")
							{								
								$arAllow = array("HTML" => "Y", "ANCHOR" => "Y", "IMG" => "Y", "SMILES" => "Y", "NL2BR" => "N", "VIDEO" => "Y", "QUOTE" => "Y", "CODE" => "Y");
								if($arResult["allowVideo"] != "Y")
									$arAllow["VIDEO"] = "N";

								$arResult["postPreview"]["textFormated"] = $p->convert($arResult["postPreview"]["~DETAIL_TEXT"], false, $arImages, $arAllow, $arParserParams);
							}
							else
							{
								$arAllow = array("HTML" => "N", "ANCHOR" => "Y", "BIU" => "Y", "IMG" => "Y", "QUOTE" => "Y", "CODE" => "Y", "FONT" => "Y", "LIST" => "Y", "SMILES" => "Y", "NL2BR" => "N", "VIDEO" => "Y");
								if($arResult["allowVideo"] != "Y")
									$arAllow["VIDEO"] = "N";
								$arResult["postPreview"]["textFormated"] = $p->convert($arResult["postPreview"]["DETAIL_TEXT"], false, $arImages, $arAllow, $arParserParams);
							}
							$arResult["postPreview"]["BlogUser"] = CBlogUser::GetByID($arResult["UserID"], BLOG_BY_USER_ID); 
							$arResult["postPreview"]["BlogUser"] = CBlogTools::htmlspecialcharsExArray($arResult["postPreview"]["BlogUser"]);
							$dbUser = CUser::GetByID($arResult["UserID"]);
							$arResult["postPreview"]["arUser"] = $dbUser->GetNext();
							$arResult["postPreview"]["AuthorName"] = CBlogUser::GetUserName($arResult["postPreview"]["BlogUser"]["ALIAS"], $arResult["postPreview"]["arUser"]["NAME"], $arResult["postPreview"]["arUser"]["LAST_NAME"], $arResult["postPreview"]["arUser"]["LOGIN"]);
							
							$arResult["postPreview"]["BlogUser"]["AVATAR_file"] = CFile::GetFileArray($arResult["postPreview"]["BlogUser"]["AVATAR"]);
							if ($arResult["postPreview"]["BlogUser"]["AVATAR_file"] !== false)
							{
								$arResult["postPreview"]["BlogUser"]["Avatar_resized"] = CFile::ResizeImageGet(
										$arResult["postPreview"]["BlogUser"]["AVATAR_file"],
										array("width" => 100, "height" => 100),
										BX_RESIZE_IMAGE_EXACT,
										false
									);

								$arResult["postPreview"]["BlogUser"]["AVATAR_img"] = CFile::ShowImage($arResult["postPreview"]["BlogUser"]["Avatar_resized"]["src"], 100, 100, "border=0 align='right'");
							}

							if(strlen($arResult["PostToShow"]["CategoryText"]) > 0)
							{
								$arCatTmp = explode(",", $arResult["PostToShow"]["CategoryText"]);
								if(is_array($arCatTmp))
								{
									foreach($arCatTmp as $v)
										$arResult["postPreview"]["Category"][] = Array("NAME" => htmlspecialchars(trim($v)));
								}
							}
							elseif(strlen($arResult["postPreview"]["CATEGORY_ID"])>0)
							{
								foreach($arResult["postPreview"]["CATEGORY_ID"] as $v)
								{
									if(strlen($v)>0)
									{
										$arResult["postPreview"]["Category"][] = $arResult["Category"][$v];
									}
								}
							}
						}
						else
							$arResult["preview"] = "N";
					}
				}
			}
		}
		else
			$arResult["FATAL_MESSAGE"] = GetMessage("BLOG_ERR_NO_RIGHTS");
	}
	else
	{
		$arResult["FATAL_MESSAGE"] = GetMessage("B_B_MES_NO_BLOG");
		CHTTP::SetStatus("404 Not Found");
	}
}
else
{
	$arResult["FATAL_MESSAGE"] = GetMessage("B_B_MES_NO_BLOG");
	CHTTP::SetStatus("404 Not Found");
}
	
$this->IncludeComponentTemplate();
?>