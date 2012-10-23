<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("blog"))
{
	$CACHE_TIME = IntVal($CACHE_TIME);
	$COUNT = (intval($BLOGS_COUNT)>0) ? Array("nTopCount" => intval($BLOGS_COUNT)) : false;
	
	$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "DATE_CREATE");
	$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
	$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "ID");
	$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "DESC");
	$show_description = ($SHOW_DESCRIPTION=="Y") ? true : false;
	$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
	$is404 = ($is404=='N') ? false: true;

	//формируем кэш
	$cache = new CPHPCache;
	$cache_id = "blog_new_blogs_".serialize($arParams);
	$cache_path = "/".SITE_ID."/blog/new_blogs/";

	if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
	{
		$cache->Output();
	}
	else
	{
		if ($CACHE_TIME > 0)
			$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

		$arFilter = Array(
					"ACTIVE" => "Y",
					"GROUP_SITE_ID"=>SITE_ID
				);	
		$arSelectedFields = array("ID", "NAME", "DESCRIPTION", "URL", "OWNER_ID");

		?>
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
			<?
			$dbBlogs = CBlog::GetList(
				$SORT,
				$arFilter,
				false,
				$COUNT,
				$arSelectedFields
			);
			$i=0;
			while ($arBlog = $dbBlogs->Fetch())
			{
				$urlToBlog = CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404);
				
				$arBlogUser = CBlogUser::GetByID($arBlog["OWNER_ID"], BLOG_BY_USER_ID);
				$dbUser = CUser::GetByID($arBlog["OWNER_ID"]);

				$arUser = $dbUser->Fetch();
				$AuthorName = CBlogUser::GetUserName($arBlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
				$urlToAuthor = CBlogUser::PreparePath($arBlog["OWNER_ID"], SITE_ID, $is404);
				
				if($i<>0)
					echo '<tr><td align="center"><div style="height:1px; width:100%; overflow:hidden; background-color:#C7D2D5;"></td></tr>';
				$i++;
				?>
					<tr>

						
						<td align="left" style="padding-top:10px;"><span class="blogauthor"><a href="<?=$urlToAuthor?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urlToBlog?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>"><?=htmlspecialcharsex($AuthorName)?></a></span></td>
					</tr>
					<tr>
						<td align="left"><font class="blogpostdate"><b><a href="<?=$urlToBlog?>"><?= htmlspecialcharsex($arBlog["NAME"]) ?></a></font></b></td>					

					</tr>
					<tr>
						<td valign="top" style="padding-bottom:10px;"><span class="blogtextsm">
							<?if(strlen($arBlog["DESCRIPTION"])>0)
								echo htmlspecialcharsex($arBlog["DESCRIPTION"]);
							else
								echo "<img src='/bitrix/images/1.gif' width='1' height='1' border='0'";?>
							</span></td>
					</tr>
				<!--<div style="padding:2px"><img src="/bitrix/images/1.gif" height="1" width="1" border="0"></div>!-->
				<?
			}
			?>
			</table>

		<?
		if ($CACHE_TIME > 0)
			$cache->EndDataCache(array());
	}
}
else
	echo ShowError(GetMessage("BLOG_MAIN_NBLOGS_NOT_INSTALL"));?>