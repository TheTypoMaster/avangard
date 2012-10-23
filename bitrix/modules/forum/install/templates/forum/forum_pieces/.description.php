<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);

$arForumsList = array();
if (CModule::IncludeModule("forum"))
{
	$db_res = CForumNew::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array());
	while ($ar_res = $db_res->Fetch())
	{
		$arForumsList[$ar_res["ID"]] = $ar_res["NAME"];
	}
}


$arTemplateDescription =
	Array(
		"last_topics.php" =>
			Array(
				"NAME" => GetMessage("TFD_TOPIC_LIST"),
				"ICON" => "/bitrix/images/forum/components/forum_topics.gif",
				"DESCRIPTION" => GetMessage("TFD_TOPIC_LIST_DESCR"),
				"PARAMS" =>
					Array(
						"FID" => Array("NAME"=>GetMessage("TFD_PARAM_FID_NAME"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "VALUES"=>$arForumsList),
						"NUM" => Array("NAME"=>GetMessage("TFD_PARAM_NUM_NAME"), "TYPE"=>"STRING", "MULTIPLE"=>"N", "DEFAULT"=>10, "COLS"=>5),
						"ORDER_BY" => Array("NAME"=>GetMessage("TFD_PARAM_ORDER_BY_NAME"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "VALUES"=>array("TITLE"=>GetMessage("TFD_PARAM_SORT_TOPIC"), "POSTS"=>GetMessage("TFD_PARAM_SORT_POST"), "USER_START_NAME"=>GetMessage("TFD_PARAM_SORT_AUTHOR"), "VIEWS"=>GetMessage("TFD_PARAM_SORT_VIEWS"), "START_DATE"=>GetMessage("TFD_PARAM_SORT_DATE"), "LAST_POST_DATE"=>GetMessage("TFD_PARAM_SORT_POSTDATE")), "ADDITIONAL_VALUES"=>"N"),
						"ORDER_DIRECTION" => Array("NAME"=>GetMessage("TFD_PARAM_ORDER_DIRECTION_NAME"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "VALUES"=>array("ASC"=>GetMessage("TFD_PARAM_SO_ASC"), "DESC"=>GetMessage("TFD_PARAM_SO_DESC")), "ADDITIONAL_VALUES"=>"N"),
						"PATH2MESSAGES" => Array("NAME"=>GetMessage("TFD_PARAM_PATH2MESSAGES_NAME"), "TYPE"=>"STRING", "MULTIPLE"=>"N", "DEFAULT"=>"/forum/read.php", "COLS"=>25),
						"CACHE_TIME" => Array("NAME"=>GetMessage("TFD_PARAM_CACHE_TIME_NAME"), "TYPE"=>"STRING", "MULTIPLE"=>"N", "DEFAULT"=>"600", "COLS"=>25)
						)
				),
		"comment.php" =>
			Array(
				"NAME" => GetMessage("TFD_COMMENT"),
				"DESCRIPTION" => GetMessage("TFD_COMMENT_DESCR"),
				"ICON" => "/bitrix/images/forum/components/forum_form.gif",
				"PARAMS" =>
					Array(
						"INQUERY_FORUM_ID" => Array("NAME"=>GetMessage("TFD_PARAM_FID_NAME"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "VALUES"=>$arForumsList)
						)
				),
		"reviews.php" =>
			Array(
				"NAME" => GetMessage("TFD_REVIEW"),
				"DESCRIPTION" => GetMessage("TFD_REVIEW_DESCR"),
				"ICON" => "/bitrix/images/forum/components/forum_review.gif",
				"PARAMS" =>
					Array(
						"FORUM_ID" => Array("NAME"=>GetMessage("TFD_PARAM_FID_NAME"), "TYPE"=>"LIST", "MULTIPLE"=>"N", "VALUES"=>$arForumsList),
						"PRODUCT_ID" => Array("NAME"=>GetMessage("TFD_PARAM_PRODUCT_ID_NAME"), "TYPE"=>"STRING", "MULTIPLE"=>"N", "DEFAULT"=>"={\$arIBlockElement[\"ID\"]}", "COLS"=>20),
						"CACHE_TIME" => Array("NAME"=>GetMessage("TFD_PARAM_CACHE_TIME_NAME"), "TYPE"=>"STRING", "MULTIPLE"=>"N", "DEFAULT"=>"600", "COLS"=>25)
						)
				)
		);
?>