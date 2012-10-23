<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("forum"))
	return;
$arTemplateParameters = array(
    "TMPLT_SHOW_TOP" => array(
        "NAME" => GetMessage("F_TMPLT_SHOW_TOP"),
		"TYPE" => "STRING",
		"ROWS" => "5",
		"DEFAULT" => ""),
    "TMPLT_SHOW_BOTTOM" => array(
        "NAME" => GetMessage("F_TMPLT_SHOW_BOTTOM"),
		"TYPE" => "LIST",
		"VALUES" => array("SET_BE_READ" => GetMessage("F_SET_BE_READ")),
		"DEFAULT" => "SET_BE_READ",
		"ADDITIONAL_VALUES" => "Y"),

    "TMPLT_SHOW_MENU" => array(
        "NAME" => GetMessage("F_TMPLT_SHOW_MENU"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"BOTH" => GetMessage("F_TMPLT_SHOW_MENU_BOTH"),
			"TOP" => GetMessage("F_TMPLT_SHOW_MENU_TOP"),
			"BOTTOM" => GetMessage("F_TMPLT_SHOW_MENU_BOTTOM"),
			"NONE" => GetMessage("F_TMPLT_SHOW_MENU_NONE")),
		"DEFAULT" => "TOP"),

    "TMPLT_SHOW_AUTH_FORM" => array(
        "NAME" => GetMessage("F_TMPLT_SHOW_AUTH_FORM"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"LINK" => GetMessage("F_LINK"),
			"INPUT" => GetMessage("F_INPUTS")),
		"DEFAULT" => "INPUT"),

    "TMPLT_SHOW_ADDITIONAL_MARKER" => array(
        "NAME" => GetMessage("F_TMPLT_SHOW_ADDITIONAL_MARKER"),
		"TYPE" => "STRING",
		"DEFAULT" => "(new)"),

	"WORD_WRAP_CUT" => CForumParameters::GetWordWrapCut(false, false),
	"WORD_LENGTH" => CForumParameters::GetWordLength(false, false),
	
    "SMILE_TABLE_COLS" => array(
        "NAME" => GetMessage("F_SMILE_TABLE_COLS"),
		"TYPE" => "STRING",
		"DEFAULT" => "3"),
    
    "SHOW_TAGS" => array(
        "NAME" => GetMessage("F_SHOW_TAGS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"),
	"PATH_TO_SMILE" => Array(
		"NAME" => GetMessage("F_DEFAULT_PATH_TO_SMILE"),
		"TYPE" => "STRING",
		"DEFAULT" => "/bitrix/images/forum/smile/"),
	"PATH_TO_ICON" => Array(
		"NAME" => GetMessage("F_DEFAULT_PATH_TO_ICON"),
		"TYPE" => "STRING",
		"DEFAULT" => "/bitrix/images/forum/icon/"),
	"PAGE_NAVIGATION_TEMPLATE" => Array(
		"NAME" => GetMessage("F_PAGE_NAVIGATION_TEMPLATE"),
		"TYPE" => "STRING",
		"DEFAULT" => ""),
	"HIDE_USER_ACTION" => array(
        "NAME" => GetMessage("F_HIDE_USER_ACTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"),
);
?>