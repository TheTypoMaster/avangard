<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("����� � ���������");
?>
<p align="center"><font color="#000000"><strong>
      <br />
    ��������� ���������� ���������� 
      <br />
    </strong><strong>������ ������ ������� &laquo;��������&raquo;!</strong></font></p>

<p align="center"><font color="#000000">�� ����� ����� � 10 ������� �� 31 ����� 2009 �. (</font><font color="#ff0000">�� �������������� �������� ������� �� 30 ������</font><font color="#000000">) ���������� �������� �������:</font></p>

<p align="center"><strong><a href="/mydivan/inter_foto.php" ><font color="#ff0000">������� ������ ������� ��������� � ��������� ������ ����</font></a></strong> 
  <br />
<a href="/mydivan/inter_foto.php" >�������� ����</a></p>

<p align="center"><font color="#000000">���������� ��� ���������� ������ ������ ������� ��������� � ��������� ������ ���� �� ��� ����, �������� �����:</font></p>

<p align="center"></p>

<p><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"",
	Array(
		"SEF_MODE" => "N", 
		"WEB_FORM_ID" => "8", 
		"LIST_URL" => "/mydivan/thanks.php", 
		"EDIT_URL" => "", 
		"SUCCESS_URL" => "", 
		"CHAIN_ITEM_TEXT" => "", 
		"CHAIN_ITEM_LINK" => "", 
		"IGNORE_CUSTOM_TEMPLATE" => "N", 
		"USE_EXTENDED_ERRORS" => "N", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600", 
		"VARIABLE_ALIASES" => Array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID"
		)
	)
);?></p>

<p align="center"><font color="#000000">������ ���������� ����� ��������� �� ����� �����. ��� ����������� ����� ������� ���� ���, ������� � �����.</font></p>

<p align="center"><strong><font color="#ff0000">���������� �������� �������� �����:</font></strong></p>

<p align="center"><strong><font color="#000000">1 ����� - �������� �����������;</font></strong></p>

<p align="center"><strong><font color="#000000">2 ����� - ��������� �������;</font></strong></p>

<p align="center"><strong><font color="#000000">3 ����� - ��3 �����.</font></strong></p>

<p align="center"><strong><font color="#000000">���������� ���� �������� ������� �������!</font></strong></p>

<p align="left"><a href="/mydivan/inter_foto.php" ><font color="#000000">����������, ���������� �� �������</font></a></p>
<blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><blockquote></blockquote><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>