<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	?>
	<span class='errortext'><?=$arResult["ERROR_MESSAGE"]?></span><br /><br />
	<?
}
if(strlen($arResult["FATAL_MESSAGE"])>0)
{
	?>
	<span class='errortext'><?=$arResult["FATAL_MESSAGE"]?></span><br /><br />
	<?
}
else
{
	if(count($arResult["FRIENDS_POSTS"])>0)
	{
		foreach($arResult["FRIENDS_POSTS"] as $arPost)
		{
			$CurPost = $arPost["POST"];
			?>
			<table class="blog-table-post">
			<tr>
				<th nowrap width="100%">
					<table width="100%" cellspacing="2" cellpadding="0" border="0" class="blog-table-post-table">
					<tr>
						<td width="100%">
							<span class="blog-post-date"><b><?=$CurPost["DATE_PUBLISH_FORMATED"]?></b></span><br />
							<span class="blog-author"><b><a href="<?=$CurPost["urlToAuthor"]?>" class="blog-user"></a>&nbsp;<a href="<?=$CurPost["urlToBlog"]?>"><?=$CurPost["AuthorName"]?></a>:&nbsp;<a href="<?=$CurPost["urlToPost"]?>"><?=$CurPost["TITLE"]?></a></b></span>
						</td>
					</tr>
					</table>
				</th>
			</tr>
			<tr>
				<td>
					<span class="blog-text"><?=$CurPost["TEXT_FORMATED"]?></span><?
					if ($CurPost["CUT"] == "Y")
					{
						?><br /><br /><div align="left" class="blog-post-date"><a href="<?=$CurPost["urlToPost"]?>"><?=GetMessage("BLOG_BLOG_BLOG_MORE")?></a></div><?
					}
					?>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="blog-table-post-table">
					<tr>
						<td colspan="2"><div class="blog-line"></div></td>
					</tr>
					<tr>
						<td align="left">
							<?if(IntVal($CurPost["CATEGORY_ID"])>0)
							{
								echo GetMessage("BLOG_BLOG_BLOG_CATEGORY");
								$i=0;
								foreach($CurPost["Category"] as $v)
								{
									if($i!=0)
										echo ",";
									?> <a href="<?=$v["urlToCategory"]?>"><?=$v["NAME"]?></a><?
									$i++;
								}
							}
							?></td>
						<td align="right" nowrap><a href="<?=$CurPost["urlToPost"]?>"><?=GetMessage("BLOG_BLOG_BLOG_PERMALINK")?></a>&nbsp;|&nbsp;
						<?if($CurPost["ENABLE_TRACKBACK"]=="Y"):?>
							<a href="<?=$CurPost["urlToPost"]?>#trackback">Trackbacks: <?=$CurPost["NUM_TRACKBACKS"];?></a>&nbsp;|&nbsp;
						<?endif;?>
						<a href="<?=$CurPost["urlToPost"]?>#comment"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?> <?=$CurPost["NUM_COMMENTS"];?></a></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			<br />
			<?
		}
	}
	else
		echo GetMessage("BLOG_BLOG_BLOG_NO_AVAIBLE_MES");
}
?>	