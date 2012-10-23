<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
<!--
function BXBlogTabShow(id, type)
{
	if(type == 'post')
	{
		document.getElementById('new_posts').style.display = 'none';
		document.getElementById('popular_posts').style.display = 'none';
		document.getElementById('commented_posts').style.display = 'none';
		document.getElementById('new_posts_title').className = 'blog-tab';
		document.getElementById('popular_posts_title').className = 'blog-tab';
		document.getElementById('commented_posts_title').className = 'blog-tab';

		document.getElementById(id).style.display = 'block';
		document.getElementById(id+'_title').className = 'blog-tab-selected';
	}
	else if(type == 'blog')
	{
		document.getElementById('new_blogs').style.display = 'none';
		document.getElementById('popular_blogs').style.display = 'none';
		document.getElementById('new_blogs_title').className = 'blog-tab';
		document.getElementById('popular_blogs_title').className = 'blog-tab';

		document.getElementById(id).style.display = 'block';
		document.getElementById(id+'_title').className = 'blog-tab-selected';
	}
	
}
//-->
</script>
<table width="100%" style="font-size:100%;">
<tr>
	<td width="50%" valign="top">
	<h4><?=GetMessage("BC_MESSAGES")?></h4>
	<a id="new_posts_title" class="blog-tab-selected" href="#" onclick="BXBlogTabShow('new_posts', 'post'); return false;"><?=GetMessage("BC_NEW_POSTS")?></a><a id="commented_posts_title" class="blog-tab" href="#" onclick="BXBlogTabShow('commented_posts', 'post'); return false;"><?=GetMessage("BC_COMMENTED_POSTS")?></a><a id="popular_posts_title" class="blog-tab" href="#" onclick="BXBlogTabShow('popular_posts', 'post'); return false;"><?=GetMessage("BC_POPULAR_POSTS")?></a>
<br /><br />
	<div id="new_posts" style="display:block;">
		<?
		$APPLICATION->IncludeComponent("bitrix:blog.new_posts", ".default", Array(
			"MESSAGE_COUNT"		=> $arParams["MESSAGE_COUNT_MAIN"],
			"MESSAGE_LENGTH"	=>	$arParams["MESSAGE_LENGTH"],
			"PATH_TO_BLOG"		=>	$arParams["PATH_TO_BLOG"],
			"PATH_TO_POST"		=>	$arParams["PATH_TO_POST"],
			"PATH_TO_GROUP_BLOG_POST"		=>	$arParams["PATH_TO_GROUP_BLOG_POST"],
			"PATH_TO_USER"		=>	$arParams["PATH_TO_USER"],
			"PATH_TO_SMILE"		=>	$arParams["PATH_TO_SMILE"],
			"CACHE_TYPE"		=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"		=>	$arParams["CACHE_TIME"],
			"BLOG_VAR"			=>	$arParams["VARIABLE_ALIASES"]["blog"],
			"POST_VAR"			=>	$arParams["VARIABLE_ALIASES"]["post_id"],
			"USER_VAR"			=>	$arParams["VARIABLE_ALIASES"]["user_id"],
			"PAGE_VAR"			=>	$arParams["VARIABLE_ALIASES"]["page"],
			"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
			"GROUP_ID" 			=> $arParams["GROUP_ID"],
			),
			$component 
		);
		?>
<?
		if(strlen($arResult["PATH_TO_HISTORY"]) <= 0)
			$arResult["PATH_TO_HISTORY"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arResult["ALIASES"]["page"]."=history");
		?>
		<div style="text-align:right;"><a href="<?=$arResult["PATH_TO_HISTORY"]?>"><?=GetMessage("BC_ALL_POSTS")?></a></div>
	</div>
	<div id="commented_posts" style="display:none;">
		<?
		$APPLICATION->IncludeComponent("bitrix:blog.commented_posts", ".default", Array(
			"MESSAGE_COUNT"		=> $arParams["MESSAGE_COUNT_MAIN"],
			"MESSAGE_LENGTH"	=>	$arParams["MESSAGE_LENGTH"],
			"PERIOD_DAYS"		=>	$arParams["PERIOD_DAYS"],
			"PATH_TO_BLOG"		=>	$arParams["PATH_TO_BLOG"],
			"PATH_TO_POST"		=>	$arParams["PATH_TO_POST"],
			"PATH_TO_USER"		=>	$arParams["PATH_TO_USER"],
			"PATH_TO_GROUP_BLOG_POST"		=>	$arParams["PATH_TO_GROUP_BLOG_POST"],
			"PATH_TO_SMILE"		=>	$arParams["PATH_TO_SMILE"],
			"CACHE_TYPE"		=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"		=>	$arParams["CACHE_TIME"],
			"BLOG_VAR"			=>	$arParams["VARIABLE_ALIASES"]["blog"],
			"POST_VAR"			=>	$arParams["VARIABLE_ALIASES"]["post_id"],
			"USER_VAR"			=>	$arParams["VARIABLE_ALIASES"]["user_id"],
			"PAGE_VAR"			=>	$arParams["VARIABLE_ALIASES"]["page"],
			"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
			"GROUP_ID" 			=> $arParams["GROUP_ID"],
			),
			$component 
		);
		?>
	</div>
	<div id="popular_posts" style="display:none;">
		<?
		$APPLICATION->IncludeComponent("bitrix:blog.popular_posts", ".default", Array(
			"MESSAGE_COUNT"		=> $arParams["MESSAGE_COUNT_MAIN"],
			"MESSAGE_LENGTH"	=>	$arParams["MESSAGE_LENGTH"],
			"PERIOD_DAYS"		=>	$arParams["PERIOD_DAYS"],
			"PATH_TO_BLOG"		=>	$arParams["PATH_TO_BLOG"],
			"PATH_TO_POST"		=>	$arParams["PATH_TO_POST"],
			"PATH_TO_USER"		=>	$arParams["PATH_TO_USER"],
			"PATH_TO_GROUP_BLOG_POST"		=>	$arParams["PATH_TO_GROUP_BLOG_POST"],
			"PATH_TO_SMILE"		=>	$arParams["PATH_TO_SMILE"],
			"CACHE_TYPE"		=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"		=>	$arParams["CACHE_TIME"],
			"BLOG_VAR"			=>	$arParams["VARIABLE_ALIASES"]["blog"],
			"POST_VAR"			=>	$arParams["VARIABLE_ALIASES"]["post_id"],
			"USER_VAR"			=>	$arParams["VARIABLE_ALIASES"]["user_id"],
			"PAGE_VAR"			=>	$arParams["VARIABLE_ALIASES"]["page"],
			"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
			"GROUP_ID" 			=> $arParams["GROUP_ID"],
			),
			$component 
		);
		?>
	</div>
	</td>
	<td width="50%" valign="top" style="padding-left:10px;">
	<h4><?=GetMessage("BC_NEW_COMMENTS")?></h4>
		<?
		$APPLICATION->IncludeComponent("bitrix:blog.new_comments", ".default", Array(
	"COMMENT_COUNT"		=> $arParams["MESSAGE_COUNT_MAIN"],
	"MESSAGE_LENGTH"	=>	$arParams["MESSAGE_LENGTH"],
	"PATH_TO_BLOG"		=>	$arParams["PATH_TO_BLOG"],
	"PATH_TO_POST"		=>	$arParams["PATH_TO_POST"],
	"PATH_TO_USER"		=>	$arParams["PATH_TO_USER"],
	"PATH_TO_GROUP_BLOG_POST"		=>	$arParams["PATH_TO_GROUP_BLOG_POST"],
	"PATH_TO_SMILE"		=>	$arParams["PATH_TO_SMILE"],
	"CACHE_TYPE"		=>	$arParams["CACHE_TYPE"],
	"CACHE_TIME"		=>	$arParams["CACHE_TIME"],
	"BLOG_VAR"			=>	$arParams["VARIABLE_ALIASES"]["blog"],
	"POST_VAR"			=>	$arParams["VARIABLE_ALIASES"]["post_id"],
	"USER_VAR"			=>	$arParams["VARIABLE_ALIASES"]["user_id"],
	"PAGE_VAR"			=>	$arParams["VARIABLE_ALIASES"]["page"],
	"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
	"GROUP_ID" 			=> $arParams["GROUP_ID"],
	),
	$component 
);
		?>
	<br />	

	<a id="popular_blogs_title" id="popular_blogs_title" class="blog-tab-selected" href="#1" onclick="BXBlogTabShow('popular_blogs', 'blog'); return false;"><?=GetMessage("BC_POPULAR_BLOGS")?></a><a id="new_blogs_title" class="blog-tab" href="#1" onclick="BXBlogTabShow('new_blogs', 'blog'); return false;"><?=GetMessage("BC_NEW_BLOGS")?></a>
	<br /><br />
	<div id="popular_blogs" style="display:block;">
		<?
		$APPLICATION->IncludeComponent(
				"bitrix:blog.popular_blogs", 
				"", 
				Array(
						"BLOG_COUNT"	=> $arParams["BLOG_COUNT_MAIN"],
						"PERIOD_DAYS"	=>	$arParams["PERIOD_DAYS"],
						"BLOG_VAR"		=> $arParams["VARIABLE_ALIASES"]["blog"],
						"USER_VAR"		=> $arParams["VARIABLE_ALIASES"]["user_id"],
						"PAGE_VAR"		=> $arParams["VARIABLE_ALIASES"]["page"],
						"PATH_TO_BLOG"	=> $arParams["PATH_TO_BLOG"],
						"PATH_TO_USER"	=> $arParams["PATH_TO_USER"],
						"PATH_TO_GROUP_BLOG"		=>	$arParams["PATH_TO_GROUP_BLOG"],
						"PATH_TO_GROUP"		=>	$arParams["PATH_TO_GROUP"],
						"CACHE_TYPE"	=> $arParams["CACHE_TYPE"],
						"CACHE_TIME"	=> $arParams["CACHE_TIME"],
						"GROUP_ID" 			=> $arParams["GROUP_ID"],
					),
				$component 
			);
		?>
		<?
		if(IntVal($arParams["GROUP_ID"]) > 0)
		{
			if(strlen($arResult["PATH_TO_GROUP"]) <= 0)
				$arResult["PATH_TO_GROUP"] = htmlspecialchars($APPLICATION->GetCurPage()."?".$arResult["ALIASES"]["page"]."=group&".$arResult["ALIASES"]["group_id"]."=#group_id#");
			?>
			<br />
			<div style="text-align:right;"><a href="<?=CComponentEngine::MakePathFromTemplate($arResult["PATH_TO_GROUP"], array("group_id" => $arParams["GROUP_ID"]))?>"><?=GetMessage("BC_ALL_BLOGS")?></a></div>
			<?
		}
		?>
	</div>

	<div id="new_blogs" style="display:none;">
	<?
		$APPLICATION->IncludeComponent(
				"bitrix:blog.new_blogs", 
				"", 
				Array(
						"BLOG_COUNT"	=> $arParams["BLOG_COUNT_MAIN"],
						"BLOG_VAR"		=> $arParams["VARIABLE_ALIASES"]["blog"],
						"USER_VAR"		=> $arParams["VARIABLE_ALIASES"]["user_id"],
						"PAGE_VAR"		=> $arParams["VARIABLE_ALIASES"]["page"],
						"PATH_TO_BLOG"	=> $arParams["PATH_TO_BLOG"],
						"PATH_TO_GROUP_BLOG"		=>	$arParams["PATH_TO_GROUP_BLOG"],
						"PATH_TO_GROUP"		=>	$arParams["PATH_TO_GROUP"],
						"PATH_TO_USER"	=> $arParams["PATH_TO_USER"],
						"CACHE_TYPE"	=> $arParams["CACHE_TYPE"],
						"CACHE_TIME"	=> $arParams["CACHE_TIME"],
						"GROUP_ID" 			=> $arParams["GROUP_ID"],
					),
				$component 
			);
		?>
	</div>
	<br />
	<?
	$APPLICATION->IncludeComponent(
			"bitrix:blog.rss.link",
			"",
			Array(
					"RSS1"				=> "Y",
					"RSS2"				=> "Y",
					"ATOM"				=> "Y",
					"BLOG_VAR"			=> $arResult["VARIABLE_ALIASES"]["blog"],
					"POST_VAR"			=> $arResult["VARIABLE_ALIASES"]["post_id"],
					"GROUP_VAR"			=> $arResult["VARIABLE_ALIASES"]["group_id"],
					"PATH_TO_RSS_ALL"	=> $arParams["PATH_TO_RSS"],
					"GROUP_ID"			=> $arParams["GROUP_ID"],
					"MODE"				=> "S",
				),
			$component 
		);
		?>
	</td>
</tr>
</table>
<?
if($arParams["SET_TITLE"]=="Y")
	$APPLICATION->SetTitle(GetMessage("BLOG_TITLE"));
?>