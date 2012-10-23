<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/friends.php", 
	array(
		"ID" => IntVal($arFolders[2])
	)
);
*/
if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$ID = IntVal($ID);
$is404 = ($is404=='N') ? false : true;

$APPLICATION->SetTitle(GetMessage("B_B_FR_TITLE"));
if ($ID > 0)
{
	$arBlogUser = CBlogUser::GetByID($ID, BLOG_BY_USER_ID);
	if ($arBlogUser)
	{
		if ($GLOBALS["USER"]->IsAuthorized()
			&& $GLOBALS["USER"]->GetID() == $arBlogUser["USER_ID"])
		{
			$APPLICATION->SetTitle(GetMessage("B_B_FR_TITLES"));
		}
		else
		{
			$dbUser = CUser::GetByID($arBlogUser["USER_ID"]);
			$arUser = $dbUser->Fetch();

			$APPLICATION->SetTitle(str_replace("#NAME#", CBlogUser::GetUserName($arBlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]), GetMessage("B_B_FR_TITLE_OF")));
		}

		$dbList = CBlogUser::GetUserFriendsList($ID, $GLOBALS["USER"]->GetID(), $GLOBALS["USER"]->IsAuthorized(), 20);
		if ($arList = $dbList->Fetch())
		{
			do
			{

				$arPost = CBlogPost::GetByID($arList["ID"]);
				$arBlog = CBlog::GetByID($arPost["BLOG_ID"]);
				$urlToPost = CBlogPost::PreparePath($arBlog["URL"], $arPost["ID"], false, $is404);
				$urlToAuthor = CBlogUser::PreparePath($arPost["AUTHOR_ID"], SITE_ID, $is404);
				$urlToBlog = CBlog::PreparePath($arBlog["URL"], false, $is404);
				
				$p = new blogTextParser();

				$arImage = array();
				$dbImage = CBlogImage::GetList(
					array("ID" => "ASC"),
					array("POST_ID" => $arPost["ID"], "BLOG_ID" => $arPost["BLOG_ID"])
				);
				while ($arImage = $dbImage->Fetch())
					$arImages[$arImage['ID']] = $arImage['FILE_ID'];

				$postText = $p->convert(
					$arPost["DETAIL_TEXT"],
					true,
					$arImages
				);

				$arBlogUser = CBlogUser::GetByID($arPost["AUTHOR_ID"], BLOG_BY_USER_ID);
				$dbUser = CUser::GetByID($arPost["AUTHOR_ID"]);
				$arUser = $dbUser->Fetch();
				$authorName = CBlogUser::GetUserName($arBlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
				?>
				<table class="blogtableborder" cellspacing="1" cellpadding="0" width="100%" border="0">
						<tr>
						<td>
							<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td class="blogtablehead" align="left" nowrap width="70%" style="padding-left:10px;"><font class="blogpostdate"><?=$arPost["DATE_PUBLISH"]?></font></td>
								<td align="right" class="blogtablehead" nowrap width="30%"><font class="blogauthor"><?=GetMessage("BLOG_BLOG_BLOG_AUTHOR")?> <a href="<?=$urlToAuthor?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urlToBlog?>"><?=htmlspecialcharsex($authorName)?></a></font></td>
							</tr>
							<tr>
								<td colspan="3" style="padding-left:10px; padding-right:10px; padding-top:5px; padding-bottom:5px;"><h2><a href="<?=$urtToPost?>"><?=htmlspecialcharsex($arPost["TITLE"])?></a></h2><div style="padding-top:15px;"><span class="blogtext"><?=$postText?></span><?
								if (preg_match("/(<cut>)|([cut])|(<CUT>)|([CUT])/i",$arPost['DETAIL_TEXT']))
									print "<br><div align=\"right\" class=\"blogpostdate\"><a href=\"
									".$urlToPost."\">".GetMessage("BLOG_BLOG_BLOG_MORE")."</a></div>";
								?></div>
									<table width="100%" cellspacing="0" cellpadding="0" border="0" class="blogpostdate">
									<tr>
										<td colspan="2" style="padding-top:8px;padding-bottom:5px;"><div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div></td>
									</tr>
									<tr>
										<td align="left" nowrap style="padding-right:5px;">						
											<?if(IntVal($arPost["CATEGORY_ID"])>0)
											{
												$arCategory = CBlogCategory::GetByID($arPost["CATEGORY_ID"]);
												?>
												<?=GetMessage("BLOG_BLOG_BLOG_CATEGORY")?>&nbsp;<a href="<?=$urtToBlog?>?category=<?=$arPost["CATEGORY_ID"]?>"><?=htmlspecialcharsex($arCategory["NAME"])?></a>
												<?
											}?></td>
										<td align="right" nowrap><a href="<?=$urlToPost?>"><?=GetMessage("BLOG_BLOG_BLOG_PERMALINK")?></a>&nbsp;|&nbsp;<a href="<?=$urlToPost?>#comment"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?> <?=$arPost["NUM_COMMENTS"];?></a></td>
									</tr>
									</table>
								</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
						<br />

				<?
			}
			while ($arList = $dbList->Fetch());
			
			echo '<p align="center">';
			$dbList->NavPrint(GetMessage("BLOG_BLOG_BLOG_NAV"), false, "text", "/bitrix/modules/blog/install/templates/blog/blog/nav_chain_template.php");
			echo '</p>';
		}
		else
			echo '<p class="text">'.GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES").'</p>';

	}
	else
	{
		?><div class="blogError"><?=GetMessage("B_B_FR_NO_USER")?></div><?
	}
}
else
{
	?><div class="blogError"><?=GetMessage("B_B_FR_NO_USER")?></div><?
}

//*******************************************************
endif;
?>