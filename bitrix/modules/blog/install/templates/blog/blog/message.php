<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/* 
$APPLICATION->IncludeFile(
	"blog/blog/message.php", 
	Array(
		"ID"=>IntVal($arFolders[1]),
		"TBLenght" => 0,
		"CACHE_TIME"=>0,
	)
); 
*/

if (CModule::IncludeModule("blog"))
{
	$CACHE_TIME = intval($CACHE_TIME);
	$ID = IntVal($ID);
	$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", Trim($BLOG_URL));
	$TBLenght = (IntVal($TBLenght)>0) ? IntVal($TBLenght) : false;
	$editPage = (strlen($EDIT_PAGE)>0) ? $EDIT_PAGE : "post_edit.php";
	$is404 = ($is404=='N') ? false: true;
	
	$USER_ID = $USER->GetID();
	$PostPerm = CBlogPost::GetBlogUserPostPerms($ID, $USER_ID);

	$arPost = CBlogPost::GetByID($ID);
	$arBlog = CBlog::GetByID($arPost["BLOG_ID"]);
	$dbBlog = CBlog::GetList(
			Array(),
			Array("URL"=>$BLOG_URL),
			false,
			Array("nTopCount" => 1)
		);
	if(($arBlogUrl = $dbBlog->Fetch()) ||  strlen($BLOG_URL)<=0)
	{
		$APPLICATION->SetTitle($arPost["TITLE"]);
		$APPLICATION->AddChainItem($arBlogUrl["NAME"], CBlog::PreparePath($arBlogUrl["URL"]));

		//Заявка на чтение блога
		if($_GET["become_friend"]=="Y"  && $PostPerm<BLOG_PERMS_READ)
		{
			if($USER->IsAuthorized())
			{
				$dbCandidate = CBlogCandidate::GetList(Array(), Array("BLOG_ID"=>$arBlog["ID"], "USER_ID"=>$USER_ID));
				if($arCandidate = $dbCandidate->Fetch())
				{
					echo '<font class="text">'.GetMessage("B_B_MES_REQUEST_ALREADY").'</font>';
				}
				else
				{
					if(CBlogCandidate::Add(Array("BLOG_ID"=>$arBlog["ID"], "USER_ID"=>$USER_ID)))
						echo '<font class="text">'.GetMessage("B_B_MES_REQUEST_ADDED").'</font>';
					else
						echo ShowError(GetMessage("B_B_MES_REQUEST_ERROR"));
				}
			}
			else
				echo '<font class="text">'.GetMessage("B_B_MES_REQUEST_AUTH").'</font>';
		}
		
		if($PostPerm > BLOG_PERMS_DENY)
		{
			if(!empty($arPost) && ($arBlogUrl["ID"] == $arBlog["ID"] ||  strlen($BLOG_URL)<=0))
			{
				if($arPost["PUBLISH_STATUS"]=="P" || $PostPerm==BLOG_PERMS_FULL || $arPost["AUTHOR_ID"]==$USER_ID)
				{
					$cache = new CPHPCache;
					$cache_id = "blog_message_".serialize($arParams)."_".$PostPerm;
					$cache_path = "/".SITE_ID."/blog/".$arBlog["URL"]."/post/".$arPost["ID"]."/";

					if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
					{
						$cache->Output();
					}
					else
					{
						if ($CACHE_TIME > 0)
							$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);
						
						$urtToPost = CBlogPost::PreparePath($arBlog["URL"], $arPost["ID"], false, $is404);
						$urtToBlog = CBlog::PreparePath($arBlog["URL"], false, $is404);
						$urlToAuthor = CBlogUser::PreparePath($arPost["AUTHOR_ID"], false, $is404);
						
						$p = new blogTextParser();
						
						$res = CBlogImage::GetList(array("ID"=>"ASC"),array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arBlog['ID']));
						while ($arImage = $res->Fetch())
							$arImages[$arImage['ID']] = $arImage['FILE_ID'];
			
						$text = $p->convert($arPost["DETAIL_TEXT"], false, $arImages);
						
						$BlogUser = CBlogUser::GetByID($arPost["AUTHOR_ID"], BLOG_BY_USER_ID); 
						$dbUser = CUser::GetByID($arPost["AUTHOR_ID"]);
						$arUser = $dbUser->Fetch();
						$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
						?>
						<table class="blogtableborder" cellspacing="1" cellpadding="0" width="100%" border="0">
						<tr>
						<td>
							<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td class="blogtablehead" align="left" nowrap width="100%" style="padding-left:10px;"><font class="blogpostdate"><?=$arPost["DATE_PUBLISH"]?></font></td>
								<td align="right" class="blogtablehead" nowrap style="padding-right:10px;"><font class="blogauthor"><a href="<?=$urlToAuthor?>"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="<?=$urtToBlog?>"><?=htmlspecialcharsex($AuthorName)?></a></font></td>
								<?if($PostPerm>=BLOG_PERMS_MODERATE):?>
									<?
									if($is404)
									{
										$urlToEdit = CBlog::PreparePath($arBlog["URL"]).$editPage."?ID=".$arPost["ID"];
										$pathToDel = CBlog::PreparePath($arBlog["URL"], false, $is404)."?";
									}
									else
									{
										$urlToEdit = $editPage."?blog=".htmlspecialcharsex($arBlog["URL"])."&post_id=".$arPost["ID"];
										$pathToDel = CBlog::PreparePath($arBlog["URL"], false, $is404)."&";
									}
									?>
									<td align="right" nowrap class="blogtablehead" valign="center" style="padding-right:10px;">
										<a href="<?=$urlToEdit?>"><img src="/bitrix/templates/.default/blog/images/edit_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_EDIT_MES")?>"></a><img src="/bitrix/images/1.gif" width="5" height="1" border="0"><a href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$pathToDel?>del_id=<?= $arPost["ID"]?>&<?=bitrix_sessid_get()?>'"><img src="/bitrix/templates/.default/blog/images/delete_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_DEL_MES")?>"></a>
									</td>
								<?endif;?>
							</tr>
						<tr>
								<td colspan="3" style="padding-left:10px; padding-right:10px; padding-top:5px; padding-bottom:5px;"><font class="blogtext"><?=CFile::ShowImage($BlogUser["AVATAR"], 100, 100, "align='right'")?><?=$text?>
								<?if(IntVal($arPost["ATTACH_IMG"])>0)
									echo CFile::Show2Images($arPost["ATTACH_IMG"], $arPost["ATTACH_IMG"], 300, 300, 'align="right" title="'.GetMessage("B_B_MES_FULL_SIZE").'" border="0"');?></font><br clear="all">
								<?if(IntVal($arPost["CATEGORY_ID"])>0)
								{?>

									<table width="100%" cellspacing="0" cellpadding="0" border="0" class="blogpostdate">
									<tr>
										<td style="padding-top:8px;padding-bottom:5px;"><div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div></td>
									</tr>
									<tr>
										<td align="left">						
											<?	$arCategory = CBlogCategory::GetByID($arPost["CATEGORY_ID"]);
												?>
												<?=GetMessage("B_B_MES_CAT")?>&nbsp;<a href="<?=$urtToBlog?>?category=<?=$arPost["CATEGORY_ID"]?>"><?=htmlspecialcharsex($arCategory["NAME"])?></a>
										</td>
									</tr>
									</table>
								<?
								}?>
								</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
						
						
						
						<?
						if ($CACHE_TIME > 0)
							$cache->EndDataCache(array());
					}

					if($arPost["ENABLE_TRACKBACK"]=="Y")
					{				
						$cache = new CPHPCache;
						$cache_id = "blog_trackback_".serialize($arParams)."_".$PostPerm;
						$cache_path = "/".SITE_ID."/blog/".$arBlog["URL"]."/trackback/".$arPost["ID"]."/";
						$arurlToBlogs = CBlogSitePath::GetBySiteID(SITE_ID);
						$urlToBlogs = $arurlToBlogs["PATH"];


						if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
						{
							$cache->Output();
						}
						else
						{
							if ($CACHE_TIME > 0)
								$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

							$dbTrack = CBlogTrackback::GetList(Array("POST_DATE" => "DESC"), Array("BLOG_ID"=>$arPost["BLOG_ID"], "POST_ID"=>$arPost["ID"]));
							?>
							<p class="blogtext"><?=GetMessage("B_B_MES_TBA")?>&nbsp;<a href="<?=$urlToBlogs?>/trackback.php/<?=$arBlog["URL"]?>/<?=$arPost["ID"]?>">http://<?=$_SERVER["SERVER_NAME"]?><?=$urlToBlogs?>/trackback.php/<?=$arBlog["URL"]?>/<?=$arPost["ID"]?></a></p>
								<?if($arTrack = $dbTrack->Fetch())
								{?><span class="blogtext">
									<b>Trackbacks:</b></span>
									<table width="100%" cellpadding="0" cellspacing="0" border="0">						
									<?
										do
										{
											echo '<tr><td><div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div></td></tr>';
											echo '<tr><td class="blogpostdate">';;
											echo '<b><a href="'.htmlspecialcharsex($arTrack["URL"]).'">'.htmlspecialcharsex($arTrack["BLOG_NAME"]).'</a>:</b>';
											echo '&nbsp;'.$arTrack["POST_DATE"].'<br>';
											echo '<a href="'.htmlspecialcharsex($arTrack["URL"]).'">'.htmlspecialcharsex($arTrack["TITLE"]).'</a></td></tr>';
											echo '<tr><td class="blogtext">';
											if($TBLenght)
												echo htmlspecialcharsex(TruncateText($arTrack["PREVIEW_TEXT"], $TBLenght));
											else
												echo htmlspecialcharsex($arTrack["PREVIEW_TEXT"]);
											echo '<br><br></td></tr>';
										}
										while($arTrack = $dbTrack->Fetch())
									?></table><?
								}
								?>
							<?
							if ($CACHE_TIME > 0)
								$cache->EndDataCache(array());
						}
					}
					?>
					<?
					$APPLICATION->IncludeFile(
						"blog/blog/message_comment.php", 
						Array(
							"ID"=>$ID,
							"OWNER"=>$arPost["AUTHOR_ID"],
							"BLOG_ID" => $arPost["BLOG_ID"],
							"CACHE_TIME"=>0,
						)
					);
				}
				else
					echo ShowError(GetMessage("B_B_MES_NO_RIGHTS"));
			}
			else
				echo ShowError(GetMessage("B_B_MES_NO_MES"));

		}
		elseif($_GET["become_friend"]!="Y")
		{
			echo '<font class="text">'.GetMessage("B_B_MES_FR_ONLY").' </font>';
			if($USER->IsAuthorized())
				echo '<font class="text">'.GetMessage("B_B_MES_U_CAN").' <a href="'.$APPLICATION->GetCurPage().'?become_friend=Y">'.GetMessage("B_B_MES_U_CAN1").'</a> '.GetMessage("B_B_MES_U_CAN2").' </font>';
			else
				echo '<font class="text">'.GetMessage("B_B_MES_U_AUTH").' </font>';
		}
	}
	else
		echo ShowError(GetMessage("B_B_MES_NO_BLOG"));
}
else
	echo ShowError(GetMessage("B_B_MES_NO_MODULE"));?>
