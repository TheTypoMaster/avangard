<?
IncludeTemplateLangFile(__FILE__);
if (!CModule::IncludeModule("form")) return;

$arrForms = array();
$rsForm = CForm::GetList($v1, $v2, array("SITE" => array($_REQUEST["site"])), $v3);
while ($arForm = $rsForm->Fetch())
{
	$arrForms[$arForm["ID"]] = "[".$arForm["ID"]."] ".$arForm["NAME"];	
}

$sSectionName = GetMessage("FORM_TEMPLATE_SECTION_NAME");
$arTemplateDescription = 
	array(
		"result_new" =>
			   Array(
				 "NAME"          => GetMessage("FORM_NEW_NAME"),
				 "DESCRIPTION"   => GetMessage("FORM_NEW_DECSRIPTION"),
				 "SEPARATOR"     => "Y",
			   ),

		"result_new/default.php" => array(
			"NAME" => GetMessage("FORM_NEW_DEFAULT_TEMPLATE_NAME"),
			"ICON" => "/bitrix/images/form/components/form_fill.gif",
			"DESCRIPTION" => GetMessage("FORM_NEW_DEFAULT_TEMPLATE_DESCRIPTION"),
			"PARAMS" => array(			
				"WEB_FORM_ID" => array(
					"NAME" => GetMessage("FORM_NEW_DEFAULT_TEMPLATE_PARAM_1_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => $arrForms,
					"DEFAULT" => "={\$_REQUEST[\"WEB_FORM_ID\"]}"
					),
				"LIST_URL" => array(
					"NAME" => GetMessage("FORM_NEW_DEFAULT_TEMPLATE_PARAM_2_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_list.php"
					),
				"EDIT_URL" => array(
					"NAME" => GetMessage("FORM_NEW_DEFAULT_TEMPLATE_PARAM_3_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_edit.php"
					),
				"CHAIN_ITEM_TEXT" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_TEXT"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				"CHAIN_ITEM_LINK" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_LINK"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				)
			),

		"result_list" =>
			   Array(
				 "NAME"          => GetMessage("FORM_LIST_NAME"),
				 "DESCRIPTION"   => GetMessage("FORM_LIST_DECSRIPTION"),
				 "SEPARATOR"     => "Y",
			   ),

		"result_list/default.php" => array(
			"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_NAME"),
			"DESCRIPTION" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_DESCRIPTION"),
			"ICON" => "/bitrix/images/form/components/form_result_list.gif",
			"PARAMS" => array(			
				"WEB_FORM_ID" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_1_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => $arrForms,
					"DEFAULT" => "={\$_REQUEST[\"WEB_FORM_ID\"]}"
					),
				"VIEW_URL" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_3_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_view.php"
					),
				"EDIT_URL" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_4_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_edit.php"
					),
				"NEW_URL" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_5_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_new.php"
					),
				"SHOW_ADDITIONAL" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_6_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "N"
					),
				"SHOW_ANSWER_VALUE" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_7_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "N"
					),
				"SHOW_STATUS" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_8_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "Y"
					),
				"NOT_SHOW_FILTER" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_9_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				"NOT_SHOW_TABLE" => array(
					"NAME" => GetMessage("FORM_LIST_DEFAULT_TEMPLATE_PARAM_10_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					)
				)
			),

		"result_edit" =>
			   Array(
				 "NAME"          => GetMessage("FORM_EDIT_NAME"),
				 "DESCRIPTION"   => GetMessage("FORM_EDIT_DECSRIPTION"),
				 "SEPARATOR"     => "Y",
			   ),

		"result_edit/default.php" => array(
			"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_NAME"),
			"DESCRIPTION" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_DESCRIPTION"),
			"ICON" => "/bitrix/images/form/components/form_result_edit.gif",
			"PARAMS" => array(			
				"RESULT_ID" => array(
					"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_PARAM_1_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "={\$_REQUEST[\"RESULT_ID\"]}"
					),
				"EDIT_ADDITIONAL" => array(
					"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_PARAM_2_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "N"
					),
				"EDIT_STATUS" => array(
					"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_PARAM_3_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "Y"
					),
				"LIST_URL" => array(
					"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_PARAM_4_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_list.php"
					),
				"VIEW_URL" => array(
					"NAME" => GetMessage("FORM_EDIT_DEFAULT_TEMPLATE_PARAM_5_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_view.php"
					),
				"CHAIN_ITEM_TEXT" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_TEXT"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				"CHAIN_ITEM_LINK" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_LINK"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				)
			),

		"result_view" =>
			   Array(
				 "NAME"          => GetMessage("FORM_VIEW_NAME"),
				 "DESCRIPTION"   => GetMessage("FORM_VIEW_DECSRIPTION"),
				 "SEPARATOR"     => "Y",
			   ),

		"result_view/default.php" => array(
			"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_NAME"),
			"DESCRIPTION" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_DESCRIPTION"),
			"ICON" => "/bitrix/images/form/components/form_result_view.gif",
			"PARAMS" => array(			
				"RESULT_ID" => array(
					"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_PARAM_1_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "={\$_REQUEST[\"RESULT_ID\"]}"
					),
				"SHOW_ADDITIONAL" => array(
					"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_PARAM_2_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "N"
					),
				"SHOW_ANSWER_VALUE" => array(
					"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_PARAM_3_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "N"
					),
				"SHOW_STATUS" => array(
					"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_PARAM_4_NAME"), 
					"TYPE" => "LIST",
					"VALUES" => array("Y" => GetMessage("FORM_TEMPLATE_YES"), "N" => GetMessage("FORM_TEMPLATE_NO")),
					"ADDITIONAL_VALUES" => "N",
					"DEFAULT" => "Y"
					),
				"EDIT_URL" => array(
					"NAME" => GetMessage("FORM_VIEW_DEFAULT_TEMPLATE_PARAM_5_NAME"), 
					"TYPE" => "STRING",
					"DEFAULT" => "result_edit.php"
					),
				"CHAIN_ITEM_TEXT" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_TEXT"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				"CHAIN_ITEM_LINK" => array(
					"NAME" => GetMessage("FORM_CHAIN_ITEM_LINK"), 
					"TYPE" => "STRING",
					"DEFAULT" => ""
					),
				)
			)		
		);
?>