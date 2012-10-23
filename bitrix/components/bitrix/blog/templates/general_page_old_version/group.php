<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
	"bitrix:blog.menu",
	"",
	Array(
			"BLOG_VAR"				=> $arParams["VARIABLE_ALIASES"]["blog"],
			"POST_VAR"				=> $arParams["VARIABLE_ALIASES"]["post_id"],
			"USER_VAR"				=> $arParams["VARIABLE_ALIASES"]["user_id"],
			"PAGE_VAR"				=> $arParams["VARIABLE_ALIASES"]["page"],
			"PATH_TO_BLOG"			=> $arParams["PATH_TO_BLOG"],
			"PATH_TO_USER"			=> $arParams["PATH_TO_USER"],
			"PATH_TO_BLOG_EDIT"		=> $arParams["PATH_TO_BLOG_EDIT"],
			"PATH_TO_BLOG_INDEX"	=> $arParams["PATH_TO_BLOG_INDEX"],
			"PATH_TO_DRAFT"			=> $arParams["PATH_TO_DRAFT"],
			"PATH_TO_POST_EDIT"		=> $arParams["PATH_TO_POST_EDIT"],
			"PATH_TO_USER_FRIENDS"	=> $arParams["PATH_TO_USER_FRIENDS"],
			"PATH_TO_USER_SETTINGS"	=> $arParams["PATH_TO_USER_SETTINGS"],
			"PATH_TO_GROUP_EDIT"	=> $arParams["PATH_TO_GROUP_EDIT"],
			"PATH_TO_CATEGORY_EDIT"	=> $arParams["PATH_TO_CATEGORY_EDIT"],
			"PATH_TO_RSS_ALL"		=> $arParams["PATH_TO_RSS_ALL"],
			"SET_NAV_CHAIN"			=> $arParams["SET_NAV_CHAIN"],		
		),
	$component
);

$APPLICATION->IncludeComponent(
		"bitrix:blog.group.blog", 
		"", 
		Array(
				"BLOG_COUNT"				=> $arParams["BLOG_COUNT"],
				"SHOW_BLOG_WITHOUT_POSTS"	=> "N",
				"BLOG_VAR"					=> $arParams["VARIABLE_ALIASES"]["blog"],
				"POST_VAR"					=> $arParams["VARIABLE_ALIASES"]["post_id"],
				"USER_VAR"					=> $arParams["VARIABLE_ALIASES"]["user_id"],
				"PAGE_VAR"					=> $arParams["VARIABLE_ALIASES"]["page"],
				"PATH_TO_BLOG"				=> $arParams["PATH_TO_BLOG"],
				"PATH_TO_POST"				=> $arParams["PATH_TO_POST"],
				"PATH_TO_GROUP_BLOG"				=> $arParams["PATH_TO_GROUP_BLOG"],
				"PATH_TO_GROUP_BLOG_POST"				=> $arParams["PATH_TO_GROUP_BLOG_POST"],
				"PATH_TO_USER"				=> $arParams["PATH_TO_USER"],
				"ID"						=> (IntVal($arParams["GROUP_ID"]) > 0) ? $arParams["GROUP_ID"] : $arResult["VARIABLES"]["group_id"],
				"CACHE_TYPE"				=> $arParams["CACHE_TYPE"],
				"CACHE_TIME"				=> $arParams["CACHE_TIME"],
				"SET_TITLE"					=> $arParams["SET_TITLE"],
				"DATE_TIME_FORMAT"	=> $arParams["DATE_TIME_FORMAT"],
				"NAV_TEMPLATE"	=> $arParams["NAV_TEMPLATE"],
			),
		$component 
	);
$APPLICATION->IncludeComponent(
		"bitrix:blog.rss.link",
		"",
		Array(
				"RSS1"				=> "Y",
				"RSS2"				=> "Y",
				"ATOM"				=> "Y",
				"BLOG_VAR"			=> $arParams["VARIABLE_ALIASES"]["blog"],
				"POST_VAR"			=> $arParams["VARIABLE_ALIASES"]["post_id"],
				"GROUP_VAR"			=> $arParams["VARIABLE_ALIASES"]["group_id"],
				"PATH_TO_RSS"		=> $arParams["PATH_TO_RSS"],
				"PATH_TO_RSS_ALL"	=> $arParams["PATH_TO_RSS_ALL"],
				"BLOG_URL"			=> $arParams["VARIABLES"]["blog"],
				"GROUP_ID"			=> $arParams["VARIABLES"]["group_id"],
				"MODE"				=> "G",
			),
		$component 
	);


?>