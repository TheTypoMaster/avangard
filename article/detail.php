<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent("bitrix:news.detail", "template1", Array(
	"DISPLAY_DATE" => "Y",	// �������� ���� ��������
	"DISPLAY_NAME" => "Y",	// �������� �������� ��������
	"DISPLAY_PICTURE" => "Y",	// �������� ��������� �����������
	"DISPLAY_PREVIEW_TEXT" => "Y",	// �������� ����� ������
	"AJAX_MODE" => "N",	// �������� ����� AJAX
	"IBLOCK_TYPE" => "article",	// ��� ��������������� ����� (������������ ������ ��� ��������)
	"IBLOCK_ID" => "27",	// ��� ��������������� �����
	"ELEMENT_ID" => $_REQUEST["ID"],	// ID �������
	"ELEMENT_CODE" => "",	// ��� �������
	"CHECK_DATES" => "Y",	// ���������� ������ �������� �� ������ ������ ��������
	"FIELD_CODE" => "",	// ����
	"PROPERTY_CODE" => "",	// ��������
	"IBLOCK_URL" => "",	// URL �������� ��������� ������ ��������� (�� ��������� - �� �������� ���������)
	"META_KEYWORDS" => "-",	// ���������� �������� ����� �������� �� ��������
	"META_DESCRIPTION" => "-",	// ���������� �������� �������� �� ��������
	"BROWSER_TITLE" => "-",	// ���������� ��������� ���� �������� �� ��������
	"DISPLAY_PANEL" => "N",	// ��������� � �����. ������ ������ ��� ������� ����������
	"SET_TITLE" => "Y",	// ������������� ��������� ��������
	"SET_STATUS_404" => "N",	// ������������� ������ 404, ���� �� ������� ������� ��� ������
	"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	// �������� �������� � ������� ���������
	"ADD_SECTIONS_CHAIN" => "Y",	// �������� ������ � ������� ���������
	"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
	"USE_PERMISSIONS" => "N",	// ������������ �������������� ����������� �������
	"CACHE_TYPE" => "A",	// ��� �����������
	"CACHE_TIME" => "3600",	// ����� ����������� (���.)
	"CACHE_NOTES" => "",
	"CACHE_GROUPS" => "Y",	// ��������� ����� �������
	"DISPLAY_TOP_PAGER" => "N",	// �������� ��� �������
	"DISPLAY_BOTTOM_PAGER" => "Y",	// �������� ��� �������
	"PAGER_TITLE" => "��������",	// �������� ���������
	"PAGER_TEMPLATE" => "",	// �������� �������
	"PAGER_SHOW_ALL" => "Y",	// ���������� ������ "���"
	"AJAX_OPTION_SHADOW" => "Y",	// �������� ���������
	"AJAX_OPTION_JUMP" => "N",	// �������� ��������� � ������ ����������
	"AJAX_OPTION_STYLE" => "Y",	// �������� ��������� ������
	"AJAX_OPTION_HISTORY" => "N",	// �������� �������� ��������� ��������
	"AJAX_OPTION_ADDITIONAL" => "",	// �������������� �������������
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>