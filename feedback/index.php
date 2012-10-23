<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обратная связь");
?><?$APPLICATION->IncludeComponent("bitrix:form.result.new", "feedback_form2", Array(
	"WEB_FORM_ID"	=>	"5",
	"IGNORE_CUSTOM_TEMPLATE"	=>	"N",
	"SEF_MODE"	=>	"N",
	"SEF_FOLDER"	=>	"",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"LIST_URL"	=>	"/feedback/thanks.php",
	"EDIT_URL"	=>	"",
	"CHAIN_ITEM_TEXT"	=>	"",
	"CHAIN_ITEM_LINK"	=>	"",
	"VARIABLE_ALIASES"	=>	array(
		"WEB_FORM_ID"	=>	"WEB_FORM_ID",
		"RESULT_ID"	=>	"RESULT_ID",
	)
	)
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>