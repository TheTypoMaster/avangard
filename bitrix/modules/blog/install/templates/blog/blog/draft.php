<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("blog")):
/*
$APPLICATION->IncludeFile(
	"blog/blog/draft.php", 
	Array(
		"BLOG_URL" => $arFolders[0]
	)
);
*/
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
$editPage = "post_edit.php";
$is404 = ($is404=='N') ? false : true;

if (StrLen($BLOG_URL) > 0)
{
	$APPLICATION->SetTitle(GetMessage('B_B_DRAFT_TITLE'));
	$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME"));
	if ($arBlog = $dbBlog->Fetch())
	{
		$APPLICATION->SetTitle(str_replace("#NAME#", $arBlog["NAME"], GetMessage("B_B_DRAFT_TITLE_BLOG")));
		$APPLICATION->AddChainItem($arBlog["NAME"], CBlog::PreparePath($arBlog["URL"]));
		if (CBlog::GetBlogUserPostPerms($arBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )) >= BLOG_PERMS_WRITE)
		{
			$errorMessage = "";
			$okMessage = "";
			if (IntVal($_GET["del_id"]) > 0)
			{
				if ($_GET["sessid"] == bitrix_sessid() && CBlogPost::CanUserDeletePost(IntVal($_GET["del_id"]), ($USER->IsAuthorized() ? $USER->GetID() : 0 )))
				{
					if (CBlogPost::Delete($_GET["del_id"]))
						$okMessage = GetMessage("B_B_DRAFT_M_DEL");
					else
						$errorMessage = GetMessage("B_B_DRAFT_M_DEL_ERR");
				}
				else
					$errorMessage = GetMessage("B_B_DRAFT_M_DEL_RIGHTS");
			}

			if (StrLen($errorMessage) > 0)
				echo "<div class=\"blogError\">".$errorMessage."</div>";
			if (StrLen($okMessage) > 0)
				echo "<div class=\"blogOK\">".$okMessage."</div>";

			$bPostsFound = False;
			$parser = new blogTextParser();
			$dbPosts = CBlogPost::GetList(
				array("DATE_CREATE" => "DESC"),
				array(
					"BLOG_ID" => $arBlog["ID"],
					"AUTHOR_ID" => $GLOBALS["USER"]->GetID(),
					"!PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH
				)
			);
			$urtToBlog = CBlog::PreparePath($BLOG_URL);
			while ($arPost = $dbPosts->Fetch())
			{
				$bPostsFound = True;
				$p = new blogTextParser();
						
				$res = CBlogImage::GetList(array("ID"=>"ASC"),array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arBlog['ID']));
				while ($arImage = $res->Fetch())
					$arImages[$arImage['ID']] = $arImage['FILE_ID'];
	
				$text = $p->convert($arPost["DETAIL_TEXT"], false, $arImages);
				?>
					<table width="100%" border="0" class="blogtableborder" cellspacing="1" cellpadding="0">
					<tr>
					<td>
						<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td align="left" nowrap class="blogtablehead" style="padding-left:10px;"><span class="blogpostdate"><?=$arPost["DATE_PUBLISH"]?></span></td>
								<?
								if($is404)
								{
									$urlToEdit = CBlog::PreparePath($BLOG_URL).$editPage."?ID=".$arPost["ID"];
									$pathToDel = CBlog::PreparePath($BLOG_URL, false, $is404)."?";
								}
								else
								{
									$urlToEdit = $editPage."?blog=".htmlspecialcharsex($BLOG_URL)."&post_id=".$arPost["ID"];
									$pathToDel = CBlog::PreparePath($BLOG_URL, false, $is404)."&";
								}
								?>
								
								<td align="right" nowrap class="blogtablehead" valign="center" width="10%">
										<a href="<?=$urlToEdit?>"><img src="/bitrix/templates/.default/blog/images/edit_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_EDIT_MES")?>"></a><img src="/bitrix/images/1.gif" width="5" height="1" border="0"><a href="javascript:if(confirm('<?=GetMessage("BLOG_MES_DELETE_POST_CONFIRM")?>')) window.location='<?=$pathToDel?>del_id=<?= $arPost["ID"]?>&<?=bitrix_sessid_get()?>'"><img src="/bitrix/templates/.default/blog/images/delete_button.gif" width="18" height="18" border="0" title="<?=GetMessage("BLOG_BLOG_BLOG_DEL_MES")?>"></a>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="padding:10px"><h2><?=htmlspecialcharsex($arPost["TITLE"])?></h2><br><font class="blogtext"><?=$text?>&nbsp;<a href="<?=$urtToPost?>"><?=GetMessage("BLOG_BLOG_BLOG_MORE")?></a>
								<?
								if(IntVal($arPost["CATEGORY_ID"])>0)
								{
									$arCategory = CBlogCategory::GetByID($arPost["CATEGORY_ID"]);
									?>
									<br><br><i><?=GetMessage("BLOG_BLOG_BLOG_CATEGORY")?></i> <a href="<?=$urtToBlog?>?category=<?=$arPost["CATEGORY_ID"]?>"><?=htmlspecialcharsex($arCategory["NAME"])?></a>
									<?
								}
								?></font>
								</td>
							</tr>
						</table>
					</td>
					</tr>
					</table><br />
				
				
				
				
				
			<?
			}

			if (!$bPostsFound)
			{
				?><div class="blogMessage"><?=GetMessage("B_B_DRAFT_NO_MES")?></div><?
			}
		}
		else
		{
			?><div class="blogError"><?=GetMessage("B_B_DRAFT_NO_R_CR")?></div><?
		}
	}
	else
	{
		?><div class="blogError"><?=GetMessage("B_B_DRAFT_NO_BLOG")?></div><?
	}
}
//*******************************************************
endif;
?>
