<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Хиты продаж");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "hitlist",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_MODE" => "html",
		"EDIT_TEMPLATE" => ""
	)
);?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>