<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arYesNo = Array(
	"Y" => GetMessage("SUP_DESC_YES"),
	"N" => GetMessage("SUP_DESC_NO"),

);

$arComponentParameters = array(
	"PARAMETERS" => array(

		"ID" => array(
			"NAME" => GetMessage("SUP_EDIT_DEFAULT_TEMPLATE_PARAM_1_NAME"), 
			"TYPE" => "STRING",
			"PARENT" => "BASE",
			"DEFAULT" => "={\$_REQUEST[\"ID\"]}"
		),

		"TICKET_LIST_URL" => Array(
			"NAME" => GetMessage("SUP_EDIT_DEFAULT_TEMPLATE_PARAM_2_NAME"), 
			"TYPE" => "STRING",
			"COLS" => 45,
			"PARENT" => "URL_TEMPLATES",
			"DEFAULT" => "ticket_list.php"
		),

		"MESSAGES_PER_PAGE" => Array(
			"NAME" => GetMessage("SUP_EDIT_MESSAGES_PER_PAGE"),
			"TYPE" => "STRING",
			"PARENT" => "ADDITIONAL_SETTINGS",
			"MULTIPLE" => "N",
			"DEFAULT" => "20"
		),

		"SET_PAGE_TITLE" => Array(
			"NAME"=>GetMessage("SUP_SET_PAGE_TITLE"), 
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"N", 
			"DEFAULT"=>"Y",
			"PARENT" => "ADDITIONAL_SETTINGS",
			"VALUES"=>$arYesNo, 
			"ADDITIONAL_VALUES"=>"N"
		),
	)
);
?>