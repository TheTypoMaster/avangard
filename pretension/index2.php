<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форма для предъявления претензий");
?> 
<div class="gray_td">
  <h1>Обратная связь</h1>
</div>
 
<br />
  <?$APPLICATION->IncludeComponent("altasib:form.result.new", ".default", array(
	"WEB_FORM_ID" => "9",
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

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>