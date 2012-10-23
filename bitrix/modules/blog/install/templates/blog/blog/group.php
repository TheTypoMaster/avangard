<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/group.php", 
	array(
		"ID"=>IntVal($arFolders[1]),
		"BLOGS_COUNT"=>10,
		"SORT_BY1"=>"LAST_POST_DATE",
		"SORT_ORDER1"=>"DESC",
		"SORT_BY2"=>"NAME",
		"SORT_ORDER2"=>"ASC",
		"CACHE_TIME"=>0,
	)
);
*/
if (CModule::IncludeModule("blog"))
{
	$ID = IntVal($ID);
	$BLOGS_COUNT = (intval($BLOGS_COUNT)>0) ? IntVal($BLOGS_COUNT) : 20;
	$COUNT = array("nPageSize"=>intval($BLOGS_COUNT), "bShowAll" => false);
	$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "LAST_POST_DATE");
	$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
	$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "NAME");
	$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "ASC");
	$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
	$CACHE_TIME = intval($CACHE_TIME);
	$is404 = ($is404=='N') ? false: true;

	$arFilter = Array("SITE_ID"=>SITE_ID, "GROUP_ID"=>$ID, "ACTIVE"=>"Y");
	$arSelectFields = Array("ID", "NAME", "DESCRIPTION", "URL", "SITE_ID", "DATE_CREATE", "DATE_UPDATE", "ACTIVE", "OWNER_ID", "OWNER_LOGIN", "OWNER_NAME", "OWNER_LAST_NAME", "LAST_POST_DATE", "LAST_POST_ID", "BLOG_USER_AVATAR", "BLOG_USER_ALIAS", );

	if($arGroup = CBlogGroup::GetByID($ID))
	{
		$APPLICATION->SetTitle($arGroup["NAME"]);
		//формируем кэш
		$cache = new CPHPCache;
		$cache_id = "blog_groups_".serialize($arParams)."_".CDBResult::NavStringForCache($BLOGS_COUNT);
				
		$cache_path = "/".SITE_ID."/blog/groups/".$ID."/";

		if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
		{
			$cache->Output();
		}
		else
		{
			if ($CACHE_TIME > 0)
				$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);
				
				$dbBlogs = CBlog::GetList(
						$SORT,
						$arFilter,
						false,
						$COUNT,
						$arSelectFields
					);
					if($arBlogs = $dbBlogs->NavNext(false))
					{
						//$dbBlogs->NavPrint(GetMessage("B_B_GR_TITLE"));
						?><?
						do
						{
							$urlToBlog = CBlog::PreparePath($arBlogs["URL"], SITE_ID, $is404);
							$urlToPost = CBlogPost::PreparePath($arBlogs["URL"], $arBlogs["LAST_POST_ID"], SITE_ID, $is404);
							$urlToAuthor = CBlogUser::PreparePath($arBlogs["OWNER_ID"], SITE_ID, $is404);
							$AuthorName = CBlogUser::GetUserName($arBlogs["BLOG_USER_ALIAS"], $arBlogs["OWNER_NAME"], $arBlogs["OWNER_LAST_NAME"], $arBlogs["OWNER_LOGIN"]);
							
						?><table class="blogtableborder" cellspacing="1" cellpadding="0" width="100%" border="0">
						<tr>
						<td>
							<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td class="blogtablehead" align="left" nowrap width="70%" style="padding-left:10px;"><font class="blogpostdate"><a href="<?=$urlToBlog?>"><?=htmlspecialcharsex($arBlogs["NAME"])?></a><br></font></font></td>
								<td class="blogtablehead" align="right" nowrap width="30%" style="padding-right:10px;"><font class="blogauthor"><?=GetMessage("BLOG_BLOG_BLOG_AUTHOR")?> <a href="<?=$urlToAuthor?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urlToBlog?>"><?=htmlspecialcharsex($AuthorName)?></a></font>
								</td>
								
							</tr>
<tr>
								<td colspan="2" style="padding-left:10px; padding-right:10px; padding-top:5px; padding-bottom:5px;"><?=CFile::ShowImage($arBlogs["BLOG_USER_AVATAR"], 100, 100, 'align="right"')?><span class="blogtext"><?=htmlspecialcharsex($arBlogs["DESCRIPTION"])?></span>
								</td>
							</tr>
						<?if(IntVal($arBlogs["LAST_POST_ID"])>0):?>
							<tr>
								<td colspan="2" style="padding-left:10px; padding-top:5px; padding-bottom:5px; padding-right:10px;">
								<div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div>
							
								<div class="blogpostdate" style="padding-top:5px;"><?=GetMessage("B_B_GR_LAST_M")?> <a href="<?=$urlToPost?>"><?=$arBlogs["LAST_POST_DATE"]?></a><span>
							</td>
							</tr>
<?endif;?>
							</table>
						</td>
						</tr>
						</table><br />
						<?
						}
						while($arBlogs = $dbBlogs->NavNext(false));
						?>
						<?
						$dbBlogs->NavPrint(GetMessage("B_B_GR_TITLE"));
					}
					else
						echo ShowError(GetMessage("B_B_GR_NO_BLOGS"));
			if ($CACHE_TIME > 0)
				$cache->EndDataCache(array());
		}
	}
	else 
		echo ShowError(GetMessage("B_B_GR_NO_GROUP"));
}
else
	echo ShowError(GetMessage("B_B_GR_NO_MODULE"));?>