<?
IncludeTemplateLangFile(__FILE__);
if (!CModule::IncludeModule("blog")) return;
$sSectionName = GetMessage("BLOG_DESCR_SECTION");

$arTemplateDescription["main_page/.separator"] = Array(
			"NAME"		 => GetMessage("BLOG_DESCR_SEP_NAME"),
			"DESCRIPTION" => GetMessage("BLOG_DESCR_SEP_DESCR"),
			"SEPARATOR"	 => "Y",
	);


$arTemplateDescription["main_page/search.php"] = array(
	"NAME"			=> GetMessage("BLOG_DESCR_SEARCH_NAME"),
	"DESCRIPTION"	=> GetMessage("BLOG_DESCR_SEARCH_DESCR"),
	"ICON"		 	=> "/bitrix/images/blog/components/search.gif",
	"PARAMS"		=> array(
		"SEARCH_PAGE" => Array(
				"NAME" => GetMessage("BLOG_DESCR_SEARCH_PAGE"),
				"TYPE" => "STRING",
				"DEFAULT" => "search.php",
			),
		"PAGE_RESULT_COUNT"	=>	array(
				"NAME" => GetMessage("BLOG_DESCR_SEARCH_RESULT"),
				"TYPE" => "STRING",
				"DEFAULT" => 20,
			),
		)
	);

$arTemplateDescription["main_page/messages.php"] = array(
	"NAME"			=> GetMessage("BLOG_DESCR_MES_NAME"),
	"DESCRIPTION"	=> GetMessage("BLOG_DESCR_MES_DESCR"),
	"ICON"		 	=> "/bitrix/images/blog/components/messages.gif",
	"PARAMS"		=> array(
		"MESSAGES_COUNT" => Array(
				"NAME" => GetMessage("BLOG_DESCR_MES_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => 6,
			),
		"SORT_BY1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_1"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"DATE_PUBLISH"	=> GetMessage("BLOG_DESCR_SORT_DATE_PUBLISH"),
				"ID"			=> "ID",
				"TITLE"			=> GetMessage("BLOG_DESCR_SORT_MES_TITLE"),
				"BLOG_ID"		=> GetMessage("BLOG_DESCR_SORT_BLOG_ID"),
				"DATE_CREATE"	=> GetMessage("BLOG_DESCR_SORT_MES_CREATE"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "DATE_PUBLISH"
			),
		"SORT_ORDER1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "DESC"
			),
		"SORT_BY2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_2"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"DATE_PUBLISH"	=> GetMessage("BLOG_DESCR_SORT_DATE_PUBLISH"),
				"ID"			=> "ID",
				"TITLE"			=> GetMessage("BLOG_DESCR_SORT_MES_TITLE"),
				"BLOG_ID"		=> GetMessage("BLOG_DESCR_SORT_BLOG_ID"),
				"DATE_CREATE"	=> GetMessage("BLOG_DESCR_SORT_MES_CREATE"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "ID"
			),
		"SORT_ORDER2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "DESC"
			),
		"CACHE_TIME"	=>	array(
				"NAME" => GetMessage("BLOG_DESCR_CACHE_TIME"),
				"TYPE" => "STRING",
				"DEFAULT" => 0,
			),
		)
	);
	
$arTemplateDescription["main_page/new_blogs.php"] = array(
	"NAME"			=> GetMessage("BLOG_DESCR_NB_NAME"),
	"DESCRIPTION"	=> GetMessage("BLOG_DESCR_NB_DESCR"),
	"ICON"		 	=> "/bitrix/images/blog/components/new_blogs.gif",
	"PARAMS"		=> array(
		"BLOGS_COUNT" => Array(
				"NAME" => GetMessage("BLOG_DESCR_NB_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => 6,
			),
		"SHOW_DESCRIPTION" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_NB_S_DESCR"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("Y" => GetMessage("BLOG_DESCR_Y"), "N" => GetMessage("BLOG_DESCR_N")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "Y"
			),
		"SORT_BY1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_1"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"DATE_CREATE"	=> GetMessage("BLOG_DESCR_DATE_CREATE"),
				"ID"			=> "ID",
				"NAME"			=> GetMessage("BLOG_DESCR_BLOG_NAME"),
				"LAST_POST_DATE"	=> GetMessage("BLOG_DESCR_LAST_MES"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "DATE_CREATE"
			),
		"SORT_ORDER1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "DESC"
			),
		"SORT_BY2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_2"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"DATE_CREATE"	=> GetMessage("BLOG_DESCR_DATE_CREATE"),
				"ID"			=> "ID",
				"NAME"			=> GetMessage("BLOG_DESCR_BLOG_NAME"),
				"LAST_POST_DATE"	=> GetMessage("BLOG_DESCR_LAST_MES"),
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "ID"
			),
		"SORT_ORDER2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "DESC"
			),
		"CACHE_TIME"	=>	array(
				"NAME" => GetMessage("BLOG_DESCR_CACHE_TIME"),
				"TYPE" => "STRING",
				"DEFAULT" => "0",
			),
		)
	);
	
$arTemplateDescription["main_page/groups.php"] = array(
	"NAME"			=> GetMessage("BLOG_DESCR_GR_NAME"),
	"DESCRIPTION"	=> GetMessage("BLOG_DESCR_GR_DESCR"),
	"ICON"		 	=> "/bitrix/images/blog/components/groups.gif",
	"PARAMS"		=> array(
		"GROUPS_COUNT" => Array(
				"NAME" => GetMessage("BLOG_DESCR_GR_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => 0,
			),
		"COLS_COUNT" => Array(
				"NAME" =>GetMessage("BLOG_DESCR_GR_COL_COUNT") ,
				"TYPE" => "STRING",
				"DEFAULT" => 3,
			),
		"SORT_BY1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_1"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"NAME"			=> GetMessage("BLOG_DESCR_GR_SORT_NAME"),
				"ID"			=> "ID",
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "NAME"
			),
		"SORT_ORDER1" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "ASC"
			),
		"SORT_BY2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_2"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array(
				"NAME"			=> GetMessage("BLOG_DESCR_GR_SORT_NAME"),
				"ID"			=> "ID",
				),
			"ADDITIONAL_VALUES"	=> "Y",
			"DEFAULT"			=> "ID"
			),
		"SORT_ORDER2" => array(
			"NAME"				=> GetMessage("BLOG_DESCR_SORT_ORDER"),
			"TYPE"				=> "LIST",
			"VALUES"			=> array("ASC" => GetMessage("BLOG_DESCR_SORT_ASC"), "DESC" => GetMessage("BLOG_DESCR_SORT_DESC")),
			"ADDITIONAL_VALUES"	=> "N",
			"DEFAULT"			=> "ASC"
			),
		"CACHE_TIME"	=>	array(
				"NAME" => GetMessage("BLOG_DESCR_CACHE_TIME"),
				"TYPE" => "STRING",
				"DEFAULT" => 0,
			),
		)
	);
?>