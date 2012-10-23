<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бланк претензий");
?> 
<div class="gray_td"><h1>Обратная связь</h1></div> 
<br />
<?$APPLICATION->IncludeComponent("altasib:form.result.new", ".default", array(
	"WEB_FORM_ID" => "10",
	"IGNORE_CUSTOM_TEMPLATE" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => "/pretension/",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"LIST_URL" => "",
	"EDIT_URL" => "",
	"CHAIN_ITEM_TEXT" => "",
	"CHAIN_ITEM_LINK" => "",
	"VARIABLE_ALIASES" => array(
		"WEB_FORM_ID" => "WEB_FORM_ID",
		"RESULT_ID" => "RESULT_ID",
	)
	),
	false
);?>

<br /><br />

<img height="434" width="700" src="/pretension/image/shema.gif" />

<br /><br />

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>