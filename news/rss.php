<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?><?$APPLICATION->IncludeFile("iblock/rss/rss_out.php", Array(
	"ID"	=>	"1",		// �������������� ����, ������� �������� ��������������
	"NUM_NEWS"	=>	"20",	// ���������� �������� ��� ��������
	"NUM_DAYS"	=>	"30",	// ���������� ���� ��� ��������
	"YANDEX"	=>	"N",	// �������������� � ������� �������
	"CACHE_TIME"	=>	"600",// ����� ����������� ���������� (0 - �� ����������)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>