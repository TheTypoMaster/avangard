<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult as $arBlog)
{
	if($arBlog["FIRST_BLOG"] != "Y")
	{
		?>
		<div class="blog-line"></div>
		<?
	}
	if(strlen($arBlog["AuthorName"]) > 0)
	{
		?>
		<span class="blog-author">
			<a href="<?=$arBlog["urlToAuthor"]?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>" class="blog-user-grey"></a>&nbsp;<a href="<?=$arBlog["urlToBlog"]?>" title="<?=GetMessage("BLOG_BLOG_M_TITLE_BLOG")?>"><?=$arBlog["AuthorName"]?></a>
		</span>
		<br clear="all"/>
		<?
	}
	?>
	<span class="blog-post-date"><b><a href="<?=$arBlog["urlToBlog"]?>"><?=$arBlog["NAME"]?></a></b></span>
	<small>
		<?if($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arBlog["DESCRIPTION"]) > 0)
		{
			?>
			<br />
			<?=$arBlog["DESCRIPTION"];?>
			<br />
			<?
		}
		?>
	</small>
	<?
}
?>

