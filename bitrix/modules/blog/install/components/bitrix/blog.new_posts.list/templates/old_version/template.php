<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(count($arResult["POSTS"])>0)
{
	foreach($arResult["POSTS"] as $CurPost)
	{
		?>
		<table class="blog-table-post">
		<tr>
			<th width="100%">
				<table class="blog-table-post-table">
				<tr>
					<td width="100%" align="left">
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
						<?
						if(!empty($CurPost["CATEGORY"]))
						{
							echo GetMessage("BLOG_BLOG_BLOG_CATEGORY");
							$i=0;
							foreach($CurPost["CATEGORY"] as $v)
							{
								if($i!=0)
									echo ",";
								?> <a href="<?=$v["urlToCategory"]?>"><?=$v["NAME"]?></a><?
								$i++;
							}
						}
						?></td>
					<td align="right" nowrap><a href="<?=$CurPost["urlToPost"]?>"><?=GetMessage("BLOG_BLOG_BLOG_PERMALINK")?></a>&nbsp;|&nbsp;
					<?if($arResult["enable_trackback"] == "Y" && $CurPost["ENABLE_TRACKBACK"]=="Y"):?>
						<a href="<?=$CurPost["urlToPost"]?>#trackback">Trackbacks: <?=$CurPost["NUM_TRACKBACKS"];?></a>&nbsp;|&nbsp;
					<?endif;?>
					<a href="<?=$CurPost["urlToPost"]?>"><?=GetMessage("BLOG_BLOG_BLOG_VIEWS")?> <?=IntVal($CurPost["VIEWS"]);?></a>&nbsp;|&nbsp;
					<a href="<?=$CurPost["urlToPost"]?>#comment"><?=GetMessage("BLOG_BLOG_BLOG_COMMENTS")?> <?=$CurPost["NUM_COMMENTS"];?></a></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<br />
		<?
	}
	if(strlen($arResult["NAV_STRING"])>0)
		echo $arResult["NAV_STRING"];
}
?>	
