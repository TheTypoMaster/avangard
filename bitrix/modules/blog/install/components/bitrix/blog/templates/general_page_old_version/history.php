<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
$APPLICATION->IncludeComponent(
		"bitrix:blog.new_posts.list", 
		"", 
		Array(
			"MESSAGE_PER_PAGE"		=> $arParams["MESSAGE_COUNT"],
			"PATH_TO_BLOG"		=>	$arParams["PATH_TO_BLOG"],
			"PATH_TO_POST"		=>	$arParams["PATH_TO_POST"],
			"PATH_TO_GROUP_BLOG_POST"		=>	$arParams["PATH_TO_GROUP_BLOG_POST"],
			"PATH_TO_USER"		=>	$arParams["PATH_TO_USER"],
			"PATH_TO_BLOG_CATEGORY"	=> 	$arResult["PATH_TO_BLOG_CATEGORY"],
			"PATH_TO_SMILE"		=>	$arParams["PATH_TO_SMILE"],
			"CACHE_TYPE"		=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"		=>	$arParams["CACHE_TIME"],
			"BLOG_VAR"			=>	$arParams["VARIABLE_ALIASES"]["blog"],
			"POST_VAR"			=>	$arParams["VARIABLE_ALIASES"]["post_id"],
			"USER_VAR"			=>	$arParams["VARIABLE_ALIASES"]["user_id"],
			"PAGE_VAR"			=>	$arParams["VARIABLE_ALIASES"]["page"],
			"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
			"GROUP_ID" 			=> $arParams["GROUP_ID"],
			"SET_TITLE" => $arResult["SET_TITLE"],
		),
		$component 
	);
?>