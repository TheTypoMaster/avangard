<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent("bitrix:subscribe.edit", "new", Array(
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"SHOW_HIDDEN" => "N",	// �������� ������� ������� ��������
	"ALLOW_ANONYMOUS" => "Y",	// ��������� ��������� ��������
	"SHOW_AUTH_LINKS" => "Y",	// ���������� ������ �� ����������� ��� ��������� ��������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "3600",	// ����� ����������� (���.)
	"CACHE_NOTES" => "",
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>