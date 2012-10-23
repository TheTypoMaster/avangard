<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FATAL_ERROR"])>0)
{
	?>
	<span class="errortext"><?=$arResult["FATAL_ERROR"]?></span><br />
	<?
}
else
{
	if(count($arResult["BLOG"])>0)
	{
		foreach($arResult["BLOG"] as $arBlog)
		{
			if(IntVal($arBlog["LAST_POST_ID"])>0 || $arParams["SHOW_BLOG_WITHOUT_POSTS"] == "Y")
			{
				?>
				<table class="blog-table-post">
				<tr>
					<th nowrap width="100%" align="left">
						<span class="blog-post-date"><b><a href="<?=$arBlog["urlToBlog"]?>"><?=$arBlog["NAME"]?></a></b></span>
					</th>
				</tr>
				<tr>
					<td>
						<?if(IntVal($arBlog["OWNER_ID"]) > 0)
						{
							?>
							<?=$arBlog["BLOG_USER_AVATAR_IMG"]?>
							<span class="blog-author"><b><a href="<?=$arBlog["urlToAuthor"]?>" class="blog-user"></a>&nbsp;<a href="<?=$arBlog["urlToBlog"]?>"><?=$arBlog["AuthorName"]?></a></b></span><br clear="all" />
							<?
						}
						?>
						<?if(strlen($arBlog["DESCRIPTION"])>0):?>
							<span class="blog-text"><?=$arBlog["DESCRIPTION"]?></span>
							<br />
						<?endif;?>
						<?if(IntVal($arBlog["LAST_POST_ID"])>0):?>
							<div class="blog-line"></div>
							<?=GetMessage("B_B_GR_LAST_M")?> <a href="<?=$arBlog["urlToPost"]?>"><?=$arBlog["LAST_POST_DATE_FORMATED"]?></a>
						<?endif;?>
					</td>
				</tr>
				</table>
				<br />
				<?
			}
		}
		if(strlen($arResult["NAV_STRING"])>0)
			echo $arResult["NAV_STRING"];
	}
	else
		echo GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES");
}
?>	