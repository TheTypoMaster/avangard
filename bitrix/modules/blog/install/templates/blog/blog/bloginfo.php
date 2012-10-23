<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile("blog/blog/bloginfo.php", 
	Array(
		"URL"=>$arFolders[0],
		"CATEGORY" => $category,
		"CACHE_TIME"=>0,
	)
);
*/
if (CModule::IncludeModule("blog"))
{
	$is404 = ($is404=='N') ? false: true;
	$CACHE_TIME = intval($CACHE_TIME);
	$category = IntVal($CATEGORY);
	$arSelectFields = Array("ID", "NAME", "URL", "ACTIVE", "OWNER_ID");

	$dbBlog = CBlog::GetList(
		Array(),
		Array("URL"=>$URL, "ACTIVE" => "Y"),
		false,
		Array("nTopCount" => 1),
		$arSelectedFields
	);
	if($arBlog = $dbBlog->Fetch())
	{
		echo '<div align="left" style="padding-left:25px;">';
		echo '<p><font class="blogtext"><b><a href="'.CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404).'">';
		echo htmlspecialcharsex($arBlog["NAME"]);
		echo '</a></b></font><br><br>';
		$arUserBlog = CBlogUser::GetByID($arBlog["OWNER_ID"], BLOG_BY_USER_ID);
		$dbUser = CUser::GetByID($arBlog["OWNER_ID"]);
		$arUser = $dbUser->Fetch();
		$AuthorName = CBlogUser::GetUserName($arUserBlog["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
		echo '<a href="'.CBlogUser::PreparePath($arBlog["OWNER_ID"], SITE_ID, $is404).'">';
		echo CFile::ShowImage($arUserBlog["AVATAR"], 100, 100, 'title="'.$AuthorName.'" border="0"');
		echo '</a></p>';

		if ($GLOBALS["USER"]->IsAuthorized())
		{
			$arMyBlog = CBlog::GetByOwnerID($GLOBALS["USER"]->GetID());
			if ($arMyBlog && $arMyBlog["ID"] != $arBlog["ID"])
			{
				echo "<p><font class=\"blogtext\">";

				$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
				$strPath = $arPath["PATH"];

				if (CBlog::IsFriend($arBlog["ID"], $GLOBALS["USER"]->GetID()))
					echo "<a href=\"".CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404).($is404?"?":"&")."become_friend=N\" title=\"".GetMessage("BLOG_BLOG_BLOGINFO_IO11")."\">".GetMessage("BLOG_BLOG_BLOGINFO_IO1")."</a><br>";
				else
					echo "<a href=\"".CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404).($is404?"?":"&")."become_friend=Y\" title=\"".GetMessage("BLOG_BLOG_BLOGINFO_IO21")."\">".GetMessage("BLOG_BLOG_BLOGINFO_IO2")."</a><br>";

				if (CBlog::IsFriend($arMyBlog["ID"], $arBlog["OWNER_ID"]))
					echo "<a href=\"".CBlog::PreparePath($arMyBlog["URL"], SITE_ID, $is404)."user_settings.php?del_id=".$arBlog["OWNER_ID"]."\" title=\"".GetMessage("BLOG_BLOG_BLOGINFO_IO31")."\">".GetMessage("BLOG_BLOG_BLOGINFO_IO3")."</a><br>";
				else
					echo "<a href=\"".$strPath."/add_friends.php?BLOG_URL=".UrlEncode($arMyBlog["URL"])."&add_friend[]=".UrlEncode($arBlog["URL"])."\" title=\"".GetMessage("BLOG_BLOG_BLOGINFO_IO41")."\">".GetMessage("BLOG_BLOG_BLOGINFO_IO4")."</a><br>";

				echo "</font></p>";
			}
		}

		//формируем кэш
		$cache = new CPHPCache;
		$cache_id = "blog_blog_category"."_".$category;
		$cache_path = "/".SITE_ID."/blog/".$arBlog["URL"]."/category/";

		if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
		{
			$cache->Output();
		}
		else
		{
			if ($CACHE_TIME > 0)
				$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

			$dbCategory = CBlogCategory::GetList(Array("NAME"=>"ASC"), Array("BLOG_ID"=>$arBlog["ID"]));
			if($arCategory = $dbCategory->Fetch())
			{
				echo '<p align="left"><font class="blogtext">';
				echo '<b>'.GetMessage("BLOG_BLOG_BLOGINFO_CAT").'</b><br>';
				do
				{
					if($category == $arCategory["ID"])
						echo "<b>";
					if($is404)
						echo '<a href="'.CBlog::PreparePath($arBlog["URL"]).'?category='.$arCategory["ID"].'" title="'.GetMessage("BLOG_BLOG_BLOGINFO_CAT_VIEW").'">';
					else
						echo '<a href="'.CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404).'&category='.$arCategory["ID"].'" title="'.GetMessage("BLOG_BLOG_BLOGINFO_CAT_VIEW").'">';
					echo htmlspecialcharsex($arCategory["NAME"]);
					echo '</a>';
					if($category == $arCategory["ID"])
						echo "</b>";
					echo "<br>";
				}
				while($arCategory = $dbCategory->Fetch());
				echo '</font><p>';
			}
	
			if ($CACHE_TIME > 0)
				$cache->EndDataCache(array());
		}
		echo '</div>';
	}
}
else
	echo ShowError(GetMessage("BLOG_BLOG_BLOGINFO_NO_MODULE"));?>