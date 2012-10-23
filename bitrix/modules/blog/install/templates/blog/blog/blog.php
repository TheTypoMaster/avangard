<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile("blog/blog/blog.php", 
	Array(
		"BLOG_URL"=>$arFolders[0],
		"MESSAGE_COUNT"=>10,
		"SORT_BY1"=>"DATE_PUBLISH",
		"SORT_ORDER1"=>"DESC",
		"SORT_BY2"=>"ID",
		"SORT_ORDER2"=>"DESC",
		"EDIT_PAGE" => "post_edit.php",
		"MONTH" => $MONTH,
		"YEAR" => $YEAR,
		"DAY" => $DAY,
		"CATEGORY" => $category,
		"CACHE_TIME_LONG"=>0,
		"CACHE_TIME_SHORT"=>0,
	)
);
*/
if (CModule::IncludeModule("blog"))
{
	$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", Trim($BLOG_URL));
	$MESSAGE_COUNT = (intval($MESSAGE_COUNT)>0) ? IntVal($MESSAGE_COUNT) : "20";
	$SORT_BY1 = (isset($SORT_BY1) ? $SORT_BY1 : "DATE_PUBLISH");
	$SORT_ORDER1 = (isset($SORT_ORDER1) ? $SORT_ORDER1 : "DESC");
	$SORT_BY2 = (isset($SORT_BY2) ? $SORT_BY2 : "ID");
	$SORT_ORDER2 = (isset($SORT_ORDER2) ? $SORT_ORDER2 : "DESC");
	$SORT = Array($SORT_BY1=>$SORT_ORDER1, $SORT_BY2=>$SORT_ORDER2);
	$editPage = (strlen($EDIT_PAGE)>0) ? $EDIT_PAGE : "post_edit.php";
	$year = (IntVal($YEAR)>0 ? IntVal($YEAR) : false);
	$month = (IntVal($MONTH)>0 ? IntVal($MONTH) : false);
	$day = (IntVal($DAY)>0 ? IntVal($DAY) : false);
	$category = (IntVal($CATEGORY)>0 ? IntVal($CATEGORY) : false);
	$CACHE_TIME_SHORT = intval($CACHE_TIME_SHORT);
	$CACHE_TIME_LONG = intval($CACHE_TIME_LONG);
	$is404 = ($is404=='N') ? false: true;

	$arSelectFields = Array("ID", "NAME", "DESCRIPTION", "URL", "DATE_CREATE", "DATE_UPDATE", "ACTIVE", "OWNER_ID", "OWNER_NAME", "LAST_POST_DATE", "LAST_POST_ID", "BLOG_USER_AVATAR", "BLOG_USER_ALIAS");

	CpageOption::SetOptionString("main", "nav_page_in_session", "N");
	$APPLICATION->SetTitle(GetMessage("BLOG_BLOG_BLOG_TITLE"));
	
	$dbBlog = CBlog::GetList(
		Array(),
		Array("URL"=>$BLOG_URL),
		false,
		Array("nTopCount" => 1),
		$arSelectedFields
	);
	if($arBlog = $dbBlog->Fetch())
	{
		$user_id = $USER->GetID();
		$APPLICATION->SetTitle($arBlog["NAME"]);
		$title = $arBlog["NAME"]." - ";
		$APPLICATION->AddChainItem($arBlog["NAME"], CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404));
		$PostPerm = CBlog::GetBlogUserPostPerms($arBlog["ID"], $user_id);

		//Заявка на чтение блога
		if($_GET["become_friend"]=="Y"/* && $PostPerm<BLOG_PERMS_READ*/)
		{
			if($USER->IsAuthorized())
			{
				$dbCandidate = CBlogCandidate::GetList(Array(), Array("BLOG_ID"=>$arBlog["ID"], "USER_ID"=>$user_id));
				if($arCandidate = $dbCandidate->Fetch())
				{
					echo '<font class="text">'.GetMessage("BLOG_BLOG_BLOG_REQUEST_ALREADY").'</font>';
				}
				else
				{
					if(CBlogCandidate::Add(Array("BLOG_ID"=>$arBlog["ID"], "USER_ID"=>$user_id)))
						echo '<font class="text">'.GetMessage("BLOG_BLOG_BLOG_REQUEST_ADDED").'</font>';
					else
						echo ShowError(GetMessage('BLOG_BLOG_BLOG_REQUEST_ERROR'));
				}
			}
			else
				echo '<font class="text">'.GetMessage("BLOG_BLOG_BLOG_REQUEST_NEED_AUTH").'</font>';
		}
		elseif($_GET["become_friend"]=="N")
		{
			if($USER->IsAuthorized())
			{
				CBlogUser::DeleteFromUserGroup($user_id, $arBlog["ID"], BLOG_BY_USER_ID);

				$dbCandidate = CBlogCandidate::GetList(
					array(),
					array("BLOG_ID" => $arBlog["ID"], "USER_ID" => $user_id)
				);
				if ($arCandidate = $dbCandidate->Fetch())
					CBlogCandidate::Delete($arCandidate["ID"]);

				echo '<font class="text">'.GetMessage("BLOG_BLOG_BLOG_LEAVED").'</font>';
			}
		}

		if($PostPerm>=BLOG_PERMS_READ)
		{
			$arFilter = Array(
				"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
				">PERMS" => "D",
				"BLOG_ID" => $arBlog["ID"],
			);	
			if($year && $month && $day)
			{
				$from = mktime(0, 0, 0, $month, $day, $year);
				$to = mktime(0, 0, 0, $month, ($day+1), $year);
				if($to>time())
					$to = time();
				$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($from, "FULL");
				$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($to, "FULL");
			}
			elseif($year && $month)
			{
				$from = mktime(0, 0, 0, $month, 1, $year);
				$to = mktime(0, 0, 0, ($month+1), 1, $year);
				if($to>time())
					$to = time();
				$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($from, "FULL");
				$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($to, "FULL");
			}
			elseif($year)
			{
				$from = mktime(0, 0, 0, 1, 1, $year);
				$to = mktime(0, 0, 0, 1, 1, ($year+1));
				if($to>time())
					$to = time();
				$arFilter[">=DATE_PUBLISH"] = ConvertTimeStamp($from, "FULL");
				$arFilter["<DATE_PUBLISH"] = ConvertTimeStamp($to, "FULL");
			}
			else
				$arFilter["<=DATE_PUBLISH"] = ConvertTimeStamp(false, "FULL"); 
			if($category)
				$arFilter["CATEGORY_ID"] = $category;
				
			if(isset($arFilter[">=DATE_PUBLISH"]))
			{
				$title .= GetMessage("BLOG_BLOG_BLOG_MES_FOR");
				if($year && $month && $day)
					$title .= ConvertTimeStamp(mktime(0, 0, 0, $month, $day, $year));
				elseif($year && $month)
					$title .= GetMessage("BLOG_BLOG_BLOG_M_".$month)." ".$year." ".GetMessage("BLOG_BLOG_BLOG_MES_YEAR");
				elseif($year)
					$title .= $year." ".GetMessage("BLOG_BLOG_BLOG_MES_YEAR_ONE");
				$APPLICATION->SetTitle($title);
			}
			if(isset($arFilter["CATEGORY_ID"]))
			{
				$title .= GetMessage("BLOG_BLOG_BLOG_MES_CAT").' "';
				$arCat = CBlogCategory::GetByID($arFilter["CATEGORY_ID"]);
				$title .= htmlspecialcharsex($arCat["NAME"]).'"';
				$APPLICATION->SetTitle($title);
			}
			
			//Удаление сообщения
			$errorMessage = "";
			$okMessage = "";
			if (IntVal($_GET["del_id"]) > 0)
			{
				if ($_GET["sessid"] == bitrix_sessid() && CBlogPost::CanUserDeletePost(IntVal($_GET["del_id"]), ($USER->IsAuthorized() ? $user_id : 0 )))
				{
					$DEL_ID = IntVal($_GET["del_id"]);
					if (CBlogPost::Delete($DEL_ID))
					{
						$okMessage = GetMessage("BLOG_BLOG_BLOG_MES_DELED");
						$Blog = CBlog::GetByID($BLOG_ID);
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/first_page/");
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/calendar/");
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/post/".$DEL_ID."/");
						BXClearCache(True, "/".SITE_ID."/blog/last_messages/");
						BXClearCache(True, "/".SITE_ID."/blog/groups/".$arBlog["GROUP_ID"]."/");
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/trackback/".$DEL_ID."/");
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/rss_out/");
					}
					else
						$errorMessage = GetMessage("BLOG_BLOG_BLOG_MES_DEL_ERROR");
				}
				else
					$errorMessage = GetMessage("BLOG_BLOG_BLOG_MES_DEL_NO_RIGHTS");
			}

			if (StrLen($errorMessage) > 0)
				echo "<div class=\"blogError\">".$errorMessage."</div>";
			if (StrLen($okMessage) > 0)
				echo "<div class=\"blogOK\">".$okMessage."</div>";			
			
			//формируем кэш
			$arUserGroups = CBlogUser::GetUserGroups(($GLOBALS["USER"]->IsAuthorized() ? $user_id : 0), $arBlog["ID"], "Y", BLOG_BY_USER_ID);
			$numUserGroups = count($arUserGroups);
			for ($i = 0; $i < $numUserGroups - 1; $i++)
			{
				for ($j = $i + 1; $j < $numUserGroups; $j++)
				{
					if ($arUserGroups[$i] > $arUserGroups[$j])
					{
						$tmpGroup = $arUserGroups[$i];
						$arUserGroups[$i] = $arUserGroups[$j];
						$arUserGroups[$j] = $tmpGroup;
					}
				}
			}

			$strUserGroups = "";
			for ($i = 0; $i < $numUserGroups; $i++)
				$strUserGroups .= "_".$arUserGroups[$i];

			if(!isset($_GET["PAGEN_1"]) || IntVal($_GET["PAGEN_1"])<1)
			{
				$CACHE_TIME = $CACHE_TIME_SHORT;
				$cache_path = "/".SITE_ID."/blog/".$BLOG_URL."/first_page/";
			}
			else
			{
				$CACHE_TIME = $CACHE_TIME_LONG;
				$cache_path = "/".SITE_ID."/blog/".$BLOG_URL."/pages/".IntVal($_GET["PAGEN_1"])."/";
			}
			
			$cache = new CPHPCache;
			$cache_id = "blog_blog_message_".serialize($arParams)."_".CDBResult::NavStringForCache($MESSAGE_COUNT)."_".$strUserGroups;

			if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
			{
				$cache->Output();
			}
			else
			{
				if ($CACHE_TIME > 0)
					$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

				//вывод сообщения
				$dbPost = CBlogPost::GetList(
					$SORT,
					$arFilter,
					array(
						"ID", "DATE_PUBLISH", "MAX" => "PERMS"
					),
					array("bDescPageNumbering"=>true, "nPageSize"=>$MESSAGE_COUNT, "bShowAll" => false)
				);

				if($arPost = $dbPost->NavNext(false))
				{
					do
					{
						$CurPost = CBlogPost::GetByID($arPost["ID"]);
						$urtToPost = CBlogPost::PreparePath($arBlog["URL"], $CurPost["ID"], SITE_ID, $is404);
						$urlToAuthor = CBlogUser::PreparePath($CurPost["AUTHOR_ID"], SITE_ID, $is404);
						$urlToBlog = CBlog::PreparePath($arBlog["URL"], SITE_ID, $is404);
						
						$p = new blogTextParser();
						$arImage = array();
						$res = CBlogImage::GetList(array("ID"=>"ASC"),array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arBlog['ID']));
						while ($arImage = $res->Fetch())
							$arImages[$arImage['ID']] = $arImage['FILE_ID'];
						$text = $p->convert($CurPost["DETAIL_TEXT"], true, $arImages);
						
						$BlogUser = CBlogUser::GetByID($CurPost["AUTHOR_ID"], BLOG_BY_USER_ID); 
						$dbUser = CUser::GetByID($CurPost["AUTHOR_ID"]);
						$arUser = $dbUser->Fetch();
						$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
						?>
						<table class="blogtableborder" cellspacing="1" cellpadding="0" width="100%" border="0">
						<tr>
						<td>
							<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td class="blogtablehead" align="left" nowrap width="70%" style="padding-left:10px;"><font class="blogpostdate"><?=$CurPost["DATE_PUBLISH"]?></font></td>
								<td align="right" class="blogtablehead" nowrap width="30%"><font class="blogauthor"><?=GetMessage("BLOG_BLOG_BLOG_AUTHOR")?> <a href="<?=$urlToAuthor?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urlToBlog?>"><?=htmlspecialcharsex($AuthorName)?></a></font></td>
								<?if($PostPerm>=BLOG_PERMS_MODERATE):?>
									<?
									if($is404)
										$urlToEdit = CBlog::PreparePath($BLOG_URL).$editPage."?ID=".$CurPost["ID"];
									else
										$urlToEdit = $editPage."?blog=".htmlspecialcharsex($arBlog["URL"])."&post_id=".$CurPost["ID"];
									?>
									<td align="right" nowrap class="blogtablehead" valign="center" width="10%" style="padding-right:5px;">
										<a href="<?=$urlToEdit?>"><img src="/bitrix/templates/.default/blog/images/edit_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_EDIT_MES")?>"></a><img src="/bitrix/images/1.gif" width="5" height="1" border="0"><a href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$APPLICATION->GetCurPageParam("del_id=".$CurPost["ID"].'&'.bitrix_sessid_get(), Array("del_id", "sessid"))?>'"><img src="/bitrix/templates/.default/blog/images/delete_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_DEL_MES")?>"></a>
									</td>
								<?endif;?>
							</tr>
							<tr>
								<td colspan="3" style="padding-left:10px; padding-right:10px; padding-top:5px; padding-bottom:5px;"><h2><a href="<?=$urtToPost?>"><?=htmlspecialcharsex($CurPost["TITLE"])?></a></h2><div style="padding-top:15px;"><span class="blogtext"><?=$text?></span><?
								if (preg_match("/(\[CUT\])/i",$CurPost['DETAIL_TEXT']))
									print "<br><div align=\"right\" class=\"blogpostdate\"><a href=\"$urtToPost\">".GetMessage("BLOG_BLOG_BLOG_MORE")."</a></div>";
								?></div>
									<table width="100%" cellspacing="0" cellpadding="0" border="0" class="blogpostdate">
									<tr>
										<td colspan="2" style="padding-top:8px;padding-bottom:5px;"><div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div></td>
									</tr>
									<tr>
										<td align="left" nowrap style="padding-right:5px;">						
											<?if(IntVal($CurPost["CATEGORY_ID"])>0)
											{
												$arCategory = CBlogCategory::GetByID($CurPost["CATEGORY_ID"]);
												if($is404)
													$urlToCat = $urlToBlog."?category=".$CurPost["CATEGORY_ID"];
												else
													$urlToCat = $urlToBlog."&category=".$CurPost["CATEGORY_ID"];
												?>
												<?=GetMessage("BLOG_BLOG_BLOG_CATEGORY")?>&nbsp;<a href="<?=$urtToCat?>"><?=htmlspecialcharsex($arCategory["NAME"])?></a>
												<?
											}?></td>
										<td align="right" nowrap><a href="<?=$urtToPost?>"><?=GetMessage("BLOG_BLOG_BLOG_PERMALINK")?></a>&nbsp;|&nbsp;<a href="<?=$urtToPost?>#comment"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?> <?=$CurPost["NUM_COMMENTS"];?></a></td>
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
					while($arPost = $dbPost->NavNext(false));
					echo '<p align="center">';
					$dbPost->NavPrint(GetMessage("BLOG_BLOG_BLOG_NAV"), false, "text", "/bitrix/modules/blog/install/templates/blog/blog/nav_chain_template.php");
					echo '</p>';
				}
				else
					echo '<p class="blogtext">'.GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES").'</p>';

				if ($CACHE_TIME > 0)
					$cache->EndDataCache(array());
			}
		}
		elseif($_GET["become_friend"]!="Y")
		{
			echo '<font class="blogtext">'.GetMessage("BLOG_BLOG_BLOG_FRIENDS_ONLY").' </font>';
			if($USER->IsAuthorized())
				echo '<font class="blogtext">'.GetMessage("BLOG_BLOG_BLOG_U_CAN").' <a href="'.$APPLICATION->GetCurPage().'?become_friend=Y">'.GetMessage("BLOG_BLOG_BLOG_U_CAN1").'</a> '.GetMessage("BLOG_BLOG_BLOG_U_CAN2").' </font>';
			else
				echo '<font class="blogtext">'.GetMessage("BLOG_BLOG_BLOG_NEED_AUTH").' </font>';
		}
	}
	else
		echo ShowError(GetMessage("BLOG_BLOG_BLOG_NO_BLOG"));
}
else
	echo ShowError(GetMessage("BLOG_BLOG_BLOG_NO_MODULE"));?>
