<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("blog"))
{
	$CACHE_TIME = IntVal($CACHE_TIME);
	$COUNT = (intval($MESSAGES_COUNT)>0) ? Array("nTopCount" => intval($MESSAGES_COUNT)*3) : false;
	$MESSAGES_CNT = (intval($MESSAGES_COUNT)>0) ? intval($MESSAGES_COUNT) 
										: 6;
	$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "DATE_PUBLISH");
	$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
	$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "ID");
	$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "DESC");
	$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
	$is404 = ($is404=='N') ? false: true;
	$message_lenght = (IntVal($message_lenght)>0)?$message_lenght:100;
	
	//формируем кэш
	$UserGroupID = $USER->IsAuthorized() ? array(1, 2) : array(1);
	$cache = new CPHPCache;
	$cache_id = "blog_last_messages_".serialize($arParams)."_".serialize($UserGroupID);
	$cache_path = "/".SITE_ID."/blog/last_messages/";

	if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
	{
		$cache->Output();
	}
	else
	{
		if ($CACHE_TIME > 0)
			$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);
	
		$arFilter = Array(
				"<=DATE_PUBLISH" => ConvertTimeStamp(false, "FULL", false),
				"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
				//">PERMS" => "D"
			);	
		$arSelectedFields = array("ID", "BLOG_ID", "TITLE", "DATE_PUBLISH", "AUTHOR_ID", "DETAIL_TEXT");
		?>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">

			<?
			$dbPosts = CBlogPost::GetList(
				$SORT,
				$arFilter,
				false,
				$COUNT,
				$arSelectedFields
			);

			$itemCnt = 0;
			$PrevTime = false;
			$i=0;
			while ($arPost = $dbPosts->Fetch())
			{
				$bCanRead = false;
				if(is_array($UserGroupID))
				{
					foreach($UserGroupID as $GroupI)
					{
						$perms = CBlogUserGroup::GetGroupPerms($GroupI, $arPost["BLOG_ID"], $arPost["ID"]);
						if($perms >= BLOG_PERMS_READ)
							$bCanRead = true;
					}
				}
				else
				{
					$perms = CBlogUserGroup::GetGroupPerms($UserGroupID, $arPost["BLOG_ID"], $arPost["ID"]);
					if($perms >= BLOG_PERMS_READ)
						$bCanRead = true;
				}
				if ($bCanRead)
				{
					$arTime = Array();
					$CurTime = "";
					$arBlog = CBlog::GetByID($arPost["BLOG_ID"]);
					$arBlogGroup = CBlogGroup::GetByID($arBlog["GROUP_ID"]);
					if($arBlogGroup["SITE_ID"] == SITE_ID)
					{
						$urlToBlog = CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404);
						$urlToPost = CBlogPost::PreparePath($arBlog["URL"], $arPost["ID"], SITE_ID, $is404);
						$urlToAuthor = CBlogUser::PreparePath($arPost["AUTHOR_ID"], false, $is404);
						
						$BlogUser = CBlogUser::GetByID($arPost["AUTHOR_ID"], BLOG_BY_USER_ID); 
						$dbUser = CUser::GetByID($arPost["AUTHOR_ID"]);
						$arUser = $dbUser->Fetch();
						$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
						/*
						$arTime = ParseDateTime($arPost["DATE_PUBLISH"]);
						if((IntVal($arTime["MM"])!=IntVal($PrevTime["MM"]) ||
							IntVal($arTime["DD"])!=IntVal($PrevTime["DD"]) ||
							IntVal($arTime["YYYY"])!=IntVal($PrevTime["YYYY"])) || 
							$PrevTime===false)
						{
							echo '<tr><td class="tablebodytext" style="padding-left:15px;"><b>';
							echo ConvertTimeStamp(mktime(0,0,0,$arTime["MM"],$arTime["DD"],$arTime["YYYY"]), "SHORT");
							echo '</td></tr>';
						}
						*/
						$CurTime = $arTime["HH"].":".$arTime["MI"].":".$arTime["SS"];
						$PrevTime = $arTime;
						if($i<>0)
							echo '<tr><td align="center"><div style="height:1px; width:100%; overflow:hidden; background-color:#C7D2D5;"></td></tr>';
						$i++;
						?>
							
							<tr>
								<td align="left" style="padding-top:10px;"><span class="blogauthor"><a href="<?=$urlToAuthor?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urlToBlog?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>"><?=htmlspecialcharsex($AuthorName)?></a></span></td>
							</tr>
							<tr>
								<td align="left"><font class="blogpostdate"><?=$arPost["DATE_PUBLISH"]?></font></td>
								
							<tr>
								<td valign="top" style="padding-bottom:10px;">
									<span class="blogpostdate"><b><a href="<?=$urlToPost?>"><?
									if(strlen($arPost["TITLE"])>0) 
										echo htmlspecialcharsex($arPost["TITLE"]); 
									else 
										echo GetMessage("BLOG_MAIN_MES_NO_SUBJECT"); 
									?></a></b></span><br>
									<?
									$text = TruncateText(htmlspecialcharsex(HTMLToTxt(preg_replace(array('/\[(.*?)\]/', '/\<(.*?)\>/'), Array('', ''), $arPost["DETAIL_TEXT"]))),$message_lenght);
									if(strlen($text)>0):?>
										<span class="blogtextsm"><?=$text?></span><br>
									<?endif;?>
									</td>
							</tr>
						<!--<div style="padding:2px"><img src="/bitrix/images/1.gif" height="1" width="1" border="0"></div>!-->
						<?
						$itemCnt++;
						if ($itemCnt >= $MESSAGES_CNT)
							break;
					}
				}
			}
			?>	</table>
		<?	
		if ($CACHE_TIME > 0)
			$cache->EndDataCache(array());
	}
}
else
	echo ShowError(GetMessage("BLOG_MAIN_MES_NOT_INSTALL"));?>